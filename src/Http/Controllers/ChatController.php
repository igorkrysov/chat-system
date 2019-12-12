<?php

namespace Techsmart\Chat\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Techsmart\Chat\Http\Chat;
use Techsmart\Chat\Http\Message;
use Techsmart\Chat\Http\MessageFile;
use App\User;
use Auth;

class ChatController {

    public function index() {        
        return view("chat::chats");
    }

    public function loadUsers(Request $request) { 
        $users = User::where('id', '!=', Auth::User()->id)->get();

        if ($request->ajax()) {
            return response()->json(['status' => true, 'users' => $users]);
        }

    }

    public function loadChats(Request $request) { 
        $chats = Chat::chats();

        if ($request->ajax()) {
            return response()->json(['status' => true, 'chats' => $chats]);
        }

    }
    
    public function loadMessages(Request $request, $chatId) {
        $messages = Message::loadMessages($chatId);

        if ($request->ajax()) {
            return response()->json(['status' => true, 'messages' => $messages]);
        }

        return view("chat::messages", ['messages' => $messages]);
    }
    
    public function sendMessage(Request $request) {
        $message = Message::sendMessage();

        if ($request->ajax()) {
            return response()->json(['status' => true, 'messages' => $message]);
        }

        return response()->json(['status' => true, 'message' => $message, 'createdAt' => $message->created_at]);
    }

    public function uploaFile(Request $request) {
        $file = MessageFile::uploadFile($request);

        return response()->json(['file' => $file]);
    }

    public function deleteMessage($messageId) {
        Message::delete($messageId);
        MessageFile::where('message_id', $messageId)->delete();
        
        return resonse()->json(['status' => true]);
    }

    public function updateMessage(Request $request, $messageId) {
        if (Message::updateMessage($request, $messageId)) {
            return response()->json(['status' => true, 'message' => 'Сообщение успешно обновлено!']);
        }

        return response()->json(['status' => false, 'message' => 'Сообщение не моежет быть обновлено!']); 
    }

    public function readMessage($chatId) {
        Message::setRead($chatId);

        return response()->json(['status' => true]);
    }

    public function findMessage(Request $request) {
        $chats = Chat::findMessage($request);

        return response()->json(['chats' => $chats]);
    }

    public function createChat(Request $request) {
        $chat = Chat::createChat($request);

        return response()->json(['status' => true, 'chat' => $chat]);
    }
}