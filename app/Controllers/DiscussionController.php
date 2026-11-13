<?php
namespace App\Controllers;

use App\Models\Comment;
use App\Models\Discussion;
use App\View;

class DiscussionController
{
    private const INDENT_CAP = 8;    
    private const MAX_NODES  = 1000; 

    public function show(string $id): void
    {
       
        if (!ctype_digit($id) || (int) $id < 1 || strlen($id) > 9) {
            View::error(400, 'Invalid discussion ID.');
            return;
        }

      
        $discussion = Discussion::find((int) $id);
        if (!$discussion) {
            View::error(404, 'Discussion not found.');
            return;
        }

        
        $byParent = [];
        foreach (Comment::forDiscussion((int) $id) as $c) {
            $byParent[$c['parent_id'] ?? 0][] = $c;
        }

        
        $count = 0;
        $visited = [];
        $comments = $this->buildTree($byParent, 0, 0, $visited, $count);

        
        View::render('discussion', [
            'discussion' => $discussion,
            'comments'   => $comments,
        ]);
    }

    private function buildTree(array $byParent, int $parentId, int $depth, array &$visited, int &$count): array
    {
        $nodes = [];
        foreach ($byParent[$parentId] ?? [] as $c) {
            if (isset($visited[$c['id']]) || ++$count > self::MAX_NODES) {
                break; 
            }
            $visited[$c['id']] = true;

            
            $children = $this->buildTree($byParent, (int) $c['id'], $depth + 1, $visited, $count);

            $nodes[] = [
                'id'          => $c['id'],
                'author'      => $c['author'],
                'body'        => $c['body'],
                'created_at'  => $c['created_at'],
                'depth'       => $depth,
                'indent'      => min($depth, self::INDENT_CAP) * 24,
                'children'    => $children,
                'hasChildren' => !empty($children),
            ];
        }
        return $nodes;
    }
}