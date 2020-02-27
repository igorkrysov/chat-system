<?php

namespace Techsmart\Chat\Http;

use Illuminate\Database\Eloquent\Model;
use Techsmart\Chat\Http\Events\NewMessage;
// use App\Events\NewMessage;
use Auth;
use DB;

class Message extends Model
{
    protected $fillable = ['chat_id', 'src_id', 'message'];

    /**
     * Get user
     */
    public function user() {
        return $this->hasOne('App\User', 'id', 'src_id');
    }

    public function chat() {
        return $this->belongsTo('Techsmart\Chat\Http\Chat', 'chat_id', 'id');
    }

    public function files() {
        return $this->hasMany('Techsmart\Chat\Http\MessageFile', 'message_id', 'id');    
    }

    public static function sendMessage($chatId, $message, $files = []) {
        
        $chat = Chat::find($chatId);
        if ($chat && $chat->is_blocked) {
            return response()->json(['status' => false, 'errors' => "Этот чат уже не активен"]);
        }

        $message = $chat->addMessage(Auth::User()->id, $message);

        if (isset($files)) {
            foreach ($files as $file) {
                MessageFile::setMessageId($file['id'], $message->id);
            }
        }
        $message->files;
        $message->user;
        
        broadcast(new NewMessage($message))->toOthers();
        return $message;
    }

    public static function loadMessages($chatId) {
        $messages = self::where('chat_id', $chatId)->with(['files', 'user'])->get();

        $participant = ParticipantChat::where('chat_id', $chatId)->where('user_id', Auth::User()->id)->first();
        $participant->is_read = true;
        $participant->save();

        return $messages;
    }

    public static function setRead($chatId) {
        $messages = Message::where('chat_id', $chatId)->where('src_id', '!=', Auth::User()->id)->update(['is_read' => true]);        

        return true;
    }

    public static function addMessage($chatId, $from, $message) {
        $message = self::create(['chat_id' => $chatId, 'src_id' => $from, 'message' => $message]);        
        $message->load('user');

        return $message;
    }

    public static function updateMessage($text, $messageId, $files = []) {
        
        $message = self::find($messageId);
        
        if ($message) {
            // $to = \Carbon\Carbon::now();
            // $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $message->updated_at);
            // $diff_in_days = $to->diffInDays($from);
            // if ($to->diffInMinutes > 5) {
            //     return false;
            // }
            $message->message = $text;
            $message->save();
            if (isset($files)) {
                foreach ($files as $file) {
                    MessageFile::setMessageId($file['id'], $message->id);
                }
            }

            $message->files;
            $message->user;
            
            return $message;
        }

        return false;
    }
}
