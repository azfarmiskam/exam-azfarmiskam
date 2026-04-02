<?php
/**
 * ExamJe Web Installer
 *
 * One-click installation wizard for shared hosting (cPanel, StackCP) and VPS.
 * Upload all files, then visit: https://yourdomain.com/install.php
 */

// Prevent running if already installed
$basePath = dirname(__DIR__);
if (file_exists($basePath . '/storage/.installed')) {
    header('Location: /');
    exit;
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 0;
$error = '';

// =============================================
// STEP HANDLERS (POST)
// =============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Step 3: Test & save database config
    if ($step === 3) {
        $dbConnection = $_POST['db_connection'] ?? 'mysql';
        $dbHost = $_POST['db_host'] ?? '127.0.0.1';
        $dbPort = $_POST['db_port'] ?? '3306';
        $dbDatabase = $_POST['db_database'] ?? '';
        $dbUsername = $_POST['db_username'] ?? '';
        $dbPassword = $_POST['db_password'] ?? '';
        $appUrl = rtrim($_POST['app_url'] ?? '', '/');

        // Test database connection
        try {
            if ($dbConnection === 'sqlite') {
                $sqlitePath = $basePath . '/database/database.sqlite';
                if (!file_exists($sqlitePath)) {
                    file_put_contents($sqlitePath, '');
                }
                $pdo = new PDO("sqlite:{$sqlitePath}");
            } else {
                $dsn = "{$dbConnection}:host={$dbHost};port={$dbPort};dbname={$dbDatabase}";
                $pdo = new PDO($dsn, $dbUsername, $dbPassword);
            }
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $error = "Database connection failed: " . $e->getMessage();
            $step = 3;
            goto render;
        }

        // Generate app key
        $appKey = 'base64:' . base64_encode(random_bytes(32));

        // Write .env file
        $envContent = "APP_NAME=ExamJe
APP_ENV=production
APP_KEY={$appKey}
APP_DEBUG=false
APP_URL={$appUrl}

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION={$dbConnection}
" . ($dbConnection !== 'sqlite' ? "DB_HOST={$dbHost}
DB_PORT={$dbPort}
DB_DATABASE={$dbDatabase}
DB_USERNAME={$dbUsername}
DB_PASSWORD={$dbPassword}
" : "") . "
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS=\"noreply@examje.com\"
MAIL_FROM_NAME=\"\${APP_NAME}\"

VITE_APP_NAME=\"\${APP_NAME}\"
";

        if (!file_put_contents($basePath . '/.env', $envContent)) {
            $error = "Failed to write .env file. Check that the root directory is writable.";
            $step = 3;
            goto render;
        }

        header('Location: install.php?step=4');
        exit;
    }

    // Step 4: Create admin account & run migrations
    if ($step === 4) {
        $adminName = trim($_POST['admin_name'] ?? '');
        $adminEmail = trim($_POST['admin_email'] ?? '');
        $adminPassword = $_POST['admin_password'] ?? '';
        $adminPasswordConfirm = $_POST['admin_password_confirm'] ?? '';

        if (empty($adminName) || empty($adminEmail) || empty($adminPassword)) {
            $error = "All fields are required.";
            $step = 4;
            goto render;
        }

        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
            $step = 4;
            goto render;
        }

        if (strlen($adminPassword) < 8) {
            $error = "Password must be at least 8 characters.";
            $step = 4;
            goto render;
        }

        if ($adminPassword !== $adminPasswordConfirm) {
            $error = "Passwords do not match.";
            $step = 4;
            goto render;
        }

        try {
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }

            $app = require $basePath . '/bootstrap/app.php';
            $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
            $kernel->bootstrap();

            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);

            \App\Models\User::create([
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => \Illuminate\Support\Facades\Hash::make($adminPassword),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            try {
                \Illuminate\Support\Facades\Artisan::call('storage:link');
            } catch (\Exception $e) {
                // Non-critical
            }

            file_put_contents($basePath . '/storage/.installed', date('Y-m-d H:i:s'));

            header('Location: install.php?step=5');
            exit;

        } catch (\Exception $e) {
            $error = "Installation failed: " . $e->getMessage();
            $step = 4;
            goto render;
        }
    }
}

