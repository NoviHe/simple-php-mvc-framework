<?php

declare(strict_types=1);

/**
 * Global Helper Functions
 */

if (!function_exists('e')) {
    /**
     * Escape HTML output (XSS Protection)
     */
    function e(?string $value): string
    {
        return Security::escape($value);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Security::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return Security::csrfField();
    }
}

if (!function_exists('csrf_verify')) {
    function csrf_verify(?string $token = null): bool
    {
        return Security::verifyCsrfToken($token);
    }
}

if (!function_exists('base_url')) {
    function base_url(string $path = ''): string
    {
        $base = defined('BASE_URL') ? BASE_URL : '';
        return rtrim($base, '/') . ($path !== '' ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return base_url($path);
    }
}

if (!function_exists('load_css')) {
    function load_css(string $path): void
    {
        echo '<link rel="stylesheet" type="text/css" href="' . asset($path) . '">' . PHP_EOL;
    }
}

if (!function_exists('load_script')) {
    function load_script(string $path): void
    {
        echo '<script type="text/javascript" src="' . asset($path) . '"></script>' . PHP_EOL;
    }
}

if (!function_exists('current_url')) {
    function current_url(): string
    {
        $isHttps = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1');
        $protocol = $isHttps ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return $protocol . $host . $uri;
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        return Security::slugify($text);
    }
}

if (!function_exists('nav_item')) {
    function nav_item(string $route, string $title): string
    {
        global $url;
        $current = trim((string)$url, '/');
        $target = trim($route, '/');
        $isActive = ($current === $target || ($target === '' && $current === 'home'));
        $activeClass = $isActive ? ' active' : '';

        return sprintf(
            '<li class="nav-item"><a class="nav-link%s" href="%s">%s</a></li>',
            $activeClass,
            base_url($route),
            e($title)
        );
    }
}
