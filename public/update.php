<?php
/**
 * ExamJe Web Updater
 *
 * Upload a new version zip to update the system.
 * Preserves .env, database, uploaded files, and storage.
 */

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Cache-Control: no-store, no-cache, must-revalidate');

$basePath = dirname(__DIR__);
$error = '';
$success = '';
$step = isset($_GET['step']) ? (int)$_GET['step'] : 0;

// Get current version
$currentVersion = 'unknown';
if (file_exists($basePath . '/config/app.php')) {
    $config = include $basePath . '/config/app.php';
    $currentVersion = $config['version'] ?? 'unknown';
}

// Require auth — only logged-in admins can update
if ($step > 0) {
    session_start();
    // Bootstrap Laravel to check auth
    try {
        $app = require $basePath . '/bootstrap/app.php';
        $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
        $response = $kernel->handle($request = \Illuminate\Http\Request::capture());

        if (!auth()->check()) {
            header('Location: /login');
            exit;
        }
    } catch (\Exception $e) {
        // If Laravel can't boot, allow access (might be mid-update)
    }
}

// =============================================
// STEP HANDLERS
// =============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 1) {

    // Validate upload
    if (!isset($_FILES['update_zip']) || $_FILES['update_zip']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a valid zip file.';
        $step = 0;
        goto render;
    }

    $file = $_FILES['update_zip'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'zip') {
        $error = 'Only .zip files are accepted.';
        $step = 0;
        goto render;
    }

    $tempDir = $basePath . '/storage/app/update-temp';
    $zipPath = $basePath . '/storage/app/update.zip';

    // Clean previous temp
    if (is_dir($tempDir)) {
        deleteDirectory($tempDir);
    }
    mkdir($tempDir, 0755, true);

    // Move uploaded file
    move_uploaded_file($file['tmp_name'], $zipPath);

    // Extract zip
    $zip = new ZipArchive();
    if ($zip->open($zipPath) !== true) {
        $error = 'Failed to open zip file.';
        $step = 0;
        goto render;
    }

    $zip->extractTo($tempDir);
    $zip->close();

    // Find the root of extracted content (might be in a subfolder)
    $extractedRoot = $tempDir;
    $items = array_diff(scandir($tempDir), ['.', '..']);
    if (count($items) === 1) {
        $singleItem = $tempDir . '/' . reset($items);
        if (is_dir($singleItem) && file_exists($singleItem . '/artisan')) {
            $extractedRoot = $singleItem;
        }
    }

    // Verify it's a valid ExamJe package
    if (!file_exists($extractedRoot . '/artisan') || !file_exists($extractedRoot . '/config/app.php')) {
        $error = 'Invalid update package. The zip must contain a valid ExamJe installation.';
        deleteDirectory($tempDir);
        @unlink($zipPath);
        $step = 0;
        goto render;
    }

    // Get new version
    $newConfig = include $extractedRoot . '/config/app.php';
    $newVersion = $newConfig['version'] ?? 'unknown';

    // Store info for confirmation step
    session_start();
    $_SESSION['update_extracted_root'] = $extractedRoot;
    $_SESSION['update_new_version'] = $newVersion;
    $_SESSION['update_zip_path'] = $zipPath;

    $step = 1;
    goto render;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 2) {
    session_start();
    $extractedRoot = $_SESSION['update_extracted_root'] ?? '';
    $newVersion = $_SESSION['update_new_version'] ?? '';
    $zipPath = $_SESSION['update_zip_path'] ?? '';

    if (empty($extractedRoot) || !is_dir($extractedRoot)) {
        $error = 'Update session expired. Please upload the zip again.';
        $step = 0;
        goto render;
    }

    $log = [];

    try {
        // 1. Create backup of key files
        $log[] = 'Creating backup...';
        $backupDir = $basePath . '/storage/app/backup-' . date('Y-m-d-His');
        mkdir($backupDir, 0755, true);
        if (file_exists($basePath . '/.env')) {
            copy($basePath . '/.env', $backupDir . '/.env');
        }
        $log[] = 'Backup saved to: storage/app/' . basename($backupDir);

        // 2. Files/folders to SKIP (preserve existing)
        $skipFiles = ['.env', '.env.backup', '.env.production', 'database.sqlite', '.installed'];
        $skipDirs = ['storage/app', 'storage/logs', 'storage/framework/sessions', 'database', '.git', 'node_modules'];

        // 3. Copy new files over
        $log[] = 'Copying new files...';
        $fileCount = copyDirectory($extractedRoot, $basePath, $skipFiles, $skipDirs);
        $log[] = "Updated {$fileCount} files.";

        // 4. Run migrations
        $log[] = 'Running database migrations...';
        try {
            // Clear cached config before bootstrapping
            @unlink($basePath . '/bootstrap/cache/config.php');
            @unlink($basePath . '/bootstrap/cache/routes-v7.php');
            @unlink($basePath . '/bootstrap/cache/services.php');
            @unlink($basePath . '/bootstrap/cache/packages.php');

            if (function_exists('opcache_reset')) {
                opcache_reset();
            }

            // Fresh bootstrap
            $app = require $basePath . '/bootstrap/app.php';
            $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();

            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $migrateOutput = \Illuminate\Support\Facades\Artisan::output();
            $log[] = 'Migrations completed.';

            // Clear all caches
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('route:clear');
            \Illuminate\Support\Facades\Artisan::call('view:clear');
            $log[] = 'Caches cleared.';
        } catch (\Exception $e) {
            $log[] = 'Migration warning: ' . $e->getMessage();
        }

        // 5. Cleanup temp files
        $tempDir = $basePath . '/storage/app/update-temp';
        deleteDirectory($tempDir);
        @unlink($zipPath);
        $log[] = 'Temp files cleaned.';

        // Clear session
        unset($_SESSION['update_extracted_root'], $_SESSION['update_new_version'], $_SESSION['update_zip_path']);

        $_SESSION['update_log'] = $log;
        $_SESSION['update_success_version'] = $newVersion;

        header('Location: update.php?step=3');
        exit;

    } catch (\Exception $e) {
        $error = 'Update failed: ' . $e->getMessage();
        $step = 0;
        goto render;
    }
}

