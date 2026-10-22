<?php

declare(strict_types=1);

/**
 * --------------------------------------------------------------------------
 * Core Paths Definition
 * --------------------------------------------------------------------------
 */
if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

define('APP_PATH', ROOT . DS . 'application');
define('CONFIG_PATH', ROOT . DS . 'config');
define('PUBLIC_PATH', ROOT . DS . 'public');
define('STORAGE_PATH', ROOT . DS . 'tmp');

/**
 * --------------------------------------------------------------------------
 * Lightweight Environment (.env) Loader
 * --------------------------------------------------------------------------
 */
if (!function_exists('loadEnv')) {
    function loadEnv(string $filePath): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            // Skip comments and invalid lines
            if ($line === '' || str_starts_with($line, '#') || str_starts_with($line, ';')) {
                continue;
            }

            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $key = trim($parts[0]);
            $value = trim($parts[1]);

            // Strip enclosing quotes if present
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }

            // Convert keyword booleans/null
            $lowerVal = strtolower($value);
            $parsedValue = match ($lowerVal) {
                'true', '(true)' => true,
                'false', '(false)' => false,
                'null', '(null)' => null,
                'empty', '(empty)' => '',
                default => $value,
            };

            if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
                putenv("{$key}={$value}");
                $_ENV[$key] = $parsedValue;
                $_SERVER[$key] = $parsedValue;
            }
        }
    }
}

// Load .env if present, otherwise fallback to .env.example
if (file_exists(ROOT . DS . '.env')) {
    loadEnv(ROOT . DS . '.env');
} elseif (file_exists(ROOT . DS . '.env.example')) {
    loadEnv(ROOT . DS . '.env.example');
}

/**
 * --------------------------------------------------------------------------
 * Env Helper Function
 * --------------------------------------------------------------------------
 */
if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($value === false || $value === null) {
            return $default;
        }
        return $value;
    }
}

/**
 * --------------------------------------------------------------------------
 * Application Configuration
 * --------------------------------------------------------------------------
 */
define('APP_NAME', (string)env('APP_NAME', 'Simple PHP MVC'));
define('APP_ENV', (string)env('APP_ENV', 'development'));
define('APP_DEBUG', (bool)env('APP_DEBUG', true));
define('TIMEZONE', (string)env('TIMEZONE', 'Asia/Jakarta'));

date_default_timezone_set(TIMEZONE);

// Base URL resolution
$detectedUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
define('BASE_PATH', rtrim((string)env('APP_URL', $detectedUrl), '/'));
define('BASE_URL', BASE_PATH);

// Routing Defaults
define('DEFAULT_CONTROLLER', 'home');
define('DEFAULT_ACTION', 'index');

/**
 * --------------------------------------------------------------------------
 * Database Configuration (PDO)
 * --------------------------------------------------------------------------
 */
define('DB_CONNECTION', (string)env('DB_CONNECTION', 'mysql'));
define('DB_HOST', (string)env('DB_HOST', '127.0.0.1'));
define('DB_PORT', (string)env('DB_PORT', '3306'));
define('DB_NAME', (string)env('DB_DATABASE', 'mvc_db'));
define('DB_USER', (string)env('DB_USERNAME', 'root'));
define('DB_PASSWORD', (string)env('DB_PASSWORD', ''));
define('DB_CHARSET', (string)env('DB_CHARSET', 'utf8mb4'));
