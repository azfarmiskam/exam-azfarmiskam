<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Results - EzExam</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .results-container {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .score-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .score-value {
            font-size: 4rem;
            font-weight: 700;
            color: white;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .score-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.125rem;
            font-weight: 500;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-section {
            background: #f8fafc;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
            font-size: 0.875rem;
        }

        .info-value {
            color: #1e293b;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 12px;
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
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        @media (max-width: 640px) {
            .results-container {
                padding: 2rem 1.5rem;
            }

            h1 {
                font-size: 1.5rem;
            }

            .score-value {
                font-size: 3rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="results-container">
        <div class="success-icon">✓</div>
        
        <h1>Exam Completed!</h1>
        <p class="subtitle">{{ $session->classroom->name }}</p>

        <div class="score-card">
            <div class="score-value">{{ number_format($session->score, 0) }}%</div>
            <div class="score-label">Your Score</div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $session->total_questions }}</div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #10b981;">{{ $session->correct_answers }}</div>
                <div class="stat-label">Correct</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color: #ef4444;">{{ $session->total_questions - $session->correct_answers }}</div>
                <div class="stat-label">Wrong</div>
            </div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Student Name</span>
                <span class="info-value">{{ $session->student->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Matric Number</span>
                <span class="info-value">{{ $session->student->matric_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Completed At</span>
                <span class="info-value">{{ $session->completed_at->format('M d, Y h:i A') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Time Taken</span>
                <span class="info-value">{{ $session->started_at->diffForHumans($session->completed_at, true) }}</span>
            </div>
        </div>

        @if($session->classroom->show_correct_answers)
        <a href="{{ route('exam.review', ['code' => $session->classroom->code, 'session' => $session->id]) }}" class="btn btn-primary" style="display: block; text-align: center; text-decoration: none; margin-bottom: 1rem;">
            Review Answers
        </a>
        @endif

        <a href="{{ route('home') }}" class="btn btn-primary" style="display: block; text-align: center; text-decoration: none;">
            Back to Home
        </a>
    </div>

    <script>
        // Prevent going back to exam
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
</body>
</html>
