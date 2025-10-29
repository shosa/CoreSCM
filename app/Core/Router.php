<?php

namespace App\Core;

/**
 * Simple Router per CoreSCM
 */
class Router
{
    private $routes = [];
    private $basePath = '';

    public function __construct($basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * Aggiungi route GET
     */
    public function get($path, $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * Aggiungi route POST
     */
    public function post($path, $handler)
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * Aggiungi route
     */
    private function addRoute($method, $path, $handler)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $this->basePath . $path,
            'handler' => $handler
        ];
    }

    /**
     * Esegui routing
     */
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $uri)) {
                return $this->callHandler($route['handler'], $uri, $route['path']);
            }
        }

        // 404 Not Found
        http_response_code(404);
        echo "404 - Page Not Found";
        exit;
    }

    /**
     * Match path con parametri
     */
    private function matchPath($routePath, $uri)
    {
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        return preg_match($pattern, $uri);
    }

    /**
     * Chiama handler
     */
    private function callHandler($handler, $uri, $routePath)
    {
        // Estrai parametri dalla URI
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $routePath, $paramNames);
        $pattern = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([a-zA-Z0-9_]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        preg_match($pattern, $uri, $paramValues);
        array_shift($paramValues);

        $params = [];
        if (!empty($paramNames[1])) {
            $params = array_combine($paramNames[1], $paramValues);
        }

        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_array($handler)) {
            [$controller, $method] = $handler;
            $instance = new $controller();
            return call_user_func_array([$instance, $method], array_values($params));
        }

        throw new \Exception("Invalid handler");
    }
}
