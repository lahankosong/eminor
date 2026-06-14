<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_plans', function (Blueprint $table) {
            $table->id();
            $table->date('plan_date');
            $table->unsignedBigInteger('song_id')->nullable();
            $table->string('platforms')->nullable();   // daftar platform dipisah koma
            $table->string('title')->nullable();        // ide/judul konten
            $table->string('status')->default('rencana'); // rencana | proses | selesai
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('song_id')->references('id')->on('songs')->onDelete('set null');
            $table->index('plan_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_plans');
    }
};
