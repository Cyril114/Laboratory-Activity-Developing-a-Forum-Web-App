<?php
namespace App\Models;

use App\Database;

class Comment
{
    public const MAX_DEPTH = 20;

    
    public static function forDiscussion(int $discussionId): array
    {
        $st = Database::get()->prepare(
            'SELECT c.id, c.parent_id, c.body, c.created_at, u.username AS author
               FROM comments c JOIN users u ON u.id = c.user_id
              WHERE c.discussion_id = ?
              ORDER BY c.created_at, c.id'
        );
        $st->execute([$discussionId]);
        return $st->fetchAll();
    }

    
    public static function depthInDiscussion(int $commentId, int $discussionId): ?int
    {
        $st = Database::get()->prepare(
            'SELECT parent_id FROM comments WHERE id = ? AND discussion_id = ?'
        );
        $depth = 0;
        $seen = [];
        $id = $commentId;
        while (true) {
            $st->execute([$id, $discussionId]);
            $row = $st->fetch();
            if (!$row || isset($seen[$id]) || $depth > self::MAX_DEPTH + 1) {
                return null; 
            }
            $seen[$id] = true;
            if ($row['parent_id'] === null) {
                return $depth;
            }
            $id = (int) $row['parent_id'];
            $depth++;
        }
    }

    public static function create(int $discussionId, ?int $parentId, int $userId, string $body): void
    {
        $st = Database::get()->prepare(
            'INSERT INTO comments (discussion_id, parent_id, user_id, body) VALUES (?, ?, ?, ?)'
        );
        $st->execute([$discussionId, $parentId, $userId, $body]);
    }
}