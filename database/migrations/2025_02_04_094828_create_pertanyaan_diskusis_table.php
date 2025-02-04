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
        Schema::create('pertanyaan_diskusis', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('judul', 255);
            $table->text('konten');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('diskusi_id')->constrained('diskusis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pertanyaan_diskusis');
    }
};
