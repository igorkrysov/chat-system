<?php

namespace Techsmart\Chat\Http\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Techsmart\Chat\Http\Message;
use Illuminate\Support\Facades\Log;
use App\User;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message = '';
    public $createdAt = '';    
    public $countMessages = 0;
    public $chatId = 0;

    protected $userId = 0;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        Log::info('construct_: ');
        
        $this->message = $message;
        $this->message->user;
        $this->message->files;
        $this->message->createdAt = $message->created_at;
        $this->createdAt = $message->created_at;
        $this->chatId = $message->chat_id;
        Log::info('construct_: end');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channels = [];
        foreach ($this->message->chat->participants as $participant) {
            $channels[] = new PresenceChannel('chat.' . $participant->user_id);
            Log::info('chat. . $user->id: ' . 'chat.' . $participant->user_id);
        }

        return $channels;
    }
}
