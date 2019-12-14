<?php

namespace Techsmart\Chat\Http;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Auth;

class Chat extends Model
{
    protected $appends = ["isNewMessage", "online"];

    public $isNewMessage = false;
    public $online = false;

    protected function getIsNewMessageAttribute() {
        $participant = ParticipantChat::where('chat_id', $this->id)->where('user_id', Auth::User()->id)->first();        
        $this->isNewMessage = !$participant->is_read;
        return $this->isNewMessage;
    }
    protected function getOnlineAttribute() {
        return $this->online;
    }

    public function user() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function type() {
        return $this->hasOne('App\TypeChat', 'id', 'type_chat_id');
    }

    public function admin() {
        return $this->hasOne('App\User', 'id', 'admin_id');
    }

    public function participants() {
        return $this->hasMany('Techsmart\Chat\Http\ParticipantChat', 'id', 'chat_id');
    }

    public static function createChat($name, $participants, $type, $adminId) {
        $chat = new self();
        $chat->type_chat_id = $type;
        $chat->name = $name;
        $chat->admin_id = $adminId;
        $chat->save();

        foreach($participants as $participant) {
            ParticipantChat::create(["chat_id" => $chat->id, "user_id" => $participant]);
        }

        return $chat;
    }

    public static function chats() {        
        $user = Auth::User();
        $chatIds = ParticipantChat::where('user_id', $user->id)->get()->pluck('chat_id');
        $chats = self::whereIn('id', $chatIds)->get();

        return $chats;
    }

    public static function blockChat($chatId) {
        $chat = Chat::find($chatId);
        if ($chat) {
            $chat->is_blocked = true;
            $chat->save();
        }
    }

    public function addMessage($from, $message) {
        $message = Message::addMessage($this->id, $from, $message);
        $this->touch();
        foreach ($this->participants as $participant) {
            if ($participant->user_id != Auth::User()->id) {
                $participant->is_read    = false;
                $participant->save();
            }
        }

        return $message;
    }



    public static function findMessage($search) {
        $search = $search;
        $chats = Chat::chats();
        $chatIds = $chats->pluck(['id']);
        $messages = Message::whereIn('chat_id', $chatIds)->where('message', 'like', "%$search%")->get();

        $ch = [];
        $chats = [];
        foreach ($messages as $message) {
            $ch[$message->chat_id] = $message->chat_id;
        }
        foreach ($ch as $c) {
            $chats[] = $c;
        }
        return $chats;
    }
}