// =============================================
// Requirements data (used on step 2)
// =============================================

$requirements = [];

$requirements[] = [
    'name' => 'PHP Version',
    'required' => '>= 8.2',
    'current' => PHP_VERSION,
    'pass' => version_compare(PHP_VERSION, '8.2.0', '>='),
];

$extensions = ['pdo', 'mbstring', 'openssl', 'tokenizer', 'json', 'ctype', 'fileinfo'];
foreach ($extensions as $ext) {
    $requirements[] = [
        'name' => "ext-{$ext}",
        'required' => 'Required',
        'current' => extension_loaded($ext) ? 'Installed' : 'Missing',
        'pass' => extension_loaded($ext),
    ];
}

$pdoDrivers = PDO::getAvailableDrivers();
$requirements[] = [
    'name' => 'PDO Drivers',
    'required' => 'mysql or sqlite',
    'current' => implode(', ', $pdoDrivers) ?: 'None',
    'pass' => count($pdoDrivers) > 0,
];

$writableDirs = ['.env file (root)', 'storage/', 'bootstrap/cache/'];
$writablePaths = [$basePath, $basePath . '/storage', $basePath . '/bootstrap/cache'];
foreach ($writableDirs as $i => $dir) {
    $writable = is_writable($writablePaths[$i]);
    $requirements[] = [
        'name' => "Writable: {$dir}",
        'required' => 'Writable',
        'current' => $writable ? 'Writable' : 'Not writable',
        'pass' => $writable,
    ];
}

$vendorInstalled = file_exists($basePath . '/vendor/autoload.php');
$requirements[] = [
    'name' => 'Composer Dependencies',
    'required' => 'Installed',
    'current' => $vendorInstalled ? 'Installed' : 'Not installed',
    'pass' => $vendorInstalled,
];

$assetsBuilt = file_exists($basePath . '/public/build/manifest.json');
$requirements[] = [
    'name' => 'Frontend Assets',
    'required' => 'Built',
    'current' => $assetsBuilt ? 'Built' : 'Not built',
    'pass' => $assetsBuilt,
];

$allPassed = !in_array(false, array_column($requirements, 'pass'));

$totalSteps = 5;
$stepNames = ['Guide', 'Requirements', 'Database', 'Admin', 'Complete'];

