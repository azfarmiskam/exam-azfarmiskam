<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - EzExam</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/auth.css'])
</head>
<body>
    <div class="auth-page">
        <!-- Background Elements -->
        <div class="auth-background"></div>
        
        <!-- Login Container -->
        <div class="auth-container">
            <!-- Back to Home Link - Top Left -->
            <a href="/" class="back-link-top">
                <span>←</span>
                <span>Back to Home</span>
            </a>
            
            <div class="auth-card">
                <!-- Two Column Layout -->
                <div class="auth-grid">
                    <!-- Left Side - Logo & Branding -->
                    <div class="auth-brand">
                        <div class="brand-content">
                            <div class="brand-logo">
                                <div class="logo-icon-large">📝</div>
                                <h1 class="brand-title">EzExam</h1>
                                <p class="brand-tagline">Admin Portal</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Login Form -->
                    <div class="auth-form-section">
                        <!-- Error Messages -->
                        @if($errors->any())
                            <div class="alert alert-error">
                                <span class="alert-icon">⚠️</span>
                                <div class="alert-content">
                                    @foreach($errors->all() as $error)
                                        <p>{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-error">
                                <span class="alert-icon">⚠️</span>
                                <div class="alert-content">
                                    <p>{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="alert alert-success">
                                <span class="alert-icon">✓</span>
                                <div class="alert-content">
                                    <p>{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form action="{{ route('login.post') }}" method="POST" class="auth-form" id="loginForm">
                            @csrf
                            
                            <!-- Email Field -->
                            <div class="form-group">
                                <label for="email" class="form-label">
                                    <span class="label-icon">📧</span>
                                    Email Address
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="form-control @error('email') is-invalid @enderror" 
                                    placeholder="admin@example.com"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                >
                                @error('email')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <span class="label-icon">🔒</span>
                                    Password
                                </label>
                                <div class="password-input-wrapper">
                                    <input 
                                        type="password" 
                                        id="password" 
                                        name="password" 
                                        class="form-control @error('password') is-invalid @enderror" 
                                        placeholder="Enter your password"
                                        required
                                    >
                                    <button type="button" class="password-toggle" id="togglePassword">
                                        <span class="eye-icon">👁️</span>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Math Captcha -->
                            <div class="form-group">
                                <label for="captcha" class="form-label">
                                    <span class="label-icon">🧮</span>
                                    Security Check
                                </label>
                                <div class="captcha-wrapper">
                                    <div class="captcha-question">
                                        <span class="captcha-text">What is</span>
                                        <span class="captcha-math" id="captchaQuestion">{{ session('captcha_num1', rand(1, 10)) }} + {{ session('captcha_num2', rand(1, 10)) }}</span>
                                        <span class="captcha-text">?</span>
                                        <button type="button" class="captcha-refresh" id="refreshCaptcha" title="Refresh">
                                            🔄
                                        </button>
                                    </div>
                                    <input 
                                        type="number" 
                                        id="captcha" 
                                        name="captcha" 
                                        class="form-control captcha-input @error('captcha') is-invalid @enderror" 
                                        placeholder="Answer"
                                        required
                                    >
                                </div>
                                @error('captcha')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="remember" id="remember">
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">Remember me</span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                                <span class="btn-text">Sign In</span>
                                <span class="btn-icon">→</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Password Toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = togglePassword.querySelector('.eye-icon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.textContent = type === 'password' ? '👁️' : '🙈';
        });

        // Captcha Refresh
        const refreshCaptcha = document.getElementById('refreshCaptcha');
        const captchaQuestion = document.getElementById('captchaQuestion');
        const captchaInput = document.getElementById('captcha');

        refreshCaptcha.addEventListener('click', function() {
            // Generate new random numbers
            const num1 = Math.floor(Math.random() * 10) + 1;
            const num2 = Math.floor(Math.random() * 10) + 1;
            
            // Update display
            captchaQuestion.textContent = `${num1} + ${num2}`;
            captchaInput.value = '';
            captchaInput.focus();
            
            // Store in session via AJAX
            fetch('{{ route('captcha.refresh') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ num1, num2 })
            });
            
            // Animate refresh button
            refreshCaptcha.style.transform = 'rotate(360deg)';
            setTimeout(() => {
                refreshCaptcha.style.transform = 'rotate(0deg)';
            }, 300);
        });

        // Form Validation
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        loginForm.addEventListener('submit', function(e) {
            // Disable submit button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="btn-text">Signing in...</span>';
        });

        // Auto-focus on first error field
        window.addEventListener('DOMContentLoaded', function() {
            const firstError = document.querySelector('.is-invalid');
            if (firstError) {
                firstError.focus();
            }
        });
    </script>
</body>
</html>
