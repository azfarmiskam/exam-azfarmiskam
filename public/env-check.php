<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>Environment Diagnostic</h1>";
echo "<pre>";
echo "APP_ENV: " . config('app.env') . "\n";
echo "APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "\nVite Manifest Path: " . public_path('build/manifest.json') . "\n";
echo "Manifest Exists: " . (file_exists(public_path('build/manifest.json')) ? 'YES' : 'NO') . "\n";

if (file_exists(public_path('build/manifest.json'))) {
    echo "\nManifest Contents:\n";
    echo file_get_contents(public_path('build/manifest.json'));
}

echo "\n\nVite::asset() test:\n";
try {
    echo "This should show build path, not localhost:5173\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
echo "</pre>";
