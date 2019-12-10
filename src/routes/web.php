<?php
Route::prefix('api')->group(function () {

    Route::group(['middleware' => ['api']], function () {
        Route::get("load-chats", "ChatController@loadChats");
        Route::get("load-messages/{chatId}", "ChatController@loadMessages");

        Route::post("send-message", "ChatController@sendMessage");
        Route::post("upload-file", "ChatController@uploaFile");
        Route::delete("delete-message/{messageId}", "ChatController@deleteMessage");
        Route::patch("update-message/{messageId}", "ChatController@updateMessage");
        Route::post("/read-messages/{chatId}", "ChatController@readMessage");
        Route::post("/find-messages", "ChatController@findMessage");

    });

});

Route::group(['middleware' => ['auth']], function () {
    Route::get("chats", "ChatController@index");
    Route::get("load-chats", "ChatController@loadChats");
    Route::get("load-messages/{chatId}", "ChatController@loadMessages");

    Route::post("send-message", "ChatController@sendMessage");
    Route::post("upload-file", "ChatController@uploaFile");
    Route::delete("delete-message/{messageId}", "ChatController@deleteMessage");
    Route::patch("update-message/{messageId}", "ChatController@updateMessage");
    Route::post("/read-messages/{chatId}", "ChatController@readMessage");
    Route::post("/find-messages", "ChatController@findMessage");

});


Route::get("/test", function() {
    echo "TEST<br>";
});