<?php

namespace Techsmart\Chat\Http\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Techsmart\Chat\Message;
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
        $this->message = $message;
        $this->message->createdAt = $message->created_at;
        $this->createdAt = $message->created_at;
        // $this->countMessages = Message::countCommonUnreadMessages($this->message->dst_id);  
        $this->chatId = $message->chat_id;      
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channels[] = new PresenceChannel('chats.' . $this->message->chat_id);

        return $channels;
    }
}
