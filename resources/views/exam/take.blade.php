<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Taking Exam - EzExam</title>
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
        .exam-header {
            background: white;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .exam-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #2d3748;
        }

        .timer {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #fff5f5;
            border-radius: 0.5rem;
            border: 2px solid #feb2b2;
        }

        .timer.warning {
            background: #fffaf0;
            border-color: #fbd38d;
            animation: pulse 1.5s infinite;
        }

        .timer.danger {
            background: #fff5f5;
            border-color: #fc8181;
            animation: pulse-danger 0.75s infinite;
        }

        .timer.danger .timer-text {
            color: #e53e3e;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        @keyframes pulse-danger {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.03); }
        }

        /* Timer Warning Banner */
        .timer-banner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            padding: 0.75rem 1.5rem;
            text-align: center;
            font-weight: 600;
            font-size: 0.9375rem;
            z-index: 200;
            transform: translateY(-100%);
            transition: transform 0.4s ease;
            font-family: 'Inter', sans-serif;
        }

        .timer-banner.visible {
            transform: translateY(0);
        }

        .timer-banner.warning-banner {
            background: linear-gradient(135deg, #f6e05e 0%, #ecc94b 100%);
            color: #744210;
        }

        .timer-banner.danger-banner {
            background: linear-gradient(135deg, #fc8181 0%, #f56565 100%);
            color: white;
        }

        /* Loading skeleton */
        .skeleton {
            background: linear-gradient(90deg, #e2e8f0 25%, #edf2f7 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 0.5rem;
        }

        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .skeleton-text {
            height: 1rem;
            margin-bottom: 0.75rem;
        }

        .skeleton-text.short { width: 40%; }
        .skeleton-text.medium { width: 70%; }
        .skeleton-text.long { width: 100%; }

        .skeleton-option {
            height: 3.5rem;
            margin-bottom: 1rem;
            border-radius: 0.75rem;
        }

        /* Loading overlay for submission */
        .submit-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(4px);
            z-index: 10000;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 1.5rem;
        }

        .submit-overlay.visible {
            display: flex;
        }

        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #e2e8f0;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .submit-overlay-text {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2d3748;
        }

        /* Save indicator */
        .save-indicator {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.8125rem;
            font-weight: 600;
            z-index: 150;
            opacity: 0;
            transition: opacity 0.3s ease;
            font-family: 'Inter', sans-serif;
        }

        .save-indicator.saving {
            background: #ebf8ff;
            color: #2b6cb0;
            opacity: 1;
        }

        .save-indicator.saved {
            background: #f0fff4;
            color: #276749;
            opacity: 1;
        }

        .timer-icon {
            font-size: 1.25rem;
        }

        .timer-text {
            font-size: 1.125rem;
            font-weight: 700;
            color: #742a2a;
        }

        /* Main Container */
        .exam-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 1.5rem;
        }

        /* Question Area */
        .question-area {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .question-header {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .question-number {
            font-size: 0.875rem;
            color: #718096;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .question-text {
            font-size: 1.125rem;
            font-weight: 600;
            color: #2d3748;
            line-height: 1.6;
        }

        .question-image {
            margin: 1.5rem 0;
            text-align: center;
        }

        .question-image img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .options {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .option {
            padding: 1.25rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .option:hover {
            border-color: #cbd5e0;
            background: #f7fafc;
        }

        .option.selected {
            border-color: #667eea;
            background: #eef2ff;
        }

        .option-label {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #4a5568;
            flex-shrink: 0;
        }

        .option.selected .option-label {
            background: #667eea;
            color: white;
        }

        .option-text {
            flex: 1;
            font-size: 0.9375rem;
            color: #2d3748;
        }

        /* Navigation */
        .question-nav {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #e2e8f0;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.9375rem;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .btn-secondary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.3);
        }

        /* Sidebar */
        .sidebar {
            position: sticky;
            top: 90px;
            height: fit-content;
        }

        .progress-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
        }

        .progress-card h3 {
            font-size: 0.9375rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .progress-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .stat {
            text-align: center;
            padding: 0.75rem;
            background: #f7fafc;
            border-radius: 0.5rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #718096;
            margin-top: 0.25rem;
        }

        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s ease;
        }

        /* Question Grid */
        .question-grid {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .question-grid h3 {
            font-size: 0.9375rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.5rem;
        }

        .grid-item {
            aspect-ratio: 1;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .grid-item:hover {
            border-color: #cbd5e0;
        }

        .grid-item.answered {
            background: #d1fae5;
            border-color: #6ee7b7;
            color: #065f46;
        }

        .grid-item.current {
            background: #667eea;
            border-color: #667eea;
            color: white;
        }

        .submit-btn {
            width: 100%;
            margin-top: 1.5rem;
            padding: 1rem;
            background: linear-gradient(135deg, #f56565 0%, #c53030 100%);
            color: white;
            font-size: 1rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(245, 101, 101, 0.3);
        }

        @media (max-width: 1024px) {
            .exam-container {
                grid-template-columns: 1fr;
            }

            .sidebar {
                position: static;
            }
        }

        @media (max-width: 640px) {
            .exam-header {
                padding: 0.75rem 1rem;
            }

            .header-content {
                flex-direction: column;
                gap: 0.5rem;
            }

            .exam-title {
                font-size: 1rem;
            }

            .timer {
                padding: 0.375rem 0.75rem;
            }

            .timer-text {
                font-size: 1rem;
            }

            .exam-container {
                margin: 1rem auto;
                padding: 0 0.75rem;
                gap: 1rem;
            }

            .question-area {
                padding: 1.25rem;
                border-radius: 0.75rem;
            }

            .question-text {
                font-size: 1rem;
            }

            .option {
                padding: 1rem;
                gap: 0.75rem;
            }

            .option-label {
                width: 28px;
                height: 28px;
                font-size: 0.8125rem;
            }

            .option-text {
                font-size: 0.875rem;
            }

            .question-nav {
                margin-top: 1.5rem;
                padding-top: 1rem;
            }

            .btn {
                padding: 0.625rem 1rem;
                font-size: 0.875rem;
            }

            .grid {
                grid-template-columns: repeat(5, 1fr);
                gap: 0.375rem;
            }

            .grid-item {
                font-size: 0.75rem;
            }

            .progress-card, .question-grid {
                padding: 1rem;
                border-radius: 0.75rem;
            }

            .stat-value {
                font-size: 1.25rem;
            }

            .submit-btn {
                padding: 0.75rem;
                font-size: 0.9375rem;
            }

            .timer-banner {
                font-size: 0.8125rem;
                padding: 0.625rem 1rem;
            }

            .save-indicator {
                bottom: 1rem;
                right: 1rem;
                font-size: 0.75rem;
                padding: 0.375rem 0.75rem;
            }

            .submit-overlay-text {
                font-size: 1rem;
                padding: 0 1rem;
                text-align: center;
            }
        }

        @media (max-width: 380px) {
            .grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .exam-container {
                padding: 0 0.5rem;
            }
        }

        /* Submit Modal */
        .submit-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .submit-modal-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .submit-modal-body {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .submit-modal-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
        }

        .submit-modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0 0 0.5rem 0;
        }

        .submit-modal-text {
            font-size: 0.9375rem;
            color: #718096;
            margin: 0;
            line-height: 1.5;
        }

        .submit-modal-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .submit-modal-btn {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.9375rem;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .submit-modal-btn.cancel {
            border: 2px solid #e2e8f0;
            background: white;
            color: #4a5568;
        }

        .submit-modal-btn.confirm {
            border: none;
            background: linear-gradient(135deg, #f56565 0%, #c53030 100%);
            color: white;
        }

        @media (max-width: 640px) {
            .submit-modal-card {
                padding: 1.5rem;
            }

            .submit-modal-icon {
                font-size: 2rem;
            }

            .submit-modal-title {
                font-size: 1.125rem;
            }

            .submit-modal-text {
                font-size: 0.8125rem;
            }

            .submit-modal-btn {
                padding: 0.75rem;
                font-size: 0.8125rem;
            }
        }
        /* Announcement Crawler */
        .crawler-bar {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            overflow: hidden;
            white-space: nowrap;
            position: relative;
            z-index: 99;
        }

        .crawler-track {
            display: inline-block;
            animation: crawl var(--crawl-duration, 20s) linear infinite;
            padding: 0.5rem 0;
        }

        .crawler-text {
            display: inline-block;
            color: white;
            font-size: 0.8125rem;
            font-weight: 600;
            padding: 0 3rem;
        }

        .crawler-text::before {
            content: '📢';
            margin-right: 0.5rem;
        }

        @keyframes crawl {
            0% { transform: translateX(100vw); }
            100% { transform: translateX(-100%); }
        }

        @media (max-width: 640px) {
            .crawler-text {
                font-size: 0.75rem;
                padding: 0 2rem;
            }

            .crawler-track {
                padding: 0.375rem 0;
            }
        }
    </style>
</head>
<body>
    <!-- Announcement Crawler -->
    @if($classroom->crawler_text)
    <div class="crawler-bar">
        <div class="crawler-track" id="crawlerTrack">
            <span class="crawler-text">{{ $classroom->crawler_text }}</span>
            <span class="crawler-text">{{ $classroom->crawler_text }}</span>
        </div>
    </div>
    @endif

    <!-- Timer Warning Banners -->
    <div class="timer-banner warning-banner" id="warningBanner">Warning: Less than 5 minutes remaining!</div>
    <div class="timer-banner danger-banner" id="dangerBanner">Less than 1 minute remaining! Your exam will be auto-submitted.</div>

    <!-- Submit Loading Overlay -->
    <div class="submit-overlay" id="submitOverlay">
        <div class="spinner"></div>
        <div class="submit-overlay-text">Submitting your exam...</div>
    </div>

    <!-- Save Indicator -->
    <div class="save-indicator" id="saveIndicator"></div>

    <!-- Header -->
    <div class="exam-header">
        <div class="header-content">
            <div>
                <div class="exam-title">{{ $classroom->name ?? 'Exam in Progress' }}</div>
                <div style="font-size: 0.75rem; color: #718096; margin-top: 0.25rem;">Exam in Progress</div>
            </div>
            <div class="timer" id="timer">
                <span class="timer-icon">⏱️</span>
                <span class="timer-text" id="timerText">--:--</span>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="exam-container">
        <!-- Question Area -->
        <div class="question-area">
            <div class="question-header">
                <div class="question-number" id="questionNumber"><div class="skeleton skeleton-text short"></div></div>
                <div class="question-text" id="questionText">
                    <div class="skeleton skeleton-text long"></div>
                    <div class="skeleton skeleton-text medium"></div>
                </div>
            </div>

            <div class="question-image" id="questionImage" style="display: none;">
                <img src="" alt="Question Image" id="questionImg">
            </div>

            <div class="options" id="optionsContainer">
                <div class="skeleton skeleton-option"></div>
                <div class="skeleton skeleton-option"></div>
                <div class="skeleton skeleton-option"></div>
                <div class="skeleton skeleton-option"></div>
            </div>

            <div class="question-nav">
                <button class="btn btn-secondary" id="prevBtn" onclick="previousQuestion()">
                    ← Previous
                </button>
                <button class="btn btn-primary" id="nextBtn" onclick="nextQuestion()">
                    Next →
                </button>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <div class="progress-card">
                <h3>Progress</h3>
                <div class="progress-stats">
                    <div class="stat">
                        <div class="stat-value" id="answeredCount">0</div>
                        <div class="stat-label">Answered</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value" id="remainingCount">0</div>
                        <div class="stat-label">Remaining</div>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                </div>
            </div>

            <div class="question-grid">
                <h3>Questions</h3>
                <div class="grid" id="questionGrid">
                    <!-- Grid items will be loaded here -->
                </div>
                <button class="btn submit-btn" onclick="submitExam()">
                    Submit Exam
                </button>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    <!-- Submit Confirmation Modal -->
    <div class="submit-modal" id="submitModal">
        <div class="submit-modal-card">
            <div class="submit-modal-body">
                <div class="submit-modal-icon">⚠️</div>
                <h3 class="submit-modal-title">Submit Exam?</h3>
                <p class="submit-modal-text">Are you sure you want to submit your exam? This action cannot be undone.</p>
            </div>
            <div class="submit-modal-actions">
                <button class="submit-modal-btn cancel" onclick="closeSubmitModal()">Cancel</button>
                <button class="submit-modal-btn confirm" onclick="confirmSubmit()">Submit Exam</button>
            </div>
        </div>
    </div>

    <script>
        // Exam data (will be loaded from backend)
        const sessionId = {{ $session }};
        const code = '{{ $code }}';
        const isPreview = {{ isset($isPreview) && $isPreview ? 'true' : 'false' }};
        const previewData = isPreview ? {!! $previewData ?? '{}' !!} : null;
        
        let questions = [];
        let answers = {};
        let currentQuestionIndex = 0;
        let timerInterval = null;

        // Load exam data
        async function loadExam() {
            try {
                let data;
                
                if (isPreview) {
                    // Preview mode - use preloaded data
                    data = previewData;
                    console.log('Preview mode - using preloaded data');
                } else {
                    // Normal mode - fetch from API
                    const response = await fetch(`/exam/${code}/session/${sessionId}/data`);
                    
                    if (!response.ok) {
                        const errorData = await response.json();
                        console.error('API Error:', errorData);
                        throw new Error(errorData.message || 'Failed to load exam data');
                    }
                    
                    data = await response.json();

                    // Handle server-side expiry
                    if (data.expired && data.redirect) {
                        window.removeEventListener('beforeunload', beforeUnloadHandler);
                        window.location.href = data.redirect;
                        return;
                    }
                }

                // Validate data
                if (!data.questions || !Array.isArray(data.questions)) {
                    console.error('Invalid data received:', data);
                    throw new Error('Invalid exam data received from server');
                }
                
                questions = data.questions;
                answers = data.answers || {};
                
                if (data.timer_minutes && data.expires_at) {
                    startTimer(data.expires_at);
                }
                
                renderQuestionGrid();
                loadQuestion(0);
            } catch (error) {
                console.error('Error loading exam:', error);
                alert(`Error loading exam: ${error.message}\n\nPlease refresh the page or contact support.`);
            }
        }

        // Render question grid
        function renderQuestionGrid() {
            const grid = document.getElementById('questionGrid');
            grid.innerHTML = questions.map((q, index) => `
                <div class="grid-item ${index === currentQuestionIndex ? 'current' : ''} ${answers[q.id] ? 'answered' : ''}" 
                     onclick="loadQuestion(${index})">
                    ${index + 1}
                </div>
            `).join('');
            
            updateProgress();
        }

        // Load question
        function loadQuestion(index) {
            if (index < 0 || index >= questions.length) return;
            
            currentQuestionIndex = index;
            const question = questions[index];
            
            document.getElementById('questionNumber').textContent = `Question ${index + 1} of ${questions.length}`;
            document.getElementById('questionText').textContent = question.question_text;
            
            // Show image if exists
            if (question.image_path) {
                document.getElementById('questionImage').style.display = 'block';
                document.getElementById('questionImg').src = `/storage/${question.image_path}`;
            } else {
                document.getElementById('questionImage').style.display = 'none';
            }
            
            // Render options
            let optionKeys = ['A', 'B', 'C', 'D'];
            
            // Check if shuffling is needed
            if (question.shuffle_answers) {
                if (!window.shuffledOrders) window.shuffledOrders = {};
                
                if (!window.shuffledOrders[question.id]) {
                    // Create a copy and shuffle it
                    const shuffled = [...optionKeys];
                    for (let i = shuffled.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
                    }
                    window.shuffledOrders[question.id] = shuffled;
                }
                optionKeys = window.shuffledOrders[question.id];
            }

            const optionsHtml = optionKeys.map(opt => `
                <div class="option ${answers[question.id] === opt ? 'selected' : ''}" 
                     onclick="selectAnswer('${opt}')">
                    ${!question.shuffle_answers ? `<div class="option-label">${opt}</div>` : ''}
                    <div class="option-text" ${question.shuffle_answers ? 'style="padding-left: 0.5rem;"' : ''}>
                        ${question['option_' + opt.toLowerCase()]}
                    </div>
                </div>
            `).join('');
            
            document.getElementById('optionsContainer').innerHTML = optionsHtml;
            
            // Update navigation buttons
            document.getElementById('prevBtn').disabled = index === 0;
            document.getElementById('nextBtn').textContent = index === questions.length - 1 ? 'Finish' : 'Next →';
            
            renderQuestionGrid();
        }

        // Select answer
        async function selectAnswer(option) {
            const question = questions[currentQuestionIndex];
            answers[question.id] = option;

            loadQuestion(currentQuestionIndex);

            // Save answer to backend (skip in preview mode)
            if (!isPreview) {
                showSaveIndicator('saving');
                try {
                    await fetch(`/exam/${code}/session/${sessionId}/answer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            question_id: question.id,
                            answer: option
                        })
                    });
                    showSaveIndicator('saved');
                } catch (error) {
                    console.error('Error saving answer:', error);
                    showSaveIndicator('error');
                }
            }
        }

        // Save indicator
        function showSaveIndicator(state) {
            const el = document.getElementById('saveIndicator');
            el.className = 'save-indicator';

            if (state === 'saving') {
                el.textContent = 'Saving...';
                el.classList.add('saving');
            } else if (state === 'saved') {
                el.textContent = 'Saved';
                el.classList.add('saved');
                setTimeout(() => { el.style.opacity = '0'; }, 1500);
            }
        }

        // Navigation
        function previousQuestion() {
            if (currentQuestionIndex > 0) {
                loadQuestion(currentQuestionIndex - 1);
            }
        }

        function nextQuestion() {
            if (currentQuestionIndex < questions.length - 1) {
                loadQuestion(currentQuestionIndex + 1);
            } else {
                submitExam();
            }
        }

        // Update progress
        function updateProgress() {
            const answeredCount = Object.keys(answers).length;
            const totalCount = questions.length;
            const remainingCount = totalCount - answeredCount;
            const progress = (answeredCount / totalCount) * 100;
            
            document.getElementById('answeredCount').textContent = answeredCount;
            document.getElementById('remainingCount').textContent = remainingCount;
            document.getElementById('progressFill').style.width = progress + '%';
        }

        // Timer
        let warningShown = false;
        let dangerShown = false;

        function startTimer(expiresAt) {
            const endTime = new Date(expiresAt).getTime();

            function updateTimer() {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance <= 0) {
                    clearInterval(timerInterval);
                    document.getElementById('timerText').textContent = '00:00';
                    // Auto-submit without confirmation when time expires
                    autoSubmitExpired();
                    return;
                }

                const totalSeconds = Math.floor(distance / 1000);
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;
                const timerEl = document.getElementById('timer');

                document.getElementById('timerText').textContent =
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                // 5 minute warning
                if (totalSeconds <= 300 && !warningShown) {
                    warningShown = true;
                    timerEl.classList.add('warning');
                    document.getElementById('warningBanner').classList.add('visible');
                    setTimeout(() => {
                        document.getElementById('warningBanner').classList.remove('visible');
                    }, 5000);
                }

                // 1 minute critical warning
                if (totalSeconds <= 60 && !dangerShown) {
                    dangerShown = true;
                    timerEl.classList.remove('warning');
                    timerEl.classList.add('danger');
                    document.getElementById('warningBanner').classList.remove('visible');
                    document.getElementById('dangerBanner').classList.add('visible');
                }

                // Hide danger banner at 30s (it's been visible long enough)
                if (totalSeconds <= 30) {
                    document.getElementById('dangerBanner').classList.remove('visible');
                }
            }

            updateTimer(); // run immediately
            timerInterval = setInterval(updateTimer, 1000);
        }

        // Auto-submit when time expires (no confirmation modal)
        async function autoSubmitExpired() {
            if (isPreview) return;

            document.getElementById('submitOverlay').classList.add('visible');
            document.querySelector('.submit-overlay-text').textContent = 'Time is up! Submitting your exam...';

            try {
                const response = await fetch(`/exam/${code}/session/${sessionId}/submit`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    window.removeEventListener('beforeunload', beforeUnloadHandler);
                    window.location.href = result.redirect;
                }
            } catch (error) {
                console.error('Error auto-submitting exam:', error);
                document.getElementById('submitOverlay').classList.remove('visible');
                alert('Failed to auto-submit. Please click Submit Exam manually.');
            }
        }

        // Submit exam
        function submitExam() {
            // Show custom confirmation modal
            document.getElementById('submitModal').style.display = 'flex';
        }

        function closeSubmitModal() {
            document.getElementById('submitModal').style.display = 'none';
        }

        async function confirmSubmit() {
            closeSubmitModal();

            if (isPreview) {
                alert('This is a preview. Exam submission is disabled.');
                return;
            }

            // Show loading overlay
            document.getElementById('submitOverlay').classList.add('visible');

            try {
                const response = await fetch(`/exam/${code}/session/${sessionId}/submit`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    window.removeEventListener('beforeunload', beforeUnloadHandler);
                    window.location.href = result.redirect;
                } else {
                    document.getElementById('submitOverlay').classList.remove('visible');
                    alert(result.error || 'Error submitting exam. Please try again.');
                }
            } catch (error) {
                console.error('Error submitting exam:', error);
                document.getElementById('submitOverlay').classList.remove('visible');
                alert('Error submitting exam. Please try again.');
            }
        }

        // Prevent page refresh during exam
        const beforeUnloadHandler = (e) => {
            e.preventDefault();
            e.returnValue = '';
        };
        
        window.addEventListener('beforeunload', beforeUnloadHandler);

        // ==========================================
        // ANTI-CHEATING MEASURES
        // ==========================================
        
        // Disable right-click (context menu)
        document.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            return false;
        });

        // Disable text selection
        document.addEventListener('selectstart', (e) => {
            e.preventDefault();
            return false;
        });

        // Disable copy, cut, and paste
        document.addEventListener('copy', (e) => {
            e.preventDefault();
            return false;
        });

        document.addEventListener('cut', (e) => {
            e.preventDefault();
            return false;
        });

        document.addEventListener('paste', (e) => {
            e.preventDefault();
            return false;
        });

        // Disable keyboard shortcuts for copying
        document.addEventListener('keydown', (e) => {
            // Ctrl+C, Ctrl+X, Ctrl+V, Ctrl+A, Ctrl+P, Ctrl+S, F12
            if (
                (e.ctrlKey && (e.key === 'c' || e.key === 'C')) ||  // Copy
                (e.ctrlKey && (e.key === 'x' || e.key === 'X')) ||  // Cut
                (e.ctrlKey && (e.key === 'v' || e.key === 'V')) ||  // Paste
                (e.ctrlKey && (e.key === 'a' || e.key === 'A')) ||  // Select All
                (e.ctrlKey && (e.key === 'p' || e.key === 'P')) ||  // Print
                (e.ctrlKey && (e.key === 's' || e.key === 'S')) ||  // Save
                (e.ctrlKey && (e.key === 'u' || e.key === 'U')) ||  // View Source
                e.key === 'F12' ||                                   // DevTools
                (e.ctrlKey && e.shiftKey && (e.key === 'i' || e.key === 'I')) || // DevTools
                (e.ctrlKey && e.shiftKey && (e.key === 'j' || e.key === 'J')) || // DevTools
                (e.ctrlKey && e.shiftKey && (e.key === 'c' || e.key === 'C'))    // DevTools
            ) {
                e.preventDefault();
                return false;
            }
        });

        // Disable drag and drop
        document.addEventListener('dragstart', (e) => {
            e.preventDefault();
            return false;
        });

        // Add CSS to prevent text selection
        const style = document.createElement('style');
        style.textContent = `
            body {
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
            
            /* Allow selection only in answer buttons */
            .answer-btn {
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }
        `;
        document.head.appendChild(style);

        // Detect if user switches tabs/windows (optional - for monitoring)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.warn('Student switched tabs/windows at:', new Date().toISOString());
                // You could log this to the server for monitoring
            }
        });

        // Adjust crawler speed based on text length
        const crawlerTrack = document.getElementById('crawlerTrack');
        if (crawlerTrack) {
            const textLen = crawlerTrack.textContent.trim().length;
            const duration = Math.max(15, textLen * 0.2);
            crawlerTrack.style.setProperty('--crawl-duration', duration + 's');
            crawlerTrack.style.animationDuration = duration + 's';
        }

        // Load exam on page load
        loadExam();
    </script>

    <!-- Footer -->
    <div style="text-align: center; padding: 1rem 0; color: #718096; font-size: 0.8125rem; background: white; border-top: 1px solid #e2e8f0;">
        © {{ date('Y') }} EzExam by <a href="https://azfarmiskam.site" target="_blank" style="color: inherit; text-decoration: underline;">AzfarMiskam</a>. All rights reserved.
    </div>
</body>
</html>
