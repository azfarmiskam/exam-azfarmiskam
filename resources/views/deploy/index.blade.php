<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deployment Helper</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; max-width: 800px; margin: 0 auto; padding: 2rem; background: #f3f4f6; }
        .card { background: white; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        h1 { margin-top: 0; color: #111827; }
        h2 { font-size: 1.25rem; color: #374151; margin-bottom: 1rem; }
        .status { padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem; }
        .status.connected { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .status.failed { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .btn { display: inline-block; padding: 0.75rem 1.5rem; background-color: #2563eb; color: white; text-decoration: none; border-radius: 0.375rem; border: none; cursor: pointer; font-size: 1rem; margin-right: 0.5rem; }
        .btn:hover { background-color: #1d4ed8; }
        .btn-secondary { background-color: #4b5563; }
        .btn-secondary:hover { background-color: #374151; }
        pre { background: #1f2937; color: #f9fafb; padding: 1rem; border-radius: 0.375rem; overflow-x: auto; font-size: 0.875rem; }
        .alert { padding: 1rem; border-radius: 0.375rem; margin-bottom: 1rem; }
        .alert-success { background-color: #d1fae5; color: #065f46; }
        .alert-error { background-color: #fee2e2; color: #991b1b; }
        form { display: inline; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Deployment Helper</h1>
        <p>Use this tool to manage database migrations and cache on your shared hosting.</p>

        <h2>Database Connection</h2>
        @if(str_contains($dbStatus, 'Connected'))
            <div class="status connected">
                ✅ {{ $dbStatus }}
            </div>
        @else
            <div class="status failed">
                ❌ {{ $dbStatus }}
                @if($dbError)
                    <br><small>{{ $dbError }}</small>
                @endif
            </div>
            <p><strong>Tip:</strong> Ensure your <code>.env</code> file has the correct database credentials.</p>
        @endif
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if(session('output'))
            <h3>Command Output:</h3>
            <pre>{{ session('output') }}</pre>
        @endif

        <h2>Actions</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <form action="{{ route('deploy.migrate') }}" method="POST">
                @csrf
                <button type="submit" class="btn" onclick="return confirm('Are you sure you want to run MIGRATIONS? This updates the database structure.')">Run Migrations</button>
            </form>

            <form action="{{ route('deploy.seed') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary" onclick="return confirm('Are you sure you want to run SEEDERS? This adds test data.')">Run Seeders</button>
            </form>

            <form action="{{ route('deploy.clear') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary">Clear Cache</button>
            </form>
        </div>
    </div>
</body>
</html>
