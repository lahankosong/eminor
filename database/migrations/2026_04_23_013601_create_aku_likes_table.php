<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aku_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aku_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['aku_post_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aku_likes');
    }
};