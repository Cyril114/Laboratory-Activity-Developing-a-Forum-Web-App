<?php
namespace App;

class Security
{
    public static function userId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf'];
    }

    public static function checkCsrf(): bool
    {
        $sent = $_POST['csrf'] ?? '';
        return is_string($sent) && !empty($_SESSION['csrf'])
            && hash_equals($_SESSION['csrf'], $sent);
    }
}
