<?php
namespace src\Utils;

class SessionUtils
{
    public static function setSession(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function unsetSession(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function fromSession(string $key): mixed
    {
        return $_SESSION[$key] ?? '';
    }

    public static function fromServer(string $field): string
    {
        // Sanitize
        if (isset($_SERVER[$field])) {
            $strSanitized = htmlentities((string) $_SERVER[$field], ENT_QUOTES, 'UTF-8');
        } else {
            $strSanitized = '';
        }
        return filter_var($strSanitized, FILTER_SANITIZE_URL);
    }

    public static function isPostSubmitted(): bool
    {
        return isset($_POST) && !empty($_POST);
    }
    
    public static function fromPost(string $field, $default = '', bool $sanitize=false): string
    {
        $strSanitized = isset($_POST[$field]) ? htmlentities((string) $_POST[$field], ENT_QUOTES, 'UTF-8') : $default;
        return $sanitize ? filter_var($strSanitized, FILTER_SANITIZE_URL) : $strSanitized;
    }

    public static function fromGet(string $field, $default = '', bool $sanitize=false): string
    {
        $strSanitized = isset($_GET[$field]) ? htmlentities((string) $_GET[$field], ENT_QUOTES, 'UTF-8') : $default;
        return $sanitize ? filter_var($strSanitized, FILTER_SANITIZE_URL) : $strSanitized;
    }
}
