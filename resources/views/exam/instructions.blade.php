<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Exam Instructions - {{ $classroom->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .instructions-container {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .header p {
            opacity: 0.9;
            font-size: 0.95rem;
        }

        .content {
            padding: 2rem;
        }

        .student-info {
            background: #f7fafc;
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-bottom: 2rem;
            border-left: 4px solid #667eea;
        }

        .student-info h3 {
            font-size: 1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.75rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .info-item {
            font-size: 0.875rem;
        }

        .info-label {
            color: #718096;
            font-weight: 500;
        }

        .info-value {
            color: #2d3748;
            font-weight: 600;
        }

        .exam-details {
            background: #fff5f5;
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-bottom: 2rem;
            border-left: 4px solid #f56565;
        }

        .exam-details h3 {
            font-size: 1rem;
            font-weight: 600;
            color: #742a2a;
            margin-bottom: 0.75rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: #2d3748;
        }

        .detail-item:last-child {
            margin-bottom: 0;
        }

        .instructions-list {
            margin-bottom: 2rem;
        }

        .instructions-list h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .instructions-list ol {
            padding-left: 1.5rem;
        }

        .instructions-list li {
            margin-bottom: 0.75rem;
            color: #4a5568;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .btn {
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        @media (max-width: 640px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="instructions-container">
        <div class="header">
            <img src="{{ $logoUrl }}" alt="EzExam" style="height: 64px; width: auto; margin-bottom: 0.75rem;">
            <h1>{{ $classroom->name }}</h1>
            <p>Please read the instructions carefully before starting</p>
        </div>

        <div class="content">
            <div class="student-info">
                <h3>👤 Your Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $student->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Student ID:</span>
                        <span class="info-value">{{ $student->matric_number }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value">{{ $student->email }}</span>
                    </div>
                    @if($student->group)
                    <div class="info-item">
                        <span class="info-label">Group:</span>
                        <span class="info-value">{{ $student->group->name }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="exam-details">
                <h3>⚠️ Exam Details</h3>
                <div class="detail-item">
                    <span>📋</span>
                    <span><strong>Questions:</strong> {{ $classroom->questions_per_exam }} questions</span>
                </div>
                @if($classroom->timer_minutes)
                <div class="detail-item">
                    <span>⏱️</span>
                    <span><strong>Time Limit:</strong> {{ $classroom->timer_minutes }} minutes</span>
                </div>
                @endif
                <div class="detail-item">
                    <span>🔄</span>
                    <span><strong>Questions Order:</strong> {{ $classroom->shuffle_questions ? 'Randomized' : 'Fixed' }}</span>
                </div>
                <div class="detail-item">
                    <span>📊</span>
                    <span><strong>Results:</strong> {{ $classroom->show_results ? 'Shown immediately' : 'Hidden' }}</span>
                </div>
            </div>

            <div class="instructions-list">
                <h3>📖 Instructions</h3>
                <ol>
                    <li>Once you click "Begin Exam", the timer will start immediately.</li>
                    <li>Answer all questions to the best of your ability.</li>
                    <li>You can navigate between questions using the navigation buttons.</li>
                    <li>Your answers are saved automatically as you select them.</li>
                    @if($classroom->timer_minutes)
                    <li>The exam will auto-submit when time runs out.</li>
                    @endif
                    <li>Make sure you have a stable internet connection.</li>
                    <li>Do not refresh the page or close the browser during the exam.</li>
                    <li>Click "Submit Exam" when you're done answering all questions.</li>
                </ol>
            </div>

            <div class="actions">
                <a href="/" class="btn btn-secondary">Cancel</a>
                <form action="{{ route('exam.start', $classroom->code) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Begin Exam →
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
