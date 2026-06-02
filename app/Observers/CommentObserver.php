<?php

namespace App\Observers;

use App\Events\CommentCreatedEvent;
use App\Models\Comment;

class CommentObserver
{
    public function created(Comment $comment): void
    {
        $comment->load('user:id,name');
        CommentCreatedEvent::dispatch($comment);
    }
}
