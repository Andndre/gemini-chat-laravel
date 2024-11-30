<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // gemini or user
            $table->string('role');
            // the message
            $table->text('content');
            // relation to the chat_sessions table
            $table->foreignId('chat_session_id')->constrained('chat_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_items');
    }
};
