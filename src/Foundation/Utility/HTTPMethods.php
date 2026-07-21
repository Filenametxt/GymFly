<?php
namespace App\Foundation\Utility;

/**
 * Class HTTPMethods
 * 
 * Utility class to centralize all access to HTTP superglobals ($_GET, $_POST, $_FILES, $_SERVER, $_REQUEST)
 * and enforce decoupling between Presentation (View) and Control subsystems.
 */
class HTTPMethods
{
    /**
     * Get a parameter from $_GET array
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    /**
     * Get a parameter from $_POST array
     */
    public static function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get a string parameter from $_POST array
     */
    public static function postString(string $key, string $default = ''): string
    {
        return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
    }

    /**
     * Get an integer parameter from $_POST array
     */
    public static function postInt(string $key, int $default = 0): int
    {
        return isset($_POST[$key]) ? (int)$_POST[$key] : $default;
    }

    /**
     * Get a float parameter from $_POST array (converts empty string to null)
     */
    public static function postFloat(string $key): ?float
    {
        return (!empty($_POST[$key]) || (isset($_POST[$key]) && $_POST[$key] === '0')) ? (float)$_POST[$key] : null;
    }

    /**
     * Get a date parameter from $_POST array as \DateTimeImmutable
     */
    public static function postDate(string $key): ?\DateTimeImmutable
    {
        if (empty($_POST[$key])) {
            return null;
        }
        try {
            return new \DateTimeImmutable($_POST[$key]);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get an uploaded file array from $_FILES
     */
    public static function files(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    /**
     * Alias for files()
     */
    public static function postFile(string $key): ?array
    {
        return self::files($key);
    }

    /**
     * Get an array parameter from $_POST
     */
    public static function postArray(string $key): array
    {
        return (isset($_POST[$key]) && is_array($_POST[$key])) ? $_POST[$key] : [];
    }

    /**
     * Get a boolean parameter from $_POST
     */
    public static function postBool(string $key): bool
    {
        return isset($_POST[$key]) && ($_POST[$key] === '1' || $_POST[$key] === 'true' || $_POST[$key] === 'on' || $_POST[$key] === true);
    }

    /**
     * Check if request is AJAX
     */
    public static function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get referer header or default
     */
    public static function getReferer(string $default = 'index.php'): string
    {
        return $_SERVER['HTTP_REFERER'] ?? $default;
    }

    /**
     * Get parameter from $_REQUEST ($_POST or $_GET)
     */
    public static function request(string $key, mixed $default = null): mixed
    {
        return $_REQUEST[$key] ?? $default;
    }

    /**
     * Get HTTP request method (e.g. GET, POST)
     */
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Get server variable
     */
    public static function server(string $key, mixed $default = null): mixed
    {
        return $_SERVER[$key] ?? $default;
    }
}