if ($step === 3) {
    session_start();
    $log = $_SESSION['update_log'] ?? [];
    $newVersion = $_SESSION['update_success_version'] ?? 'unknown';
    unset($_SESSION['update_log'], $_SESSION['update_success_version']);
}

// =============================================
// HELPER FUNCTIONS
// =============================================

function deleteDirectory($dir) {
    if (!is_dir($dir)) return;
    $items = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($items as $item) {
        $item->isDir() ? rmdir($item->getRealPath()) : unlink($item->getRealPath());
    }
    rmdir($dir);
}

function copyDirectory($src, $dst, $skipFiles = [], $skipDirs = []) {
    $count = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($iterator as $item) {
        $relativePath = str_replace($src . DIRECTORY_SEPARATOR, '', $item->getPathname());
        $relativePath = str_replace('\\', '/', $relativePath);
        $targetPath = $dst . '/' . $relativePath;

        // Check skip dirs
        $skip = false;
        foreach ($skipDirs as $skipDir) {
            if (strpos($relativePath, $skipDir) === 0) {
                $skip = true;
                break;
            }
        }
        if ($skip) continue;

        // Check skip files
        if (in_array(basename($relativePath), $skipFiles)) continue;

        if ($item->isDir()) {
            if (!is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
        } else {
            $dir = dirname($targetPath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            copy($item->getPathname(), $targetPath);
            $count++;
        }
    }
    return $count;
}

render:
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update ExamJe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; }
        .updater { background: white; border-radius: 1.5rem; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 560px; width: 100%; overflow: hidden; animation: slideUp 0.4s ease-out; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .updater-header { background: linear-gradient(135deg, #1e3a5f, #2d5a87); padding: 2rem; text-align: center; color: white; }
        .updater-header img { height: 56px; margin-bottom: 0.75rem; }
        .updater-header h1 { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem; }
        .updater-header p { font-size: 0.8125rem; opacity: 0.8; }
        .updater-body { padding: 2rem; }
        .alert { padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; margin-bottom: 1.25rem; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-warning { background: #fef3c7; color: #92400e; }
        .version-badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 700; }
        .version-current { background: #e2e8f0; color: #475569; }
        .version-new { background: #d1fae5; color: #065f46; }
        .info-box { background: #f8fafc; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem; }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; font-size: 0.875rem; }
        .info-row:not(:last-child) { border-bottom: 1px solid #e2e8f0; }
        .info-label { color: #64748b; }
        .info-value { font-weight: 600; }
        .form-group { margin-bottom: 1.25rem; }
        .form-label { display: block; font-weight: 600; font-size: 0.8125rem; color: #334155; margin-bottom: 0.5rem; }
        .form-control { width: 100%; padding: 0.625rem 0.875rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; font-family: 'Inter', sans-serif; }
        .form-control:focus { outline: none; border-color: #667eea; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.9375rem; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.2s; width: 100%; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(102,126,234,0.4); }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .btn-secondary { background: #e2e8f0; color: #475569; }
        .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
        .btn-group { display: flex; gap: 0.75rem; margin-top: 1.5rem; }
        .log-box { background: #1e293b; color: #e2e8f0; border-radius: 0.5rem; padding: 1rem; font-family: monospace; font-size: 0.75rem; max-height: 200px; overflow-y: auto; line-height: 1.8; margin: 1rem 0; }
        .log-box .log-item { padding: 0.125rem 0; }
        .log-box .log-item::before { content: '> '; color: #10b981; }
        .success-icon { width: 72px; height: 72px; margin: 0 auto 1.25rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: white; }
        .checklist { list-style: none; margin: 1rem 0; }
        .checklist li { padding: 0.375rem 0; font-size: 0.8125rem; color: #475569; display: flex; align-items: center; gap: 0.5rem; }
        .checklist li::before { content: '✓'; color: #10b981; font-weight: 700; }
        @media (max-width: 640px) { body { padding: 1rem 0.5rem; } .updater-body { padding: 1.5rem; } }
    </style>
</head>
<body>
<div class="updater">
    <div class="updater-header">
        <?php if (file_exists(__DIR__ . '/images/logo.png')): ?>
            <img src="images/logo.png" alt="ExamJe">
        <?php endif; ?>
        <h1>System Updater</h1>
        <p>Current version: <span class="version-badge version-current">v<?= htmlspecialchars($currentVersion) ?></span></p>
    </div>

    <div class="updater-body">

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($step === 0): ?>
    <!-- STEP 0: Upload -->
    <div class="info-box">
        <p style="font-size: 0.8125rem; color: #475569; margin-bottom: 0.75rem;">Upload the new version zip file to update your system. The updater will:</p>
        <ul class="checklist">
            <li>Preserve your .env, database, and uploaded files</li>
            <li>Update all application files</li>
            <li>Run new database migrations</li>
            <li>Clear all caches</li>
        </ul>
    </div>

    <div class="alert alert-warning">
        <strong>Recommended:</strong> Back up your database before updating.
    </div>

    <form method="POST" action="update.php?step=1" enctype="multipart/form-data" id="uploadForm">
        <div class="form-group">
            <label class="form-label">Update Package (.zip)</label>
            <input type="file" name="update_zip" class="form-control" accept=".zip" required>
        </div>
        <button type="submit" class="btn btn-primary" id="uploadBtn">Upload & Verify</button>
    </form>

    <div style="text-align: center; margin-top: 1.25rem;">
        <a href="/admin/dashboard" style="font-size: 0.8125rem; color: #64748b; text-decoration: none;">Back to Dashboard</a>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', function() {
            const btn = document.getElementById('uploadBtn');
            btn.disabled = true;
            btn.textContent = 'Uploading & verifying...';
        });
    </script>

    <?php elseif ($step === 1): ?>
    <!-- STEP 1: Confirm -->
    <?php
        session_start();
        $newVersion = $_SESSION['update_new_version'] ?? 'unknown';
    ?>
    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Current Version</span>
            <span class="version-badge version-current">v<?= htmlspecialchars($currentVersion) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">New Version</span>
            <span class="version-badge version-new">v<?= htmlspecialchars($newVersion) ?></span>
        </div>
    </div>

    <div class="alert alert-warning">
        <strong>This will update your system.</strong> Your database, .env file, and uploaded files will be preserved. This action cannot be undone.
    </div>

    <form method="POST" action="update.php?step=2" id="confirmForm">
        <div class="btn-group">
            <a href="update.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-danger" id="confirmBtn">Confirm Update</button>
        </div>
    </form>

    <script>
        document.getElementById('confirmForm').addEventListener('submit', function() {
            const btn = document.getElementById('confirmBtn');
            btn.disabled = true;
            btn.textContent = 'Updating... do not close this page';
        });
    </script>

    <?php elseif ($step === 3): ?>
    <!-- STEP 3: Success -->
    <div style="text-align: center;">
        <div class="success-icon">&#10003;</div>
        <h2 style="font-size: 1.375rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Update Complete!</h2>
        <p style="color: #64748b; font-size: 0.9375rem;">ExamJe has been updated to <span class="version-badge version-new">v<?= htmlspecialchars($newVersion) ?></span></p>
    </div>

    <div class="log-box">
        <?php foreach ($log as $line): ?>
            <div class="log-item"><?= htmlspecialchars($line) ?></div>
        <?php endforeach; ?>
    </div>

    <div class="alert alert-success">
        A backup of your previous .env was saved in <strong>storage/app/</strong>.
    </div>

    <a href="/admin/dashboard" class="btn btn-primary">Go to Dashboard</a>

    <?php
        // Auto-rename updater after showing success page
        @rename(__FILE__, __DIR__ . '/update.php.disabled');
    ?>

    <?php endif; ?>

    </div>
</div>
</body>
</html>
