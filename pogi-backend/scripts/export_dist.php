<?php
/**
 * POGI SKP Tracker - Static Dist Exporter
 * Renders Laravel Blade views with live database context into static HTML pages for Vercel deployment.
 * Eliminates the need for any legacy prototype files.
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$backendDir = dirname(__DIR__);
$distDir = $backendDir . '/dist';
$publicDir = $backendDir . '/public';

echo "[export_dist] Exporting Blade views to $distDir...\n";

if (!is_dir($distDir)) {
    mkdir($distDir, 0755, true);
}

// 1. Copy public assets (css, js, assets, etc.)
function copyRecursive($src, $dst) {
    if (!file_exists($src)) return;
    if (is_dir($src)) {
        if (!is_dir($dst)) mkdir($dst, 0755, true);
        $files = scandir($src);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            copyRecursive("$src/$file", "$dst/$file");
        }
    } else {
        copy($src, $dst);
    }
}

$entries = scandir($publicDir);
foreach ($entries as $e) {
    if (in_array($e, ['.', '..', 'index.php', '.htaccess'])) continue;
    copyRecursive("$publicDir/$e", "$distDir/$e");
}
echo "[export_dist] Public assets copied.\n";

// 2. Set Super Admin session for full data access
$user = App\Models\User::where('role', 'SUPER_ADMIN')->first() ?? App\Models\User::first() ?? new App\Models\User([
    'id' => 'auth-user',
    'full_name' => 'Dian Ratnasari',
    'email' => 'super@example.com',
    'role' => 'SUPER_ADMIN',
    'branch' => 'Pusat'
]);
Auth::login($user);

// 3. Define view render routes
$pages = [
    'dashboard' => [App\Http\Controllers\DashboardController::class, 'index'],
    'doctors' => [App\Http\Controllers\DoctorController::class, 'index'],
    'doctor-detail' => [App\Http\Controllers\DoctorController::class, 'show'],
    'events' => [App\Http\Controllers\EventController::class, 'index'],
    'activity' => [App\Http\Controllers\ActivityController::class, 'index'],
    'provinces' => [App\Http\Controllers\ProvinceController::class, 'index'],
    'reports' => [App\Http\Controllers\ReportController::class, 'index'],
    'admin-performance' => [App\Http\Controllers\AdminPerformanceController::class, 'index'],
    'manage' => [App\Http\Controllers\ManageController::class, 'index'],
];

// 4. Render Login view specifically (logout temporarily, then re-login)
try {
    Auth::logout();
    $loginHtml = view('login', ['errors' => new Illuminate\Support\ViewErrorBag()])->render();
    $loginHtml = preg_replace('#https?://(?:localhost|127\.0\.0\.1)(?::\d+)?/#', '/', $loginHtml);
    file_put_contents("$distDir/login.html", $loginHtml);
    echo "  [OK] login.html (" . strlen($loginHtml) . " bytes)\n";
} catch (Throwable $e) {
    echo "  [FAIL] login: " . $e->getMessage() . "\n";
}

// Re-login Super Admin after logout (critical: without this, AUTH_USER.name = 'Pengguna')
Auth::login($user);

$req = Illuminate\Http\Request::create('/', 'GET');

foreach ($pages as $slug => [$ctrlClass, $method]) {
    try {
        $controller = app($ctrlClass);
        $result = $controller->$method($req);
        $html = $result->render();

        // Fix asset URLs to be root-relative for static hosting (e.g. Vercel)
        $html = preg_replace('#https?://(?:localhost|127\.0\.0\.1)(?::\d+)?/#', '/', $html);

        file_put_contents("$distDir/$slug.html", $html);
        echo "  [OK] $slug.html (" . strlen($html) . " bytes)\n";

        if ($slug === 'dashboard') {
            file_put_contents("$distDir/index.html", $html);
            echo "  [OK] index.html (dashboard copy)\n";
        }
    } catch (Throwable $e) {
        echo "  [FAIL] $slug: " . $e->getMessage() . "\n";
    }
}

echo "[export_dist] Export completed successfully!\n";
