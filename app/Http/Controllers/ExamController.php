<?php

namespace App\Http\Controllers;

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
        $questions = $classroom->questions()
            ->inRandomOrder()
            ->limit($classroom->questions_per_exam)
            ->get();

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
        if ($classroom->shuffle_questions) {
            shuffle($questionOrder);
        }
        
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

            // Verify session belongs to current student (flexible check)
            $currentStudentId = session('student_id');
            if ($currentStudentId && $session->student_id !== $currentStudentId) {
                return response()->json(['error' => 'Unauthorized'], 403);
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
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
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
            $isCorrect = $answer->answer === $question->correct_answer;
            
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

        // Clear session data
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

        // Check if exam is completed
        if (!$session->completed_at) {
            return redirect()->route('exam.take', ['code' => $code, 'session' => $sessionId]);
        }

        return view('exam.results', compact('session'));
    }
}
