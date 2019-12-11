<?php
Route::prefix('api')->group(function () {

    Route::group(['middleware' => ['api']], function () {
        Route::get("load-chats", "Techsmart\Chat\Http\Controllers\ChatController@loadChats");
        Route::get("load-messages/{chatId}", "ChatController@loadMessages");

        Route::post("send-message", "ChatController@sendMessage");
        Route::post("upload-file", "ChatController@uploaFile");
        Route::delete("delete-message/{messageId}", "ChatController@deleteMessage");
        Route::patch("update-message/{messageId}", "ChatController@updateMessage");
        Route::post("/read-messages/{chatId}", "ChatController@readMessage");
        Route::post("/find-messages", "ChatController@findMessage");

    });

});

Route::group([], function () {
    Route::get("chats", "Techsmart\Chat\Http\Controllers\ChatController@index");
    Route::get("load-chats", "Techsmart\Chat\Http\Controllers\ChatController@loadChats");
    Route::get("load-messages/{chatId}", "Techsmart\Chat\Http\Controllers\ChatController@loadMessages");

    Route::post("send-message", "Techsmart\Chat\Http\Controllers\ChatController@sendMessage");
    Route::post("upload-file", "Techsmart\Chat\Http\Controllers\ChatController@uploaFile");
    Route::delete("delete-message/{messageId}", "Techsmart\Chat\Http\Controllers\ChatController@deleteMessage");
    Route::patch("update-message/{messageId}", "Techsmart\Chat\Http\Controllers\ChatController@updateMessage");
    Route::post("/read-messages/{chatId}", "Techsmart\Chat\Http\Controllers\ChatController@readMessage");
    Route::post("/find-messages", "Techsmart\Chat\Http\Controllers\ChatController@findMessage");

});


Route::get("/test", function() {
    echo "TEST<br>";
});