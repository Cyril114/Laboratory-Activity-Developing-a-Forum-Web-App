<?php
namespace App\Models;

use App\Database;

class User
{
    public static function exists(string $username, string $email): bool
    {
        $st = Database::get()->prepare('SELECT 1 FROM users WHERE username = ? OR email = ?');
        $st->execute([$username, $email]);
        return (bool) $st->fetchColumn();
    }

    public static function create(string $username, string $email, string $password): void
    {
        $st = Database::get()->prepare(
            'INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)'
        );
        $st->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
    }

    
    public static function verify(string $username, string $password): ?int
    {
        $st = Database::get()->prepare('SELECT id, password_hash FROM users WHERE username = ?');
        $st->execute([$username]);
        $u = $st->fetch();
        if (!$u || $u['password_hash'] === null) {
            return null; 
        }
        return password_verify($password, $u['password_hash']) ? (int) $u['id'] : null;
    }

    public static function find(int $id): ?array
    {
        $st = Database::get()->prepare('SELECT id, username FROM users WHERE id = ?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }
}