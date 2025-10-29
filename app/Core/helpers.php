<?php
/**
 * Helper functions for CoreSCM views
 */

// Define $thisurl as alias to url() for backward compatibility with views
$thisurl = 'url';

if (!function_exists('url')) {
    /**
     * Generate URL - SEMPRE con /scm/ prefix
     */
    function url($path = '', $params = [])
    {
        $basePath = defined('BASE_PATH') ? BASE_PATH : '/scm';

        if (empty($path)) {
            $url = $basePath;
        } else {
            $path = '/' . ltrim($path, '/');
            $url = $basePath . $path;
        }

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate asset URL
     */
    function asset($path)
    {
        $baseUrl = $_ENV['APP_URL'] ?? '';
        return $baseUrl . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('env')) {
    /**
     * Get environment variable
     */
    function env($key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}

if (!function_exists('config')) {
    /**
     * Get config value
     */
    function config($key, $default = null)
    {
        $config = require __DIR__ . '/../../config/app.php';

        $keys = explode('.', $key);
        $value = $config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }
}

if (!function_exists('formatDateIta')) {
    /**
     * Format date in Italian format
     */
    function formatDateIta($date)
    {
        if (empty($date)) return '-';
        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return date('d/m/Y', $timestamp);
    }
}

if (!function_exists('formatDateTimeIta')) {
    /**
     * Format datetime in Italian format
     */
    function formatDateTimeIta($datetime)
    {
        if (empty($datetime)) return '-';
        $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
        return date('d/m/Y H:i', $timestamp);
    }
}

if (!function_exists('timeAgo')) {
    /**
     * Format time as "time ago"
     */
    function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) {
            return $time == 1 ? '1 secondo fa' : $time . ' secondi fa';
        }

        $time = round($time / 60);
        if ($time < 60) {
            return $time == 1 ? '1 minuto fa' : $time . ' minuti fa';
        }

        $time = round($time / 60);
        if ($time < 24) {
            return $time == 1 ? '1 ora fa' : $time . ' ore fa';
        }

        $time = round($time / 24);
        if ($time < 30) {
            return $time == 1 ? '1 giorno fa' : $time . ' giorni fa';
        }

        $time = round($time / 30);
        if ($time < 12) {
            return $time == 1 ? '1 mese fa' : $time . ' mesi fa';
        }

        $time = round($time / 12);
        return $time == 1 ? '1 anno fa' : $time . ' anni fa';
    }
}