render:
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install ExamJe</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }
        .installer {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 640px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.4s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .installer-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }
        .installer-header img { height: 64px; margin-bottom: 0.75rem; }
        .installer-header h1 { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem; }
        .installer-header p { font-size: 0.8125rem; opacity: 0.8; }

        .steps { display: flex; justify-content: center; gap: 0.5rem; padding: 1.5rem 2rem 0; }
        .step-dot { width: 10px; height: 10px; border-radius: 50%; background: #e2e8f0; transition: all 0.3s; }
        .step-dot.active { background: #667eea; transform: scale(1.3); }
        .step-dot.done { background: #10b981; }

        .step-labels { display: flex; justify-content: center; gap: 1.25rem; padding: 0.5rem 2rem 1rem; font-size: 0.6875rem; color: #94a3b8; }
        .step-labels span.active { color: #667eea; font-weight: 600; }
        .step-labels span.done { color: #10b981; }

        .installer-body { padding: 0 2rem 2rem; }

        .alert { padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; margin-bottom: 1.25rem; }
        .alert-error { background: #fee2e2; color: #991b1b; }

        .req-table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; }
        .req-table th { text-align: left; padding: 0.5rem; font-size: 0.75rem; color: #64748b; border-bottom: 2px solid #e2e8f0; text-transform: uppercase; letter-spacing: 0.5px; }
        .req-table td { padding: 0.5rem; font-size: 0.8125rem; border-bottom: 1px solid #f1f5f9; }
        .badge-pass { display: inline-block; padding: 0.125rem 0.5rem; background: #d1fae5; color: #065f46; border-radius: 1rem; font-size: 0.6875rem; font-weight: 600; }
        .badge-fail { display: inline-block; padding: 0.125rem 0.5rem; background: #fee2e2; color: #991b1b; border-radius: 1rem; font-size: 0.6875rem; font-weight: 600; }

        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-weight: 600; font-size: 0.8125rem; color: #334155; margin-bottom: 0.375rem; }
        .form-label small { font-weight: 400; color: #94a3b8; }
        .form-control { width: 100%; padding: 0.625rem 0.875rem; border: 2px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.875rem; font-family: 'Inter', sans-serif; transition: border-color 0.2s; }
        .form-control:focus { outline: none; border-color: #667eea; }
        select.form-control { appearance: auto; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.5rem; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.9375rem; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.2s; width: 100%; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(102,126,234,0.4); }
        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
        .btn-secondary { background: #e2e8f0; color: #475569; }
        .btn-group { display: flex; gap: 0.75rem; margin-top: 1.5rem; }

        .success-icon { width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: white; }
        .info-box { background: #f8fafc; border-radius: 0.75rem; padding: 1.25rem; margin: 1.5rem 0; }
        .info-row { display: flex; justify-content: space-between; padding: 0.375rem 0; font-size: 0.8125rem; }
        .info-row span:first-child { color: #64748b; }
        .info-row span:last-child { font-weight: 600; color: #1e293b; }
        .warning-text { background: #fef3c7; color: #92400e; padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem; margin-top: 1rem; }

        .db-fields { display: none; }
        .db-fields.active { display: block; }

        /* Guide page */
        .guide-section { margin-bottom: 1.5rem; }
        .guide-section h3 { font-size: 0.9375rem; font-weight: 700; color: #1e293b; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; }
        .guide-section h3 .num { width: 24px; height: 24px; background: #667eea; color: white; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; flex-shrink: 0; }

        .guide-card { background: #f8fafc; border-radius: 0.75rem; padding: 1rem 1.25rem; margin-bottom: 0.75rem; border-left: 3px solid #667eea; }
        .guide-card h4 { font-size: 0.8125rem; font-weight: 700; color: #334155; margin-bottom: 0.375rem; }
        .guide-card p { font-size: 0.75rem; color: #64748b; line-height: 1.5; }
        .guide-card code { background: #1e293b; color: #e2e8f0; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.6875rem; }

        .guide-tabs { display: flex; gap: 0.5rem; margin-bottom: 1rem; flex-wrap: wrap; }
        .guide-tab { padding: 0.375rem 0.875rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; border: 2px solid #e2e8f0; background: white; color: #64748b; font-family: 'Inter', sans-serif; transition: all 0.2s; }
        .guide-tab.active { border-color: #667eea; background: #eef2ff; color: #667eea; }
        .guide-content { display: none; }
        .guide-content.active { display: block; }

        .command-box { background: #1e293b; color: #e2e8f0; padding: 0.75rem 1rem; border-radius: 0.5rem; font-family: monospace; font-size: 0.75rem; margin: 0.5rem 0; overflow-x: auto; line-height: 1.6; }

        @media (max-width: 640px) {
            body { padding: 1rem 0.5rem; align-items: flex-start; padding-top: 1.5rem; }
            .installer-body { padding: 0 1.25rem 1.5rem; }
            .form-row { grid-template-columns: 1fr; }
            .step-labels { gap: 0.75rem; font-size: 0.5625rem; }
            .guide-tabs { gap: 0.375rem; }
        }
    </style>
</head>
<body>
<div class="installer">
    <div class="installer-header">
        <?php if (file_exists(__DIR__ . '/images/logo.png')): ?>
            <img src="images/logo.png" alt="ExamJe">
        <?php endif; ?>
        <h1>ExamJe Installer</h1>
        <p>v2.0.0</p>
    </div>

    <!-- Steps -->
    <div class="steps">
        <?php for ($i = 0; $i < $totalSteps; $i++): ?>
            <div class="step-dot <?= $step === $i ? 'active' : ($step > $i ? 'done' : '') ?>"></div>
        <?php endfor; ?>
    </div>
    <div class="step-labels">
        <?php foreach ($stepNames as $i => $name): ?>
            <span class="<?= $step === $i ? 'active' : ($step > $i ? 'done' : '') ?>"><?= $name ?></span>
        <?php endforeach; ?>
    </div>

    <div class="installer-body">

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($step === 0): ?>
    <!-- ==================== STEP 0: Installation Guide ==================== -->

    <div class="guide-section">
        <h3>Welcome to ExamJe</h3>
        <p style="font-size: 0.8125rem; color: #64748b; margin-bottom: 1rem;">
            Before you begin, make sure you have completed the preparation steps below for your hosting platform.
        </p>
    </div>

    <div class="guide-section">
        <h3><span class="num">1</span> Upload Files</h3>
        <p style="font-size: 0.8125rem; color: #64748b; margin-bottom: 0.75rem;">
            Extract the ExamJe package and upload all files to your server. The <code>public/</code> folder should be your web root (document root).
        </p>
    </div>

    <div class="guide-section">
        <h3><span class="num">2</span> Install Dependencies</h3>
        <p style="font-size: 0.8125rem; color: #64748b; margin-bottom: 0.75rem;">
            Run these commands via SSH or your hosting terminal. Choose your platform:
        </p>

        <div class="guide-tabs">
            <button class="guide-tab active" onclick="showGuide('cpanel')">cPanel</button>
            <button class="guide-tab" onclick="showGuide('stackcp')">StackCP</button>
            <button class="guide-tab" onclick="showGuide('vps')">VPS / Cloud</button>
            <button class="guide-tab" onclick="showGuide('local')">Local Dev</button>
        </div>

        <div class="guide-content active" id="guide-cpanel">
            <div class="guide-card">
                <h4>cPanel Shared Hosting</h4>
                <p>Go to <strong>cPanel &gt; Terminal</strong> or use SSH access:</p>
                <div class="command-box">cd ~/public_html<br>composer install --optimize-autoloader --no-dev</div>
                <p style="margin-top: 0.5rem;">If your hosting has Node.js support (via cPanel &gt; Setup Node.js App):</p>
                <div class="command-box">npm install &amp;&amp; npm run build</div>
                <p style="margin-top: 0.5rem;">If no Node.js: run <code>npm run build</code> locally and upload the <code>public/build/</code> folder.</p>
            </div>
            <div class="guide-card">
                <h4>Document Root</h4>
                <p>Set your domain's document root to the <code>public</code> folder. In cPanel: <strong>Domains &gt; your domain &gt; Document Root</strong> &rarr; <code>/public_html/public</code></p>
            </div>
            <div class="guide-card">
                <h4>Create Database</h4>
                <p>Go to <strong>cPanel &gt; MySQL Databases</strong>. Create a new database and user, then assign the user to the database with all privileges.</p>
            </div>
        </div>

        <div class="guide-content" id="guide-stackcp">
            <div class="guide-card">
                <h4>StackCP Shared Hosting</h4>
                <p>Use <strong>StackCP &gt; Terminal</strong> or Git deployment:</p>
                <div class="command-box">cd ~/public_html<br>composer install --optimize-autoloader --no-dev</div>
                <p style="margin-top: 0.5rem;">Build assets locally and upload <code>public/build/</code> folder, or if Node.js is available:</p>
                <div class="command-box">npm install &amp;&amp; npm run build</div>
            </div>
            <div class="guide-card">
                <h4>Document Root</h4>
                <p>Point your domain to the <code>public</code> subfolder via StackCP site settings.</p>
            </div>
            <div class="guide-card">
                <h4>Create Database</h4>
                <p>Go to <strong>StackCP &gt; MariaDB Databases</strong>. Create a database and note the credentials.</p>
            </div>
        </div>

        <div class="guide-content" id="guide-vps">
            <div class="guide-card">
                <h4>VPS / Cloud Server</h4>
                <p>SSH into your server and navigate to your project directory:</p>
                <div class="command-box">cd /var/www/examje<br>composer install --optimize-autoloader --no-dev<br>npm install &amp;&amp; npm run build<br>chown -R www-data:www-data storage bootstrap/cache<br>chmod -R 775 storage bootstrap/cache</div>
            </div>
            <div class="guide-card">
                <h4>Nginx Config</h4>
                <p>Set <code>root</code> to <code>/var/www/examje/public</code> in your Nginx server block. Ensure <code>try_files $uri $uri/ /index.php?$query_string;</code> is set.</p>
            </div>
            <div class="guide-card">
                <h4>Create Database</h4>
                <div class="command-box">mysql -u root -p<br>CREATE DATABASE examje;<br>CREATE USER 'examje'@'localhost' IDENTIFIED BY 'your_password';<br>GRANT ALL ON examje.* TO 'examje'@'localhost';<br>FLUSH PRIVILEGES;</div>
            </div>
        </div>

        <div class="guide-content" id="guide-local">
            <div class="guide-card">
                <h4>Local Development</h4>
                <p>Clone the repo and install dependencies:</p>
                <div class="command-box">git clone https://github.com/azfarmiskam/exam-azfarmiskam.git<br>cd exam-azfarmiskam<br>composer install<br>npm install &amp;&amp; npm run build</div>
                <p style="margin-top: 0.5rem;">Then start the dev server:</p>
                <div class="command-box">php artisan serve</div>
                <p style="margin-top: 0.5rem;">Visit <code>http://localhost:8000/install.php</code></p>
            </div>
        </div>
    </div>

    <div class="guide-section">
        <h3><span class="num">3</span> Run This Installer</h3>
        <p style="font-size: 0.8125rem; color: #64748b;">
            Once files are uploaded and dependencies installed, click the button below to continue with the automated setup.
        </p>
    </div>

    <a href="install.php?step=1" class="btn btn-primary">Start Installation &rarr;</a>

    <script>
        function showGuide(id) {
            document.querySelectorAll('.guide-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.guide-tab').forEach(el => el.classList.remove('active'));
            document.getElementById('guide-' + id).classList.add('active');
            event.target.classList.add('active');
        }
    </script>

    <?php elseif ($step === 1): ?>
    <!-- ==================== STEP 1: Requirements ==================== -->
    <table class="req-table">
        <thead>
            <tr><th>Requirement</th><th>Required</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php foreach ($requirements as $req): ?>
            <tr>
                <td><?= htmlspecialchars($req['name']) ?></td>
                <td style="color: #64748b;"><?= htmlspecialchars($req['required']) ?></td>
                <td>
                    <span class="<?= $req['pass'] ? 'badge-pass' : 'badge-fail' ?>">
                        <?= htmlspecialchars($req['current']) ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="btn-group">
        <a href="install.php?step=0" class="btn btn-secondary">&larr; Back</a>
        <?php if ($allPassed): ?>
            <a href="install.php?step=2" class="btn btn-primary">Continue &rarr;</a>
        <?php else: ?>
            <a href="install.php?step=1" class="btn btn-secondary">Re-check</a>
        <?php endif; ?>
    </div>

    <?php if (!$allPassed): ?>
        <div class="alert alert-error" style="margin-top: 1rem;">Please fix the failed requirements, then click Re-check.</div>
    <?php endif; ?>

    <?php elseif ($step === 2): ?>
    <!-- ==================== STEP 2: Database ==================== -->
    <form method="POST" action="install.php?step=3">
        <div class="form-group">
            <label class="form-label">Application URL</label>
            <input type="url" name="app_url" class="form-control" placeholder="https://yourdomain.com"
                   value="<?= htmlspecialchars(($_POST['app_url'] ?? (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'])) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Database Type</label>
            <select name="db_connection" class="form-control" id="dbType" onchange="toggleDbFields()">
                <option value="mysql">MySQL / MariaDB</option>
                <option value="pgsql">PostgreSQL</option>
                <option value="sqlite">SQLite (local only)</option>
            </select>
        </div>

        <div id="dbServerFields" class="db-fields active">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Database Host</label>
                    <input type="text" name="db_host" class="form-control" value="<?= htmlspecialchars($_POST['db_host'] ?? '127.0.0.1') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Port</label>
                    <input type="text" name="db_port" class="form-control" id="dbPort" value="<?= htmlspecialchars($_POST['db_port'] ?? '3306') ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Database Name</label>
                <input type="text" name="db_database" class="form-control" value="<?= htmlspecialchars($_POST['db_database'] ?? '') ?>" placeholder="examje">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="db_username" class="form-control" value="<?= htmlspecialchars($_POST['db_username'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="db_password" class="form-control">
                </div>
            </div>
        </div>

        <div class="btn-group">
            <a href="install.php?step=1" class="btn btn-secondary">&larr; Back</a>
            <button type="submit" class="btn btn-primary">Test &amp; Continue &rarr;</button>
        </div>
    </form>

    <script>
        function toggleDbFields() {
            const type = document.getElementById('dbType').value;
            const fields = document.getElementById('dbServerFields');
            const port = document.getElementById('dbPort');
            if (type === 'sqlite') {
                fields.classList.remove('active');
            } else {
                fields.classList.add('active');
                port.value = type === 'pgsql' ? '5432' : '3306';
            }
        }
    </script>

    <?php elseif ($step === 3): ?>
    <!-- ==================== STEP 3: Admin Account ==================== -->
    <p style="color: #64748b; font-size: 0.8125rem; margin-bottom: 1.25rem;">
        Create your administrator account. You can add more admins later from the dashboard.
    </p>

    <form method="POST" action="install.php?step=4" id="adminForm">
        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="admin_name" class="form-control" placeholder="Your name" value="<?= htmlspecialchars($_POST['admin_name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" name="admin_email" class="form-control" placeholder="admin@yourdomain.com" value="<?= htmlspecialchars($_POST['admin_email'] ?? '') ?>" required>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Password <small>(min 8 chars)</small></label>
                <input type="password" name="admin_password" class="form-control" minlength="8" required>
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="admin_password_confirm" class="form-control" minlength="8" required>
            </div>
        </div>

        <div class="btn-group">
            <a href="install.php?step=2" class="btn btn-secondary">&larr; Back</a>
            <button type="submit" class="btn btn-primary" id="installBtn">Install ExamJe</button>
        </div>
    </form>

    <script>
        document.getElementById('adminForm').addEventListener('submit', function() {
            const btn = document.getElementById('installBtn');
            btn.disabled = true;
            btn.textContent = 'Installing... please wait';
        });
    </script>

    <?php elseif ($step === 4): ?>
    <!-- ==================== STEP 4: Complete ==================== -->
    <div style="text-align: center;">
        <div class="success-icon">&#10003;</div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin-bottom: 0.5rem;">Installation Complete!</h2>
        <p style="color: #64748b; font-size: 0.9375rem;">ExamJe has been installed successfully.</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span>Application URL</span>
            <span><?= htmlspecialchars((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']) ?></span>
        </div>
        <div class="info-row">
            <span>Admin Panel</span>
            <span>/login</span>
        </div>
        <div class="info-row">
            <span>Student Portal</span>
            <span>/</span>
        </div>
    </div>

    <div class="warning-text">
        <strong>Important:</strong> Delete <strong>public/install.php</strong> now for security. Anyone who visits this URL can reinstall the system.
    </div>

    <div class="btn-group" style="margin-top: 1.5rem;">
        <a href="/login" class="btn btn-primary">Go to Admin Login &rarr;</a>
    </div>

    <?php else: ?>
        <?php header('Location: install.php?step=0'); exit; ?>
    <?php endif; ?>

    </div>
</div>
</body>
</html>
