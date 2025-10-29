<?php
/**
 * CoreSCM - API Sincronizzazione
 * Endpoint per CoreGre per sincronizzare dati
 */

// Autoload
require __DIR__ . '/../vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Load config
$appConfig = require __DIR__ . '/../config/app.php';

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: X-API-Secret, Content-Type');

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Verifica autenticazione API
$headers = getallheaders();
$apiSecret = $headers['X-API-Secret'] ?? $headers['x-api-secret'] ?? '';

if (empty($appConfig['security']['api_secret']) || $apiSecret !== $appConfig['security']['api_secret']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    logSync('UNAUTHORIZED ACCESS ATTEMPT', 'error');
    exit;
}

// Initialize database
App\Core\Database::init();

// Ottieni azione
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($action) {
        case 'health':
            // Health check
            echo json_encode([
                'success' => true,
                'server' => 'corescm-aruba',
                'timestamp' => date('Y-m-d H:i:s'),
                'version' => '1.0.0'
            ]);
            break;

        case 'get_updates':
            // Ritorna record modificati dopo un timestamp
            handleGetUpdates();
            break;

        case 'push_updates':
            // Riceve aggiornamenti da CoreGre
            handlePushUpdates();
            break;

        case 'get_stats':
            // Statistiche sincronizzazione
            handleGetStats();
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid action']);
    }

} catch (Exception $e) {
    logSync('ERROR: ' . $e->getMessage(), 'error');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// ========== HANDLERS ==========

/**
 * GET UPDATES - Ritorna record modificati dopo timestamp
 */
function handleGetUpdates()
{
    $since = $_GET['since'] ?? '1970-01-01 00:00:00';

    $tables = [
        'scm_laboratories' => App\Models\ScmLaboratory::class,
        'scm_launches' => App\Models\ScmLaunch::class,
        'scm_launch_articles' => App\Models\ScmLaunchArticle::class,
        'scm_launch_phases' => App\Models\ScmLaunchPhase::class,
        'scm_progress_tracking' => App\Models\ScmProgressTracking::class,
    ];

    $updates = [];
    $totalRecords = 0;

    foreach ($tables as $tableName => $modelClass) {
        $records = $modelClass::where('updated_at', '>', $since)->get()->toArray();
        if (!empty($records)) {
            $updates[$tableName] = $records;
            $totalRecords += count($records);
        }
    }

    logSync("GET_UPDATES: $totalRecords records sent (since: $since)", 'info');

    echo json_encode([
        'success' => true,
        'data' => $updates,
        'total_records' => $totalRecords,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

/**
 * PUSH UPDATES - Riceve aggiornamenti da CoreGre
 */
function handlePushUpdates()
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (!is_array($data)) {
        throw new Exception('Invalid data format');
    }

    $tables = [
        'scm_laboratories' => App\Models\ScmLaboratory::class,
        'scm_launches' => App\Models\ScmLaunch::class,
        'scm_launch_articles' => App\Models\ScmLaunchArticle::class,
        'scm_launch_phases' => App\Models\ScmLaunchPhase::class,
        'scm_progress_tracking' => App\Models\ScmProgressTracking::class,
        'scm_standard_phases' => App\Models\ScmStandardPhase::class,
        'scm_settings' => App\Models\ScmSettings::class,
    ];

    $totalProcessed = 0;
    $errors = [];

    foreach ($data as $tableName => $records) {
        if (!isset($tables[$tableName])) {
            continue;
        }

        $modelClass = $tables[$tableName];

        foreach ($records as $record) {
            try {
                // UPSERT: updateOrCreate
                $primaryKey = $record['id'] ?? null;

                if ($primaryKey) {
                    $modelClass::updateOrCreate(
                        ['id' => $primaryKey],
                        $record
                    );
                } else {
                    $modelClass::create($record);
                }

                $totalProcessed++;
            } catch (Exception $e) {
                $errors[] = [
                    'table' => $tableName,
                    'record_id' => $primaryKey,
                    'error' => $e->getMessage()
                ];
            }
        }
    }

    logSync("PUSH_UPDATES: $totalProcessed records processed", 'info');

    if (!empty($errors)) {
        logSync("PUSH_UPDATES ERRORS: " . json_encode($errors), 'error');
    }

    echo json_encode([
        'success' => true,
        'message' => 'Updates applied',
        'processed' => $totalProcessed,
        'errors' => $errors,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

/**
 * GET STATS - Statistiche database
 */
function handleGetStats()
{
    $stats = [
        'laboratories' => App\Models\ScmLaboratory::count(),
        'launches' => App\Models\ScmLaunch::count(),
        'launches_active' => App\Models\ScmLaunch::where('status', 'active')->count(),
        'progress_updates' => App\Models\ScmProgressTracking::count(),
        'last_update' => App\Models\ScmProgressTracking::max('updated_at'),
    ];

    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

// ========== HELPERS ==========

/**
 * Log sincronizzazione
 */
function logSync($message, $level = 'info')
{
    $logFile = __DIR__ . '/../storage/logs/sync-' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] [$level] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
