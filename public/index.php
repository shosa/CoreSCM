<?php
/**
 * CoreSCM - Entry Point
 * Modulo SCM pubblico per lavoranti/terzisti
 */

// Start session
session_start();

// Autoload
require __DIR__ . '/../vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Load helpers
require __DIR__ . '/../app/Core/helpers.php';

// Load config
$config = require __DIR__ . '/../config/app.php';

// Define constants for views
define('APP_NAME', $config['name']);
define('APP_VERSION', $config['version']);
define('VIEW_PATH', __DIR__ . '/../app/views');
define('BASE_PATH', '/scm'); // SEMPRE /scm ovunque

// Set timezone
date_default_timezone_set($config['timezone']);

// Error reporting
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Initialize database
App\Core\Database::init();

// Initialize router
$router = new App\Core\Router();

// Define routes
$router->get('/scm/', [App\Controllers\SCMController::class, 'index']);
$router->post('/scm/login', [App\Controllers\SCMController::class, 'login']);
$router->get('/scm/logout', [App\Controllers\SCMController::class, 'logout']);
$router->get('/scm/dashboard', [App\Controllers\SCMController::class, 'dashboard']);
$router->get('/scm/lavora/{id}', [App\Controllers\SCMController::class, 'workLaunch']);
$router->post('/scm/update-progress', [App\Controllers\SCMController::class, 'updateProgress']);
$router->post('/scm/update-progress-sequence/{id}', [App\Controllers\SCMController::class, 'updateProgressSequence']);
$router->post('/scm/complete-phase', [App\Controllers\SCMController::class, 'completePhase']);
$router->post('/scm/block-phase', [App\Controllers\SCMController::class, 'blockPhase']);
$router->post('/scm/unblock-phase', [App\Controllers\SCMController::class, 'unblockPhase']);

// Dispatch
try {
    $router->dispatch();
} catch (Exception $e) {
    if ($config['debug']) {
        echo '<h1>Error</h1>';
        echo '<p>' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    } else {
        http_response_code(500);
        echo '500 - Internal Server Error';
    }
}
