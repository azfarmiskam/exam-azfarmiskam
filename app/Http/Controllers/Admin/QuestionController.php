<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with('category');
        
        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Search
        if ($request->has('search')) {
            $query->where('question_text', 'like', '%' . $request->search . '%');
        }
        
        $questions = $query->latest()->get();
        
        return response()->json([
            'questions' => $questions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'question_text' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:a,b,c,d',
            'difficulty' => 'nullable|integer|min:1|max:5',
            'shuffle_answers' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('questions', 'public');
        }

        $validated['created_by'] = auth()->id();

        $question = Question::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Question created successfully',
            'question' => $question->load('category')
        ], 201);
    }

    public function show(Question $question)
    {
        $question->load('category');
        
        return response()->json([
            'question' => $question
        ]);
    }

    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'question_text' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:a,b,c,d',
            'difficulty' => 'nullable|integer|min:1|max:5',
            'shuffle_answers' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($question->image_path) {
                Storage::disk('public')->delete($question->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('questions', 'public');
        }

        $question->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Question updated successfully',
            'question' => $question->load('category')
        ]);
    }

    /**
     * Import questions from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return response()->json(['message' => 'Failed to read file'], 400);
        }

        // Read header row
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            return response()->json(['message' => 'File is empty'], 400);
        }

        // Normalize headers (lowercase, trim)
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $requiredColumns = ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer'];
        $missing = array_diff($requiredColumns, $header);
        if (!empty($missing)) {
            fclose($handle);
            return response()->json([
                'message' => 'Missing required columns: ' . implode(', ', $missing)
            ], 400);
        }

        $imported = 0;
        $errors = [];
        $row = 1;

        while (($data = fgetcsv($handle)) !== false) {
            $row++;

            if (count($data) < count($header)) {
                $errors[] = "Row {$row}: not enough columns";
                continue;
            }

            $rowData = array_combine($header, $data);

            // Validate correct_answer
            $correctAnswer = strtolower(trim($rowData['correct_answer']));
            if (!in_array($correctAnswer, ['a', 'b', 'c', 'd'])) {
                $errors[] = "Row {$row}: invalid correct_answer '{$rowData['correct_answer']}' (must be a, b, c, or d)";
                continue;
            }

            // Handle category
            $categoryId = null;
            if (!empty($rowData['category'] ?? '')) {
                $category = Category::firstOrCreate(
                    ['name' => trim($rowData['category'])],
                    ['description' => null]
                );
                $categoryId = $category->id;
            }

            // Skip if question_text is empty
            if (empty(trim($rowData['question_text']))) {
                $errors[] = "Row {$row}: question_text is empty";
                continue;
            }

            // Handle difficulty (1-5, default 3)
            $difficulty = isset($rowData['difficulty']) ? (int) $rowData['difficulty'] : 3;
            $difficulty = max(1, min(5, $difficulty));

            Question::create([
                'question_text' => trim($rowData['question_text']),
                'option_a' => trim($rowData['option_a']),
                'option_b' => trim($rowData['option_b']),
                'option_c' => trim($rowData['option_c']),
                'option_d' => trim($rowData['option_d']),
                'correct_answer' => $correctAnswer,
                'difficulty' => $difficulty,
                'category_id' => $categoryId,
                'shuffle_answers' => filter_var($rowData['shuffle_answers'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'created_by' => auth()->id(),
            ]);

            $imported++;
        }

        fclose($handle);

        return response()->json([
            'success' => true,
            'message' => "{$imported} questions imported successfully" . (count($errors) ? ", " . count($errors) . " rows skipped" : ""),
            'imported' => $imported,
            'errors' => $errors,
        ]);
    }

    /**
     * Export all questions as CSV
     */
    public function export()
    {
        $questions = Question::with('category')->orderBy('id')->get();

        $csvHeader = ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'difficulty', 'category', 'shuffle_answers'];

        $callback = function () use ($questions, $csvHeader) {
            $handle = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $csvHeader);

            foreach ($questions as $q) {
                fputcsv($handle, [
                    $q->question_text,
                    $q->option_a,
                    $q->option_b,
                    $q->option_c,
                    $q->option_d,
                    $q->correct_answer,
                    $q->difficulty ?? 3,
                    $q->category->name ?? '',
                    $q->shuffle_answers ? 'true' : 'false',
                ]);
            }

            fclose($handle);
        };

        $filename = 'ezexam-questions-' . date('Y-m-d') . '.csv';

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Download CSV template for import
     */
    public function template()
    {
        $csvHeader = ['question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'difficulty', 'category', 'shuffle_answers'];
        $sampleRow = ['What is 2 + 2?', '3', '4', '5', '6', 'b', '1', 'Mathematics', 'false'];

        $callback = function () use ($csvHeader, $sampleRow) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $csvHeader);
            fputcsv($handle, $sampleRow);
            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ezexam-import-template.csv"',
        ]);
    }

    public function destroy(Question $question)
    {
        // Delete image if exists
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully'
        ]);
    }
}
