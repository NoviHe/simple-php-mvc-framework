<?php

declare(strict_types=1);

/**
 * HomeController
 * Default landing controller for the framework.
 */
class HomeController extends Controller
{
    /**
     * Framework Home / Welcome Page
     */
    public function index(): void
    {
        $dbStatus = 'Not Connected';
        $dbError = null;

        try {
            $model = new Model();
            if ($model->connect() !== null) {
                $dbStatus = 'Connected (' . DB_CONNECTION . '://' . DB_HOST . ':' . DB_PORT . '/' . DB_NAME . ')';
            }
        } catch (Throwable $e) {
            $dbError = $e->getMessage();
        }

        $diagnostics = [
            'app_name' => APP_NAME,
            'app_env' => APP_ENV,
            'app_debug' => APP_DEBUG ? 'Enabled' : 'Disabled',
            'php_version' => PHP_VERSION,
            'database_status' => $dbStatus,
            'database_error' => $dbError,
            'base_url' => BASE_URL,
            'timezone' => TIMEZONE,
        ];

        $this->template('index', [
            'page_title' => APP_NAME . ' - Welcome',
            'diagnostics' => $diagnostics,
        ]);
    }

    /**
     * About page sample
     */
    public function about(): void
    {
        $this->template('about', [
            'page_title' => 'About - ' . APP_NAME,
            'description' => 'A lightweight, secure, and clean MVC framework for PHP.',
        ]);
    }

    /**
     * Example API endpoint returning JSON
     */
    public function api(): void
    {
        $this->json([
            'status' => 'success',
            'framework' => APP_NAME,
            'php_version' => PHP_VERSION,
            'timestamp' => date('c'),
            'message' => 'Simple PHP MVC Framework API is operational.',
        ]);
    }

    /**
     * Error page action
     */
    public function error(int $code = 404): void
    {
        http_response_code($code);
        $this->template('404', [
            'page_title' => "Error {$code} - Page Not Found",
            'code' => $code,
        ]);
    }
}
