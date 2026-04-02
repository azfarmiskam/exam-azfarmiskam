<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Identity - EzExam</title>
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

        .verify-container {
            background: white;
            padding: 2.5rem;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 480px;
            width: 100%;
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header-icon {
            width: 64px;
            height: 64px;
            background: #eef2ff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.75rem;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 0.5rem;
        }

        .header p {
            color: #718096;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .exam-info {
            background: #f7fafc;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #667eea;
            font-size: 0.875rem;
        }

        .exam-info-label {
            color: #718096;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .exam-info-value {
            color: #2d3748;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
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

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
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
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
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

        .btn-back {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: #718096;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .btn-back:hover {
            color: #4a5568;
        }

        @media (max-width: 640px) {
            body {
                padding: 1rem 0.75rem;
                align-items: flex-start;
                padding-top: 2rem;
            }

            .verify-container {
                padding: 1.75rem 1.25rem;
                border-radius: 1rem;
            }

            .header-icon {
                width: 52px;
                height: 52px;
                font-size: 1.5rem;
            }

            .header h1 {
                font-size: 1.25rem;
            }

            .header p {
                font-size: 0.8125rem;
            }

            .form-control {
                padding: 0.625rem 0.875rem;
                font-size: 0.875rem;
            }

            .btn {
                padding: 0.75rem 1rem;
                font-size: 0.9375rem;
            }
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <div class="header">
            <div class="header-icon">🔐</div>
            <h1>Verify Your Identity</h1>
            <p>Enter your matric number and email to view your exam results.</p>
        </div>

        <div class="exam-info">
            <div class="exam-info-label">Exam</div>
            <div class="exam-info-value">{{ $session->classroom->name }}</div>
        </div>

        @if($errors->has('identity'))
            <div class="error-message">
                {{ $errors->first('identity') }}
            </div>
        @endif

        <form action="{{ route('exam.results.verify.submit', ['code' => $code, 'session' => $session->id]) }}" method="POST" id="verifyForm">
            @csrf

            <div class="form-group">
                <label class="form-label">Student ID / Matric Number</label>
                <input type="text" name="matric_number" class="form-control" placeholder="Enter your student ID" value="{{ old('matric_number') }}" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="your.email@example.com" value="{{ old('email') }}" required>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                View Results
            </button>

            <a href="{{ route('home') }}" class="btn-back">Back to Home</a>
        </form>
    </div>

    <script>
        const form = document.getElementById('verifyForm');
        const btn = document.getElementById('submitBtn');

        form.addEventListener('submit', function() {
            btn.disabled = true;
            btn.textContent = 'Verifying...';
        });
    </script>
</body>
</html>
