<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    /**
     * Display a listing of classrooms.
     */
    public function index()
    {
        $classrooms = Classroom::withCount(['students', 'questions'])
            ->latest()
            ->get();
        
        return response()->json([
            'classrooms' => $classrooms
        ]);
    }

    /**
     * Store a newly created classroom.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions_per_exam' => 'required|integer|min:1',
            'timer_minutes' => 'nullable|integer|min:1',
            'show_results_immediately' => 'boolean',
            'show_correct_answers' => 'boolean',
            'allow_review' => 'boolean',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Generate unique code
        do {
            $code = strtoupper(Str::random(6));
        } while (Classroom::where('code', $code)->exists());

        $classroom = Classroom::create([
            ...$validated,
            'code' => $code,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Classroom created successfully',
            'classroom' => $classroom
        ], 201);
    }

    /**
     * Display the specified classroom.
     */
    public function show(Classroom $classroom)
    {
        $classroom->load(['groups', 'questions', 'students']);
        
        return response()->json([
            'classroom' => $classroom
        ]);
    }

    /**
     * Update the specified classroom.
     */
    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions_per_exam' => 'required|integer|min:1',
            'timer_minutes' => 'nullable|integer|min:1',
            'show_results_immediately' => 'boolean',
            'show_correct_answers' => 'boolean',
            'allow_review' => 'boolean',
            'instructions' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $classroom->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Classroom updated successfully',
            'classroom' => $classroom
        ]);
    }

    /**
     * Remove the specified classroom.
     */
    public function destroy(Classroom $classroom)
    {
        $classroom->delete();

        return response()->json([
            'success' => true,
            'message' => 'Classroom deleted successfully'
        ]);
    }

    /**
     * Toggle classroom active status.
     */
    public function toggleStatus(Classroom $classroom)
    {
        $classroom->update([
            'is_active' => !$classroom->is_active
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Classroom status updated',
            'is_active' => $classroom->is_active
        ]);
    }

    /**
     * Preview exam interface (for admin testing).
     */
    public function preview(Classroom $classroom)
    {
        // Get sample questions
        $questions = $classroom->questions()
            ->with('category')
            ->limit($classroom->questions_per_exam)
            ->get();

        // Check if enough questions are assigned
        if ($questions->count() < $classroom->questions_per_exam) {
            return redirect()->route('admin.dashboard')
                ->with('error', "Not enough questions assigned to '{$classroom->name}'. Please assign at least {$classroom->questions_per_exam} questions before previewing.");
        }

        // Shuffle if needed
        if ($classroom->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        // Create mock session data
        $sessionData = [
            'id' => 0,
            'questions' => $questions->toArray(),
            'timer_minutes' => $classroom->timer_minutes,
            'expires_at' => $classroom->timer_minutes ? now()->addMinutes($classroom->timer_minutes)->toIso8601String() : null,
            'answers' => []
        ];

        return view('exam.take', [
            'code' => $classroom->code,
            'session' => 0,
            'classroom' => $classroom,
            'isPreview' => true,
            'previewData' => json_encode($sessionData)
        ]);
    }
}
