<?php

declare(strict_types=1);

/**
 * Base Controller Class
 * Handles views, templates, models, redirects, responses, and user inputs.
 */
#[\AllowDynamicProperties]
class Controller
{
    /**
     * Create and return a View instance
     */
    protected function view(string $viewName, array $data = []): View
    {
        return new View($viewName, $data);
    }

    /**
     * Render view wrapped inside a layout template
     */
    protected function template(string $viewName, array $data = [], string $layout = 'template'): View
    {
        $view = $this->view($layout);
        $view->bind('viewName', $viewName);
        $view->bind('data', $data);

        // Bind title & description defaults if provided
        $view->bind('page_title', $data['page_title'] ?? APP_NAME);
        $view->bind('page_description', $data['page_description'] ?? 'Simple PHP MVC Framework Application');

        return $view;
    }

    /**
     * Load model instance dynamically and attach to controller property
     */
    protected function model(string $modelName): Model
    {
        $modelName = strtolower($modelName);
        $modelPath = APP_PATH . DS . 'models' . DS . $modelName . '.model.php';

        if (file_exists($modelPath)) {
            require_once $modelPath;
        }

        $className = ucfirst($modelName) . "Model";
        if (class_exists($className)) {
            $instance = new $className();
            $this->$modelName = $instance;
            return $instance;
        }

        throw new RuntimeException("Model class '{$className}' not found at: {$modelPath}");
    }

    /**
     * Return JSON response
     */
    public function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    /**
     * Redirect to a given URL
     */
    public function redirect(string $url = '', int $statusCode = 302): void
    {
        $target = str_starts_with($url, 'http://') || str_starts_with($url, 'https://')
            ? $url
            : base_url($url);

        http_response_code($statusCode);
        header('Location: ' . $target);
        exit;
    }

    /**
     * Redirect to the previous page
     */
    public function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? base_url();
        header('Location: ' . $referer);
        exit;
    }

    /**
     * Safely get input from GET, POST, or JSON body
     */
    public function input(?string $key = null, mixed $default = null): mixed
    {
        static $jsonInput = null;

        if ($jsonInput === null) {
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (str_contains($contentType, 'application/json')) {
                $raw = file_get_contents('php://input');
                $jsonInput = is_string($raw) ? json_decode($raw, true) : [];
            } else {
                $jsonInput = [];
            }
        }

        $allInputs = array_merge($_GET, $_POST, is_array($jsonInput) ? $jsonInput : []);

        if ($key === null) {
            return $allInputs;
        }

        return $allInputs[$key] ?? $default;
    }

    /**
     * Check if current request method is POST
     */
    public function isPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /**
     * Check if current request method is GET
     */
    public function isGet(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET';
    }

    /**
     * Check if current request is AJAX
     */
    public function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Sanitize string data
     */
    public function validate(mixed $data): string
    {
        if ($data === null) {
            return '';
        }
        return htmlspecialchars(trim(strip_tags((string)$data)), ENT_QUOTES, 'UTF-8');
    }
}
