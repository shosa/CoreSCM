<?php
/**
 * Base Controller for CoreSCM
 * Classe base per tutti i controller dell'applicazione CoreSCM
 */

namespace App\Core;

abstract class BaseController
{
    public function __construct()
    {
        $this->initializeSession();
    }

    /**
     * Inizializza la sessione se non già attiva
     */
    private function initializeSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Renderizza una view standalone
     */
    protected function view($viewName, $data = [])
    {
        // Estrae le variabili per renderle disponibili nella view
        $thisurl = 'url'; // backward compatibility
        extract($data);

        // Percorso completo della view
        $viewPath = (defined('VIEW_PATH') ? VIEW_PATH : __DIR__ . '/../../app/views') . '/' . str_replace('.', '/', $viewName) . '.php';

        if (!file_exists($viewPath)) {
            throw new \Exception("View {$viewName} not found at {$viewPath}");
        }

        // Include la view
        include $viewPath;
    }

    /**
     * Ritorna JSON response
     */
    protected function json($data, $statusCode = 200)
    {
        // Pulisci qualsiasi output precedente
        if (ob_get_level()) {
            ob_clean();
        }

        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Reindirizza a un URL
     */
    protected function redirect($url, $statusCode = 302)
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Reindirizza alla route precedente o a una route di default
     */
    protected function redirectBack($default = '/')
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? $default;
        $this->redirect($referer);
    }

    /**
     * Verifica se la richiesta è AJAX
     */
    protected function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * Verifica se la richiesta è POST
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Verifica se la richiesta è GET
     */
    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Imposta messaggio flash
     */
    protected function setFlash($type, $message)
    {
        $_SESSION['alert_' . $type] = $message;
    }

    /**
     * Ottiene messaggio flash
     */
    protected function getFlash($type)
    {
        $message = $_SESSION['alert_' . $type] ?? null;
        unset($_SESSION['alert_' . $type]);
        return $message;
    }

    /**
     * Sanifica input
     */
    protected function sanitize($input)
    {
        if (is_string($input)) {
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        } elseif (is_array($input)) {
            return array_map([$this, 'sanitize'], $input);
        }
        return $input;
    }

    /**
     * Ottiene l'input della richiesta
     */
    protected function input($key = null, $default = null)
    {
        $input = array_merge($_GET, $_POST);

        if ($key === null) {
            return $this->sanitize($input);
        }

        return $this->sanitize($input[$key] ?? $default);
    }

    /**
     * Genera URL utilizzando la configurazione dell'applicazione
     */
    protected function url($path = '', $params = [])
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

    /**
     * Formatta il tempo trascorso in formato "tempo fa"
     */
    protected function timeAgo($datetime)
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

    /**
     * Formatta data italiana
     */
    protected function formatDateIta($date)
    {
        if (empty($date)) return '-';

        $timestamp = is_numeric($date) ? $date : strtotime($date);
        return date('d/m/Y', $timestamp);
    }

    /**
     * Formatta data e ora italiana
     */
    protected function formatDateTimeIta($datetime)
    {
        if (empty($datetime)) return '-';

        $timestamp = is_numeric($datetime) ? $datetime : strtotime($datetime);
        return date('d/m/Y H:i', $timestamp);
    }
}
