<?php
namespace App;

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

class View
{
    public static function render(string $template, array $data = [], int $status = 200): void
    {
        $m = new Mustache_Engine([
            'loader'          => new Mustache_Loader_FilesystemLoader(__DIR__ . '/Views'),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/Views'),
        ]);

        $userId = Security::userId();
        $user = $userId ? Models\User::find($userId) : null;

        $data += [
            'loggedIn' => (bool) $user,
            'username' => $user['username'] ?? null,
            'csrf'     => Security::csrfToken(),
        ];

        http_response_code($status);
        echo $m->render($template, $data);
    }

    public static function error(int $status, string $message): void
    {
        self::render('error', ['status' => $status, 'message' => $message], $status);
    }
}