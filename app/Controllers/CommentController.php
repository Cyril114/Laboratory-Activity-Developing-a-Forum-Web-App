<?php
namespace App\Controllers;

use App\Models\Comment;
use App\Models\Discussion;
use App\Security;
use App\View;

class CommentController
{
    public function store(string $id): void
    {

        $userId = Security::userId();
        if (!$userId) {
            header('Location: /login', true, 303);
            return;
        }


        if (!Security::checkCsrf()) {
            View::error(400, 'Invalid CSRF token.');
            return;
        }


        $discussionId = (int) $id;
        if (!Discussion::find($discussionId)) {
            View::error(404, 'Discussion not found.');
            return;
        }


        $body = trim((string) ($_POST['body'] ?? ''));
        if ($body === '' || mb_strlen($body) > 2000) {
            View::error(422, 'Comment must be 1–2000 characters.');
            return;
        }

        $parentId = null;
        $rawParent = (string) ($_POST['parent_id'] ?? '');
        if ($rawParent !== '') {
            if (!ctype_digit($rawParent)) {
                View::error(400, 'Invalid parent comment.');
                return;
            }
            $parentDepth = Comment::depthInDiscussion((int) $rawParent, $discussionId);
            if ($parentDepth === null) {
                View::error(400, 'Parent comment is not in this discussion.');
                return;
            }
            if ($parentDepth + 1 > Comment::MAX_DEPTH) {
                View::error(400, 'Maximum reply depth reached.');
                return;
            }
            $parentId = (int) $rawParent;
        }

    
        Comment::create($discussionId, $parentId, $userId, $body);
        header("Location: /discussions/$discussionId", true, 303);
    }
}