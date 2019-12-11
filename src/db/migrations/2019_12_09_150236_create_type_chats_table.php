<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTypeChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types_of_chat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("type");
            // $table->unsignedBigInteger('chat_id')->nullable();  
            // $table->foreign('chat_id')->references('id')->on('chats')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('types_of_chat');
    }
}
