<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Comment $comment) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('task.' . $this->comment->task_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'comment' => [
                'id'         => $this->comment->id,
                'content'    => $this->comment->content,
                'task_id'    => $this->comment->task_id,
                'created_at' => $this->comment->created_at,
                'user'       => [
                    'id'   => $this->comment->user->id,
                    'name' => $this->comment->user->name,
                ],
            ],
        ];
    }

    public function broadcastAs()
    {
        return 'comment.created';
    }
}
