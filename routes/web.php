<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Homepage - Student Exam Code Entry
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/captcha/refresh', [LoginController::class, 'refreshCaptchaAjax'])->name('captcha.refresh');

// Admin Dashboard (placeholder - will be protected by auth middleware)
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard')->middleware('auth');

// Admin Classroom Preview (for testing exam interface)
Route::get('/admin/classrooms/{classroom}/preview', [\App\Http\Controllers\Admin\ClassroomController::class, 'preview'])
    ->name('admin.classrooms.preview')
    ->middleware('auth');

// Admin API Routes
Route::middleware(['auth', 'no-cache'])->prefix('admin/api')->name('admin.api.')->group(function () {
    // Classroom Management
    Route::apiResource('classrooms', \App\Http\Controllers\Admin\ClassroomController::class);
    Route::post('classrooms/{classroom}/toggle-status', [\App\Http\Controllers\Admin\ClassroomController::class, 'toggleStatus'])
        ->name('classrooms.toggle-status');
    
    // Classroom Groups
    Route::get('classrooms/{classroom}/groups', [\App\Http\Controllers\Admin\ClassroomGroupController::class, 'index'])
        ->name('classrooms.groups.index');
    Route::post('classrooms/{classroom}/groups', [\App\Http\Controllers\Admin\ClassroomGroupController::class, 'store'])
        ->name('classrooms.groups.store');
    Route::put('classrooms/{classroom}/groups/{group}', [\App\Http\Controllers\Admin\ClassroomGroupController::class, 'update'])
        ->name('classrooms.groups.update');
    Route::delete('classrooms/{classroom}/groups/{group}', [\App\Http\Controllers\Admin\ClassroomGroupController::class, 'destroy'])
        ->name('classrooms.groups.destroy');
    
    // Classroom Questions
    Route::get('classrooms/{classroom}/questions', function(\App\Models\Classroom $classroom) {
        $questions = $classroom->questions()->with('category')->get();
        return response()->json(['questions' => $questions]);
    })->name('classrooms.questions.index');
    
    Route::post('classrooms/{classroom}/questions', function(\Illuminate\Http\Request $request, \App\Models\Classroom $classroom) {
        $validated = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id'
        ]);
        
        $classroom->questions()->syncWithoutDetaching($validated['question_ids']);
        
        return response()->json([
            'success' => true,
            'message' => 'Questions added successfully'
        ]);
    })->name('classrooms.questions.store');
    
    Route::delete('classrooms/{classroom}/questions/{question}', function(\App\Models\Classroom $classroom, \App\Models\Question $question) {
        $classroom->questions()->detach($question->id);
        
        return response()->json([
            'success' => true,
            'message' => 'Question removed successfully'
        ]);
    })->name('classrooms.questions.destroy');
    
    // Categories
    Route::apiResource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    
    // Questions
    Route::apiResource('questions', \App\Http\Controllers\Admin\QuestionController::class);
    
    // Students
    Route::apiResource('students', \App\Http\Controllers\Admin\StudentController::class);
    
    // Exam Sessions (Results) - specific routes BEFORE parameterized routes
    Route::get('exam-sessions/statistics', [\App\Http\Controllers\Admin\ExamSessionController::class, 'statistics'])->name('exam-sessions.statistics');
    Route::get('exam-sessions', [\App\Http\Controllers\Admin\ExamSessionController::class, 'index'])->name('exam-sessions.index');
    Route::get('exam-sessions/{id}', [\App\Http\Controllers\Admin\ExamSessionController::class, 'show'])->name('exam-sessions.show');
    
    // Admin Users
    Route::apiResource('users', \App\Http\Controllers\Admin\UserController::class);
});


// Exam Code Verification
Route::post('/exam/verify', function () {
    $code = strtoupper(request('code'));
    
    // Check if classroom exists with this code
    $classroom = \App\Models\Classroom::where('code', $code)
        ->where('is_active', true)
        ->first();
    
    if (!$classroom) {
        return redirect()->back()->with('error', 'Invalid exam code. Please check and try again.');
    }
    
    // Redirect to student registration for this classroom
    return redirect()->route('exam.register', ['code' => $code]);
})->name('exam.verify');

// Student Registration
Route::get('/exam/{code}/register', function ($code) {
    $classroom = \App\Models\Classroom::where('code', $code)
        ->where('is_active', true)
        ->with('groups')
        ->firstOrFail();
    
    return view('exam.register', compact('classroom'));
})->name('exam.register');

Route::post('/exam/{code}/register', [\App\Http\Controllers\ExamController::class, 'registerSubmit'])->name('exam.register.submit');

// Exam Instructions
Route::get('/exam/{code}/instructions', [\App\Http\Controllers\ExamController::class, 'instructions'])->name('exam.instructions');

// Start Exam
Route::post('/exam/{code}/start', [\App\Http\Controllers\ExamController::class, 'start'])->name('exam.start');

// Take Exam
Route::get('/exam/{code}/take/{session}', function ($code, $session) {
    $examSession = \App\Models\ExamSession::with('classroom')->findOrFail($session);
    
    // Check if exam is already completed
    if ($examSession->completed_at) {
        return redirect()->route('exam.results', [
            'code' => $code,
            'session' => $session
        ]);
    }
    
    $classroom = $examSession->classroom;
    
    $response = response()->view('exam.take', [
        'code' => $code,
        'session' => $session,
        'classroom' => $classroom,
        'isPreview' => false
    ]);
    
    // Prevent caching
    $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    $response->headers->set('Pragma', 'no-cache');
    $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    
    return $response;
})->name('exam.take');

// Exam API Routes
Route::get('/exam/{code}/session/{session}/data', [\App\Http\Controllers\ExamController::class, 'getSessionData'])->name('exam.session.data');
Route::post('/exam/{code}/session/{session}/answer', [\App\Http\Controllers\ExamController::class, 'saveAnswer'])->name('exam.session.answer');
Route::post('/exam/{code}/session/{session}/submit', [\App\Http\Controllers\ExamController::class, 'submit'])->name('exam.session.submit');

// Exam Results
Route::get('/exam/{code}/session/{session}/results', [\App\Http\Controllers\ExamController::class, 'results'])->name('exam.results');
