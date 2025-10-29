<?php
/**
 * Application Configuration
 */

return [
    'name' => $_ENV['APP_NAME'] ?? 'CoreSCM',
    'version' => $_ENV['APP_VERSION'] ?? '1.0.0',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => $_ENV['APP_URL'] ?? 'http://localhost',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'Europe/Rome',
    'locale' => $_ENV['APP_LOCALE'] ?? 'it',

    'session' => [
        'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
        'name' => $_ENV['SESSION_NAME'] ?? 'CORESCM_SESSION',
        'secure' => true, // Solo HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ],

    'security' => [
        'api_secret' => $_ENV['API_SECRET'] ?? '',
        'login_max_attempts' => (int)($_ENV['LOGIN_MAX_ATTEMPTS'] ?? 10),
        'login_lockout_time' => (int)($_ENV['LOGIN_LOCKOUT_TIME'] ?? 3600),
    ],

    'logging' => [
        'level' => $_ENV['LOG_LEVEL'] ?? 'error',
        'max_files' => (int)($_ENV['LOG_MAX_FILES'] ?? 30),
        'path' => __DIR__ . '/../storage/logs'
    ]
];
