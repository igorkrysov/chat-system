<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->default('');
            $table->unsignedBigInteger('type_chat_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();  
            $table->foreign('type_chat_id')->references('id')->on('types_of_chat')->onDelete('set null');            
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');            
            $table->boolean('is_blocked')->default(false);            
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
        Schema::dropIfExists('chats');
    }
}
