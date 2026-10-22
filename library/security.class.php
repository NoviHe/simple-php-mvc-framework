<?php

declare(strict_types=1);

/**
 * Security Class
 * Provides CSRF protection, password hashing, and input sanitization.
 */
class Security
{
    private const CSRF_TOKEN_KEY = '_csrf_token';

    /**
     * Get active CSRF token or generate a new one if not present
     */
    public static function csrfToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }

        if (empty($_SESSION[self::CSRF_TOKEN_KEY])) {
            $_SESSION[self::CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::CSRF_TOKEN_KEY];
    }

    /**
     * Render hidden HTML input containing CSRF token
     */
    public static function csrfField(): string
    {
        $token = htmlspecialchars(self::csrfToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Validate submitted CSRF token
     */
    public static function verifyCsrfToken(?string $token = null): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }

        $sessionToken = $_SESSION[self::CSRF_TOKEN_KEY] ?? '';
        if (empty($sessionToken)) {
            return false;
        }

        $submitted = $token ?? $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!is_string($submitted)) {
            return false;
        }

        return hash_equals($sessionToken, $submitted);
    }

    /**
     * Regenerate CSRF token (useful after login/logout)
     */
    public static function regenerateCsrfToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE && !headers_sent()) {
            session_start();
        }

        $_SESSION[self::CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
        return $_SESSION[self::CSRF_TOKEN_KEY];
    }

    /**
     * Escape HTML output (XSS protection)
     */
    public static function escape(?string $value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Securely hash password using modern standard (BCRYPT / Argon2)
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Verify plain password against hash
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Generate URL slug from title/string
     */
    public static function slugify(string $text, string $divider = '-'): string
    {
        // Replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text ?? '');
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', (string)$text);
        // Trim
        $text = trim((string)$text, $divider);
        // Remove duplicate divider
        $text = preg_replace('~-+~', $divider, (string)$text);
        // Lowercase
        $text = strtolower((string)$text);

        return empty($text) ? 'n-a' : $text;
    }

    /**
     * Sanitize general string input
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim(strip_tags($input)), ENT_QUOTES, 'UTF-8');
    }
}
