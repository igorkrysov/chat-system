<?php

namespace Techsmart\Chat\Http;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Auth;

class Chat extends Model
{
    public function user() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function type() {
        return $this->hasOne('App\TypeChat', 'id', 'type_chat_id');
    }

    public function admin() {
        return $this->hasOne('App\User', 'id', 'admin_id');
    }

    public static function createChat($typeId, $adminId, $chatName = '') {
        $chat = new self();
        $chat->type_id = $typeId;
        $chat->name = $chatName;
        $chat->admin_id = $adminId;
        $chat->save();        
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

        return $message;
    }



    public static function findMessage(Reqeust $request ) {
        $reqeust->validate([
            'search' => 'required'
        ]);

        $search = $reqeust->search;
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
