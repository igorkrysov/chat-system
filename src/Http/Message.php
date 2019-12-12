<?php

namespace Techsmart\Chat\Http;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Techsmart\Chat\Events\NewMessage;
use Auth;
use DB;

class Message extends Model
{
    protected $fillable = ['chat_id', 'src_id', 'message'];

    /**
     * Get user
     */
    public function user() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function photoes() {
        return $this->hasMany('App\MessagePhoto', 'message_id', 'id');    
    }

    public static function sendMessage(Request $request) {
        $rules =[
            'chatId' => 'required|integer',
            'message' => 'required|string',
            'files' => 'nullable|array',            
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }
        $chat = Chat::find($request->chatId);
        if ($chat && $chat->is_blocked) {
            return response()->json(['status' => false, 'errors' => "Этот чат уже не активен"]);
        }

        $message = $chat->addMessage(Auth::User()->id, $request->message);

        $message->createdAt = $message->created_at;
        if ($request->has('photoes')) {
            foreach ($request->photoes as $photo) {
                MessagePhoto::setMessageId($photo['id'], $message->id);
            }
        }
        $message->photoes;
        broadcast(new NewMessage($message))->toOthers();
        
        return $message;
    }

    public static function loadMessages($chatId) {
        $messages = self::where('chat_id', $chatId)->with(['photoes'])->get();
        foreach ($messages as $key => $message) {
            $messages[$key]->createdAt = $message->created_at;
        }
        return $messages;
    }

    public static function setRead($chatId) {
        $messages = Message::where('chat_id', $chatId)->where('src_id', '!=', Auth::User()->id)->update(['is_read' => true]);        

        return true;
    }

    public static function addMessage($chatId, $from, $message) {
        $message = self::create(['chat_id' => $chatId, 'src_id' => $from, 'message' => $message]);
        broadcast(new NewMessage($mess))->toOthers();

        return $message;
    }

    public static function updateMessage(Request $request, $messageId) {
        $request->validate([
            'message' => "required|text"
        ]);
        $message = self::find($messageId);
        
        if ($message) {
            $to = \Carbon\Carbon::now();
            $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $message->updated_at);
            $diff_in_days = $to->diffInDays($from);
            if ($to->diffInMinutes > 5) {
                return false;
            }
            $message->message = $request->message;
            $message->save();

            return true;
        }

        return false;
    }
}
