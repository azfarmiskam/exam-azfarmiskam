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
            background: #f7fafc;
            min-height: 100vh;
        }

        /* Header */
        .results-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2.5rem 2rem 6rem;
            text-align: center;
            color: white;
        }

        .results-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .results-header .subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Main content pulled up over header */
        .results-body {
            max-width: 720px;
            margin: -4rem auto 2rem;
            padding: 0 1rem;
        }

        /* Score Card */
        .score-section {
            background: white;
            border-radius: 1rem;
            padding: 2.5rem 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-bottom: 1.5rem;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Score Ring */
        .score-ring-wrapper {
            position: relative;
            width: 160px;
            height: 160px;
            margin: 0 auto 1.5rem;
        }

        .score-ring {
            transform: rotate(-90deg);
        }

        .score-ring-bg {
            fill: none;
            stroke: #e2e8f0;
            stroke-width: 10;
        }

        .score-ring-fill {
            fill: none;
            stroke-width: 10;
            stroke-linecap: round;
            stroke-dasharray: 408.4;
            stroke-dashoffset: 408.4;
            transition: stroke-dashoffset 1.5s ease-out;
        }

        .score-ring-fill.pass { stroke: #10b981; }
        .score-ring-fill.fail { stroke: #ef4444; }
        .score-ring-fill.expired { stroke: #f59e0b; }

        .score-ring-value {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .score-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .score-number.pass { color: #10b981; }
        .score-number.fail { color: #ef4444; }
        .score-number.expired { color: #f59e0b; }

        .score-percent {
            font-size: 1rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Pass/Fail Badge */
        .result-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            font-weight: 700;
            font-size: 0.9375rem;
            margin-bottom: 0.5rem;
        }

        .result-badge.pass {
            background: #d1fae5;
            color: #065f46;
        }

        .result-badge.fail {
            background: #fee2e2;
            color: #991b1b;
        }

        .result-badge.expired {
            background: #fef3c7;
            color: #92400e;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.25rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            animation: slideUp 0.5s ease-out;
        }

        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.2s; }

        .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.125rem;
        }

        .stat-value.correct { color: #10b981; }
        .stat-value.wrong { color: #ef4444; }

        .stat-label {
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Info Section */
        .info-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: 1.5rem;
            animation: slideUp 0.5s ease-out;
            animation-delay: 0.3s;
            animation-fill-mode: both;
        }

        .info-card h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.625rem 0;
            border-bottom: 1px solid #f1f5f9;
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

        /* Answer Review */
        .review-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            margin-bottom: 1.5rem;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
            animation-delay: 0.4s;
            animation-fill-mode: both;
        }

        .review-card h3 {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .review-item {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .review-item:last-child {
            border-bottom: none;
        }

        .review-question-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }

        .review-q-number {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
        }

        .review-q-text {
            font-size: 0.9375rem;
            color: #1e293b;
            line-height: 1.5;
        }

        .review-badge {
            flex-shrink: 0;
            padding: 0.25rem 0.625rem;
            border-radius: 1rem;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .review-badge.correct {
            background: #d1fae5;
            color: #065f46;
        }

        .review-badge.wrong {
            background: #fee2e2;
            color: #991b1b;
        }

        .review-badge.skipped {
            background: #f1f5f9;
            color: #64748b;
        }

        .review-answers {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
        }

        .review-answer {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
        }

        .review-answer.student-correct {
            background: #d1fae5;
        }

        .review-answer.student-wrong {
            background: #fee2e2;
        }

        .review-answer.is-correct-answer {
            background: #d1fae5;
        }

        .review-answer-icon {
            flex-shrink: 0;
            font-size: 0.875rem;
        }

        .review-answer-label {
            font-weight: 600;
            color: #64748b;
            min-width: 1.25rem;
        }

        /* Buttons */
        .btn {
            display: block;
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
            text-align: center;
            text-decoration: none;
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
            .results-header {
                padding: 1.75rem 1rem 4.5rem;
            }

            .results-header h1 {
                font-size: 1.25rem;
            }

            .results-header .subtitle {
                font-size: 0.875rem;
            }

            .results-body {
                margin-top: -3.5rem;
                padding: 0 0.75rem;
            }

            .score-section {
                padding: 1.75rem 1.25rem;
                border-radius: 0.75rem;
            }

            .score-ring-wrapper {
                width: 130px;
                height: 130px;
                margin-bottom: 1.25rem;
            }

            .score-ring {
                width: 130px;
                height: 130px;
            }

            .score-number {
                font-size: 2rem;
            }

            .score-percent {
                font-size: 0.8125rem;
            }

            .result-badge {
                font-size: 0.8125rem;
                padding: 0.375rem 1rem;
            }

            .stats-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.625rem;
            }

            .stat-card {
                padding: 1rem 0.5rem;
                border-radius: 0.625rem;
            }

            .stat-icon {
                font-size: 1.25rem;
                margin-bottom: 0.375rem;
            }

            .stat-value {
                font-size: 1.375rem;
            }

            .stat-label {
                font-size: 0.6875rem;
            }

            .info-card {
                padding: 1.25rem;
                border-radius: 0.625rem;
            }

            .info-card h3 {
                font-size: 0.9375rem;
            }

            .info-row {
                padding: 0.5rem 0;
            }

            .info-label, .info-value {
                font-size: 0.8125rem;
            }

            .review-card h3 {
                padding: 1rem 1.25rem;
                font-size: 0.9375rem;
            }

            .review-item {
                padding: 1rem 1.25rem;
            }

            .review-question-header {
                flex-direction: column;
                gap: 0.5rem;
            }

            .review-q-text {
                font-size: 0.875rem;
            }

            .review-answer {
                font-size: 0.75rem;
                padding: 0.375rem 0.625rem;
            }

            .btn {
                padding: 0.875rem;
                font-size: 0.9375rem;
                border-radius: 0.625rem;
            }
        }

        @media (max-width: 380px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .info-row {
                flex-direction: column;
                gap: 0.25rem;
            }
        }
    </style>
</head>
<body>
    @php
        $score = $session->score;
        $isExpired = $session->status === 'expired';
        $isPassed = $score >= 50;
        $statusClass = $isExpired ? 'expired' : ($isPassed ? 'pass' : 'fail');
        $circumference = 2 * 3.14159 * 65;
        $offset = $circumference - ($score / 100) * $circumference;
    @endphp

    <!-- Header -->
    <div class="results-header">
        <h1>Exam Completed!</h1>
        <p class="subtitle">{{ $session->classroom->name }}</p>
    </div>

    <!-- Body -->
    <div class="results-body">
        <!-- Score Section -->
        <div class="score-section">
            <!-- Score Ring -->
            <div class="score-ring-wrapper">
                <svg class="score-ring" width="100%" height="100%" viewBox="0 0 160 160">
                    <circle class="score-ring-bg" cx="80" cy="80" r="65" />
                    <circle class="score-ring-fill {{ $statusClass }}" cx="80" cy="80" r="65"
                        style="stroke-dasharray: {{ $circumference }}; stroke-dashoffset: {{ $circumference }};"
                        data-target="{{ $offset }}" />
                </svg>
                <div class="score-ring-value">
                    <div class="score-number {{ $statusClass }}">{{ number_format($score, 0) }}</div>
                    <div class="score-percent">percent</div>
                </div>
            </div>

            <!-- Pass/Fail Badge -->
            @if($isExpired)
                <div class="result-badge expired">Time Expired</div>
            @elseif($isPassed)
                <div class="result-badge pass">Passed</div>
            @else
                <div class="result-badge fail">Failed</div>
            @endif
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📋</div>
                <div class="stat-value">{{ $session->total_questions }}</div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-value correct">{{ $session->correct_answers }}</div>
                <div class="stat-label">Correct</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">❌</div>
                <div class="stat-value wrong">{{ $session->total_questions - $session->correct_answers }}</div>
                <div class="stat-label">Wrong</div>
            </div>
        </div>

        <!-- Student Info -->
        <div class="info-card">
            <h3>Exam Details</h3>
            <div class="info-row">
                <span class="info-label">Student Name</span>
                <span class="info-value">{{ $session->student->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Matric Number</span>
                <span class="info-value">{{ $session->student->matric_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="info-value">
                    @if($isExpired)
                        Auto-submitted (time expired)
                    @else
                        Submitted
                    @endif
                </span>
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

        <!-- Answer Review -->
        @if($session->classroom->show_correct_answers)
        <div class="review-card">
            <h3>Answer Review ({{ $session->correct_answers }}/{{ $session->total_questions }} correct)</h3>
            @foreach($session->answers as $index => $answer)
                @php
                    $question = $answer->question;
                    $studentAnswer = strtoupper($answer->answer);
                    $correctAnswer = strtoupper($question->correct_answer);
                    $isCorrect = $answer->is_correct;
                @endphp
                <div class="review-item">
                    <div class="review-question-header">
                        <div>
                            <div class="review-q-number">Question {{ $index + 1 }}</div>
                            <div class="review-q-text">{{ $question->question_text }}</div>
                        </div>
                        <span class="review-badge {{ $isCorrect ? 'correct' : 'wrong' }}">
                            {{ $isCorrect ? 'Correct' : 'Wrong' }}
                        </span>
                    </div>
                    <div class="review-answers">
                        @foreach(['A', 'B', 'C', 'D'] as $opt)
                            @php
                                $optionText = $question->{'option_' . strtolower($opt)};
                                $isStudentAnswer = $studentAnswer === $opt;
                                $isCorrectOption = $correctAnswer === $opt;
                                $class = '';
                                $icon = '';

                                if ($isStudentAnswer && $isCorrect) {
                                    $class = 'student-correct';
                                    $icon = '✅';
                                } elseif ($isStudentAnswer && !$isCorrect) {
                                    $class = 'student-wrong';
                                    $icon = '❌';
                                } elseif ($isCorrectOption) {
                                    $class = 'is-correct-answer';
                                    $icon = '✅';
                                }
                            @endphp
                            <div class="review-answer {{ $class }}">
                                @if($icon)
                                    <span class="review-answer-icon">{{ $icon }}</span>
                                @endif
                                <span class="review-answer-label">{{ $opt }}.</span>
                                <span>{{ $optionText }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @endif

        <!-- Back to Home -->
        <a href="{{ route('home') }}" class="btn btn-primary">
            Back to Home
        </a>
    </div>

    <script>
        // Prevent going back to exam
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };

        // Animate score ring on load
        window.addEventListener('DOMContentLoaded', () => {
            const ring = document.querySelector('.score-ring-fill');
            if (ring) {
                const target = ring.getAttribute('data-target');
                setTimeout(() => {
                    ring.style.strokeDashoffset = target;
                }, 200);
            }
        });
    </script>
</body>
</html>
