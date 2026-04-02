<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register for {{ $classroom->name }} - ExamJe</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 1rem;
        }

        .register-container {
            background: white;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #718096;
            font-size: 0.95rem;
        }

        .classroom-info {
            background: #f7fafc;
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin-bottom: 2rem;
            border-left: 4px solid #667eea;
        }

        .classroom-info h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-label {
            font-weight: 600;
            color: #4a5568;
            font-size: 0.875rem;
        }

        .info-value {
            color: #2d3748;
            font-size: 0.875rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .form-label .required {
            color: #e53e3e;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 0.875rem 1.5rem;
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
            width: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .error-message {
            background: #fed7d7;
            color: #c53030;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        @media (max-width: 640px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="header">
            <img src="{{ $logoUrl }}" alt="ExamJe" style="height: 64px; width: auto; margin-bottom: 0.75rem;">
            <h1>Student Registration</h1>
            <p>Please fill in your details to start the exam</p>
        </div>

        <div class="classroom-info">
            <h2>{{ $classroom->name }}</h2>
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">📋 Questions:</span>
                    <span class="info-value">{{ $classroom->questions_per_exam }}</span>
                </div>
                @if($classroom->timer_minutes)
                <div class="info-item">
                    <span class="info-label">⏱️ Time Limit:</span>
                    <span class="info-value">{{ $classroom->timer_minutes }} minutes</span>
                </div>
                @endif
                <div class="info-item">
                    <span class="info-label">📊 Show Results:</span>
                    <span class="info-value">{{ $classroom->show_results ? 'Yes' : 'No' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">🔄 Shuffle:</span>
                    <span class="info-value">{{ $classroom->shuffle_questions ? 'Yes' : 'No' }}</span>
                </div>
            </div>
        </div>

        <div id="errorMessage" class="error-message"></div>

        <form id="registrationForm">
            @csrf
            <input type="hidden" name="classroom_id" value="{{ $classroom->id }}">

            @php
                $groups = $classroom->groups;
            @endphp

            @if($groups->count() > 0)
            <div class="form-group">
                <label class="form-label">
                    Group <span class="required">*</span>
                </label>
                <select name="group_id" class="form-control" required>
                    <option value="">Select your group</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div class="form-group">
                <label class="form-label">
                    Full Name <span class="required">*</span>
                </label>
                <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Student ID / Matric Number <span class="required">*</span>
                </label>
                <input type="text" name="matric_number" class="form-control" placeholder="Enter your student ID" required>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Email Address <span class="required">*</span>
                </label>
                <input type="email" name="email" class="form-control" placeholder="your.email@example.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number (Optional)</label>
                <input type="tel" name="phone" class="form-control" placeholder="Enter your phone number">
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                Continue to Exam Instructions →
            </button>

            <a href="/" class="btn btn-secondary">
                ← Cancel
            </a>
        </form>
    </div>

    <script>
        const form = document.getElementById('registrationForm');
        const submitBtn = document.getElementById('submitBtn');
        const errorMessage = document.getElementById('errorMessage');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Disable button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Registering...';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch('{{ route("exam.register.submit", $classroom->code) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // Redirect to exam instructions
                    window.location.href = result.redirect;
                } else {
                    showError(result.message || 'Registration failed. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Continue to Exam Instructions →';
                }
            } catch (error) {
                console.error('Error:', error);
                showError('An error occurred. Please try again.');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Continue to Exam Instructions →';
            }
        });

        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.classList.add('show');

            setTimeout(() => {
                errorMessage.classList.remove('show');
            }, 5000);
        }
    </script>
</body>
</html>
