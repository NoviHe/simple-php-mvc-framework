<?php

declare(strict_types=1);

/**
 * --------------------------------------------------------------------------
 * Front Controller Entry Point
 * --------------------------------------------------------------------------
 */

// Configure and start secure session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1')) {
        ini_set('session.cookie_secure', '1');
    }
    session_start();
}

// Define Root directory constants
define('ROOT', dirname(__DIR__));
define('DS', DIRECTORY_SEPARATOR);

// Load Configuration and Core Framework Files
require_once ROOT . '/config/config.php';
require_once ROOT . '/library/security.class.php';
require_once ROOT . '/application/functions/helpers.php';
require_once ROOT . '/library/controller.class.php';
require_once ROOT . '/library/model.class.php';
require_once ROOT . '/library/view.class.php';

// Class Autoloader
spl_autoload_register(function (string $className): void {
    $className = ltrim($className, '\\');
    $paths = [
        APP_PATH . DS . 'controllers' . DS . $className . '.php',
        APP_PATH . DS . 'models' . DS . strtolower(str_replace('Model', '', $className)) . '.model.php',
        ROOT . DS . str_replace('\\', DS, $className) . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Configure Error Reporting
function setErrorReporting(): void
{
    if (defined('APP_DEBUG') && APP_DEBUG === true) {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
    } else {
        error_reporting(E_ALL);
        ini_set('display_errors', '0');
        ini_set('log_errors', '1');

        $logDir = STORAGE_PATH . DS . 'log';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        ini_set('error_log', $logDir . DS . 'error.log');
    }
}

// Route and Dispatch Request
function dispatchRouter(): void
{
    $rawUrl = isset($_GET['url']) && is_string($_GET['url']) ? trim($_GET['url'], '/') : '';
    $urlSegments = $rawUrl !== '' ? explode('/', $rawUrl) : [];

    $controllerName = !empty($urlSegments[0]) ? strtolower($urlSegments[0]) : DEFAULT_CONTROLLER;
    $controllerClass = ucfirst($controllerName) . 'Controller';
    $controllerFile = APP_PATH . DS . 'controllers' . DS . $controllerClass . '.php';

    if (!file_exists($controllerFile)) {
        renderNotFound("Controller '{$controllerClass}' not found.");
        return;
    }

    require_once $controllerFile;

    if (!class_exists($controllerClass)) {
        renderNotFound("Controller class '{$controllerClass}' not defined.");
        return;
    }

    array_shift($urlSegments);
    $actionName = !empty($urlSegments[0]) ? $urlSegments[0] : DEFAULT_ACTION;
    array_shift($urlSegments);
    $parameters = $urlSegments;

    $controllerInstance = new $controllerClass();

    if (!method_exists($controllerInstance, $actionName)) {
        renderNotFound("Action '{$actionName}' not found on '{$controllerClass}'.");
        return;
    }

    try {
        call_user_func_array([$controllerInstance, $actionName], $parameters);
    } catch (Throwable $e) {
        handleException($e);
    }
}

function renderNotFound(string $message = 'Page Not Found'): void
{
    http_response_code(404);
    $view404 = APP_PATH . DS . 'views' . DS . '404.view.php';
    if (file_exists($view404)) {
        $errorMessage = $message;
        require $view404;
    } else {
        echo "<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1><p>"
            . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "</p></body></html>";
    }
    exit;
}

function handleException(Throwable $e): void
{
    http_response_code(500);

    if (defined('APP_DEBUG') && APP_DEBUG === true) {
        echo "<div style='font-family:sans-serif; background:#fff3f3; color:#b30000; padding:20px; border:2px solid #ffcccc; margin:20px; border-radius:8px;'>";
        echo "<h2>Application Error</h2>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8') . " : line " . $e->getLine() . "</p>";
        echo "<h3>Stack Trace:</h3>";
        echo "<pre style='background:#fbe6e6; padding:15px; border-radius:4px; overflow-x:auto;'>" . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . "</pre>";
        echo "</div>";
    } else {
        echo "<!DOCTYPE html><html><head><title>500 Internal Error</title></head><body><h1>500 Internal Server Error</h1><p>An unexpected error occurred. Please try again later.</p></body></html>";
    }
    exit;
}

setErrorReporting();
dispatchRouter();
