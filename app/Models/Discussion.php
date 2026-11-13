<?php
namespace App\Models;

use App\Database;

class Discussion
{
    public static function find(int $id): ?array
    {
        $st = Database::get()->prepare(
            'SELECT d.id, d.title, d.body, d.created_at, u.username AS author
               FROM discussions d JOIN users u ON u.id = d.user_id
              WHERE d.id = ?'
        );
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }
}