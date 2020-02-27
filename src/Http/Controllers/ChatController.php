<?php

namespace Techsmart\Chat\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Techsmart\Chat\Http\Chat;
use Techsmart\Chat\Http\Message;
use Techsmart\Chat\Http\MessageFile;
use Illuminate\Support\Facades\Validator;
use App\User;
use Auth;

class ChatController {

    public function index() {        
        return view("chat::chats");
    }

    public function loadUsers(Request $request) { 
        $users = User::where('id', '!=', Auth::User()->id)->get();

        return response()->json(['status' => true, 'users' => $users]);
    }

    public function loadChats(Request $request) { 
        $chats = Chat::chats();

        return response()->json(['status' => true, 'chats' => $chats]);        
    }

    public function loadMessages(Request $request, $chatId) {
        $messages = Message::loadMessages($chatId);

        return response()->json(['status' => true, 'messages' => $messages]);
        
    }
    
    public function sendMessage(Request $request) {
        $rules =[
            'chatId' => 'required|integer',
            'message' => 'required|string',
            'files_' => 'nullable|array',            
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->messages()]);
        }

        $message = Message::sendMessage($request->chatId, $request->message, $request->files_);

        return response()->json(['status' => true, 'message' => $message]);
    }

    public function uploaFile(Request $request) {        
        $file = MessageFile::uploadFile($request);
        if (is_null($file)) {
            return response()->json(['status' => false, 'message' => 'Bad format']);    
        }
        return response()->json(['status' => true, 'file' => $file]);
    }

    public function deleteMessage($messageId) {
        Message::destroy($messageId);
        MessageFile::where('message_id', $messageId)->delete();
        
        return response()->json(['status' => true]);
    }

    public function updateMessage(Request $request, $messageId) {
        $request->validate([
            'message' => "required|string",
            'files_' => 'nullable|array',  
        ]);

        if (($message = Message::updateMessage($request->message, $messageId, $request->files_))) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return response()->json(['status' => false, 'message' => 'Сообщение не моежет быть обновлено!']); 
    }

    public function readMessage($chatId) {
        Message::setRead($chatId);

        return response()->json(['status' => true]);
    }

    public function findChatWithMessage(Request $request) {
        $request->validate([
            'search' => 'required'
        ]);

        $chats = Chat::findChatWithMessage($request->search);

        return response()->json(['chats' => $chats]);
    }

    public function createChat(Request $request) {
        $request->validate([
            'name' => 'required',
            'participants' => 'required|array'
        ]);

        $name = $request->name;
        $participants = $request->participants;
        $participants[] = Auth::User()->id;    
        $type = null;
        $adminId = Auth::User()->id;
        $chat = Chat::createChat($name, $participants, $type, $adminId);

        return response()->json(['status' => true, 'chat' => $chat]);
    }

    public function blockChat($chatId) {
        Chat::blockChat($chatId);

        return response()->json(['status' => true]);
    }
}