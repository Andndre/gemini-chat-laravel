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
        Schema::create('jawaban_diskusis', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->text('jawaban');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('pertanyaan_diskusi_id')->constrained('pertanyaan_diskusis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jawaban_diskusis');
    }
};
