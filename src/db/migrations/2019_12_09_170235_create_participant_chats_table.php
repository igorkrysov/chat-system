<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipantChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_participant', function (Blueprint $table) {
            $table->bigIncrements('id');            
            $table->unsignedBigInteger('chat_id')->nullable();  
            $table->unsignedBigInteger('user_id')->nullable();  
            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');                 
            $table->dateTime('last_visited')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_participant');
    }
}
