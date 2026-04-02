<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    // Register student for exam
    public function registerSubmit(Request $request, $code)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'group_id' => 'nullable|exists:classroom_groups,id',
            'name' => 'required|string|max:255',
            'matric_number' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        // Check if classroom is active
        $classroom = Classroom::where('id', $validated['classroom_id'])
            ->where('is_active', true)
            ->firstOrFail();

        // Create or update student
        $student = Student::updateOrCreate(
            [
                'email' => $validated['email'],
                'classroom_id' => $classroom->id
            ],
            [
                'group_id' => $validated['group_id'] ?? null,
                'name' => $validated['name'],
                'matric_number' => $validated['matric_number'],
                'phone' => $validated['phone'] ?? null,
            ]
        );

        // Store student ID in session
        session(['student_id' => $student->id]);
        session(['classroom_id' => $classroom->id]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'redirect' => route('exam.instructions', ['code' => $code])
        ]);
    }

    // Show exam instructions
    public function instructions($code)
    {
        $classroom = Classroom::where('code', $code)
            ->where('is_active', true)
            ->with('groups')
            ->firstOrFail();

        $studentId = session('student_id');
        if (!$studentId) {
            return redirect()->route('exam.register', ['code' => $code]);
        }

        $student = Student::findOrFail($studentId);

        return view('exam.instructions', compact('classroom', 'student'));
    }

    // Start exam
    public function start(Request $request, $code)
    {
        $classroom = Classroom::where('code', $code)
            ->where('is_active', true)
            ->firstOrFail();

        $studentId = session('student_id');
        if (!$studentId) {
            return redirect()->route('exam.register', ['code' => $code]);
        }

        // Check if student already has an active session
        $existingSession = ExamSession::where('student_id', $studentId)
            ->where('classroom_id', $classroom->id)
            ->whereNull('completed_at')
            ->first();

        if ($existingSession) {
            return redirect()->route('exam.take', [
                'code' => $code,
                'session' => $existingSession->id
            ]);
        }

        // Get questions for this classroom
        $query = $classroom->questions();
        
        if ($classroom->shuffle_questions) {
            $query->inRandomOrder();
        } else {
            // Default order (e.g., by assignment time)
            $query->orderBy('classroom_questions.created_at');
        }

        $questions = $query->limit($classroom->questions_per_exam)->get();

        if ($questions->count() < $classroom->questions_per_exam) {
            return back()->with('error', 'Not enough questions available for this exam.');
        }

        // Create exam session
        $session = ExamSession::create([
            'student_id' => $studentId,
            'classroom_id' => $classroom->id,
            'started_at' => now(),
            'expires_at' => $classroom->timer_minutes ? now()->addMinutes($classroom->timer_minutes) : null,
        ]);

        // Store question order
        $questionOrder = $questions->pluck('id')->toArray();
        // No need to shuffle again as DB handling or fixed order is desired
        
        session(['question_order_' . $session->id => $questionOrder]);

        return redirect()->route('exam.take', [
            'code' => $code,
            'session' => $session->id
        ]);
    }

    // Get exam session data (for loading questions)
    public function getSessionData($code, $sessionId)
    {
        try {
            $session = ExamSession::with(['classroom', 'answers'])
                ->findOrFail($sessionId);

            // Verify session belongs to current student
            $currentStudentId = session('student_id');
            if (!$currentStudentId || $session->student_id !== $currentStudentId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // If session has expired server-side, auto-complete it
            if (!$session->completed_at && $session->expires_at && now()->isAfter($session->expires_at)) {
                $this->forceCompleteSession($session);

                return response()->json([
                    'expired' => true,
                    'redirect' => route('exam.results', ['code' => $code, 'session' => $sessionId]),
                ]);
            }

            // Get question order from session
            $questionOrder = session('question_order_' . $sessionId, []);
            
            // If no question order in session, get from database
            if (empty($questionOrder)) {
                $questionOrder = $session->classroom->questions()
                    ->pluck('questions.id')
                    ->toArray();
            }
            
            // Load questions in the correct order
            $questions = $session->classroom->questions()
                ->with('category')
                ->whereIn('questions.id', $questionOrder)
                ->get()
                ->sortBy(function($question) use ($questionOrder) {
                    return array_search($question->id, $questionOrder);
                })
                ->values();

            // Get existing answers
            $answers = [];
            foreach ($session->answers as $answer) {
                $answers[$answer->question_id] = $answer->answer;
            }

            return response()->json([
                'questions' => $questions,
                'answers' => $answers,
                'timer_minutes' => $session->classroom->timer_minutes,
                'expires_at' => $session->expires_at ? $session->expires_at->toIso8601String() : null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading exam session data: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());

            return response()->json([
                'error' => 'Failed to load exam data',
            ], 500);
        }
    }

    // Save student answer
    public function saveAnswer(Request $request, $code, $sessionId)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer' => 'required|in:A,B,C,D',
        ]);

        $session = ExamSession::findOrFail($sessionId);

        // Verify session belongs to current student
        if ($session->student_id !== session('student_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if session is still active
        if ($session->completed_at) {
            return response()->json(['error' => 'Exam already submitted'], 400);
        }

        // Check if expired
        if ($session->expires_at && now()->isAfter($session->expires_at)) {
            return response()->json(['error' => 'Exam time expired'], 400);
        }

        // Save or update answer
        $session->answers()->updateOrCreate(
            ['question_id' => $validated['question_id']],
            ['answer' => $validated['answer']]
        );

        return response()->json([
            'success' => true,
            'message' => 'Answer saved'
        ]);
    }

    // Submit exam
    public function submit(Request $request, $code, $sessionId)
    {
        $session = ExamSession::with(['answers.question', 'classroom'])
            ->findOrFail($sessionId);

        // Verify session belongs to current student
        if ($session->student_id !== session('student_id')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if already submitted
        if ($session->completed_at) {
            return response()->json(['error' => 'Exam already submitted'], 400);
        }

        // Calculate score
        $correctAnswers = 0;
        $totalQuestions = $session->answers->count();

        foreach ($session->answers as $answer) {
            $question = $answer->question;
            
            // Case-insensitive comparison (A = a, B = b, etc.)
            $isCorrect = strtoupper($answer->answer) === strtoupper($question->correct_answer);
            
            // Update answer with correctness
            $answer->update(['is_correct' => $isCorrect]);
            
            if ($isCorrect) {
                $correctAnswers++;
            }
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

        // Update session
        $session->update([
            'completed_at' => now(),
            'score' => $score,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'status' => 'completed'
        ]);

        // Grant results access before clearing student session
        session(['results_verified_' . $sessionId => true]);

        // Clear exam session data
        session()->forget(['student_id', 'classroom_id', 'question_order_' . $sessionId]);

        return response()->json([
            'success' => true,
            'message' => 'Exam submitted successfully',
            'redirect' => route('exam.results', ['code' => $code, 'session' => $sessionId])
        ]);
    }

    // Show exam results
    public function results($code, $sessionId)
    {
        $session = ExamSession::with(['classroom', 'student', 'answers.question'])
            ->findOrFail($sessionId);

        // If expired but not completed, force complete it now
        if (!$session->completed_at && $session->expires_at && now()->isAfter($session->expires_at)) {
            $this->forceCompleteSession($session);
            $session->refresh();
        }

        // Check if exam is completed
        if (!$session->completed_at) {
            return redirect()->route('exam.take', ['code' => $code, 'session' => $sessionId]);
        }

        // Check if student is verified to view this result
        // Allow access if: student just submitted (student_id matches), or verified via session, or admin is logged in
        $verifiedKey = 'results_verified_' . $sessionId;
        $isOwner = session('student_id') === $session->student_id;
        $isVerified = session($verifiedKey) === true;
        $isAdmin = auth()->check();

        if (!$isOwner && !$isVerified && !$isAdmin) {
            return redirect()->route('exam.results.verify', ['code' => $code, 'session' => $sessionId]);
        }

        return view('exam.results', compact('session'));
    }

    // Show results verification form
    public function showResultsVerify($code, $sessionId)
    {
        $session = ExamSession::with('classroom')->findOrFail($sessionId);

        if (!$session->completed_at) {
            abort(404);
        }

        return view('exam.results-verify', [
            'code' => $code,
            'session' => $session,
        ]);
    }

    // Verify student identity for results
    public function verifyResults(Request $request, $code, $sessionId)
    {
        $request->validate([
            'matric_number' => 'required|string',
            'email' => 'required|email',
        ]);

        $session = ExamSession::with('student')->findOrFail($sessionId);

        if (!$session->completed_at) {
            abort(404);
        }

        $student = $session->student;

        if (
            strtolower(trim($request->matric_number)) === strtolower(trim($student->matric_number)) &&
            strtolower(trim($request->email)) === strtolower(trim($student->email))
        ) {
            session(['results_verified_' . $sessionId => true]);
            return redirect()->route('exam.results', ['code' => $code, 'session' => $sessionId]);
        }

        return back()->withErrors([
            'identity' => 'The matric number and email do not match our records for this exam session.'
        ])->withInput();
    }

    // Get active announcements for a classroom (student polling)
    public function announcements($code)
    {
        $classroom = Classroom::where('code', $code)->firstOrFail();

        $announcements = Announcement::where('classroom_id', $classroom->id)
            ->active()
            ->latest()
            ->get(['id', 'message', 'expires_at']);

        return response()->json(['announcements' => $announcements]);
    }

    // Force-complete an expired session with score calculation
    private function forceCompleteSession(ExamSession $session)
    {
        $session->load('answers.question');

        $correctAnswers = 0;
        $totalQuestions = $session->answers->count();

        foreach ($session->answers as $answer) {
            $isCorrect = strtoupper($answer->answer) === strtoupper($answer->question->correct_answer);
            $answer->update(['is_correct' => $isCorrect]);
            if ($isCorrect) $correctAnswers++;
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

        $session->update([
            'completed_at' => $session->expires_at,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'status' => 'expired',
        ]);

        session(['results_verified_' . $session->id => true]);
        session()->forget(['student_id', 'classroom_id', 'question_order_' . $session->id]);
    }
}
