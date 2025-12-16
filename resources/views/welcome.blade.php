<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Online Examination System - Enter your exam code to begin">
    <title>EzExam - Welcome</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/homepage.css'])
</head>
<body>
    <div class="homepage">
        <!-- Header -->
        <header class="homepage-header">
            <div class="container">
                <nav class="homepage-nav">
                    <a href="/" class="homepage-logo">
                        <div class="logo-icon">📝</div>
                        <span>EzExam</span>
                    </a>
                    <a href="{{ route('login') }}" class="admin-login-btn">
                        <span>🔐</span>
                        <span>Login</span>
                    </a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="homepage-main">
            <div class="container">
                <div class="homepage-content">
                    <h1 class="homepage-title">Welcome to EzExam</h1>
                    <p class="homepage-subtitle">Enter your exam code below to start your examination</p>

                    <!-- Main Grid Container -->
                    <div class="main-grid">
                        <!-- Exam Code Entry Card -->
                        <div class="exam-code-card">
                            <h2>Enter Exam Code</h2>
                            <p>Please enter the unique code provided by your instructor</p>

                            @if(session('error'))
                                <div class="error-message show">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <form action="{{ route('exam.verify') }}" method="POST" id="examCodeForm">
                                @csrf
                                <div class="code-input-group">
                                    <input 
                                        type="text" 
                                        name="code" 
                                        id="examCode" 
                                        class="code-input" 
                                        placeholder="Enter exam code"
                                        maxlength="10"
                                        required
                                        autofocus
                                    >
                                </div>
                                <button type="submit" class="submit-code-btn" id="submitBtn">
                                    Start Exam
                                </button>
                            </form>
                        </div>

                        <!-- Features Grid -->
                        <div class="features-grid">
                            <div class="feature-item">
                                <div class="feature-icon">⏱️</div>
                                <h3>Timed Exams</h3>
                                <p>Auto-submit when time expires</p>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">✅</div>
                                <h3>Instant Results</h3>
                                <p>Get your scores immediately</p>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">🔒</div>
                                <h3>Secure Platform</h3>
                                <p>Protected exam environment</p>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">🛡️</div>
                                <h3>Anti-Cheat</h3>
                                <p>Copy and right-click disabled</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="homepage-footer">
            <div class="container">
                <p>&copy; {{ date('Y') }} EzExam. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <script>
        // Auto-uppercase exam code input
        const examCodeInput = document.getElementById('examCode');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('examCodeForm');

        examCodeInput.addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });

        // Form validation
        form.addEventListener('submit', function(e) {
            const code = examCodeInput.value.trim();
            
            if (code.length < 4) {
                e.preventDefault();
                showError('Please enter a valid exam code (minimum 4 characters)');
                return;
            }

            // Disable button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.textContent = 'Verifying...';
        });

        function showError(message) {
            const errorDiv = document.querySelector('.error-message');
            if (!errorDiv) {
                const newErrorDiv = document.createElement('div');
                newErrorDiv.className = 'error-message show';
                newErrorDiv.textContent = message;
                form.insertBefore(newErrorDiv, form.firstChild);
            } else {
                errorDiv.textContent = message;
                errorDiv.classList.add('show');
            }

            // Hide error after 5 seconds
            setTimeout(() => {
                const errorDiv = document.querySelector('.error-message');
                if (errorDiv) {
                    errorDiv.classList.remove('show');
                }
            }, 5000);
        }

        // Remove error message when user starts typing
        examCodeInput.addEventListener('input', function() {
            const errorDiv = document.querySelector('.error-message');
            if (errorDiv) {
                errorDiv.classList.remove('show');
            }
        });
    </script>
</body>
</html>
