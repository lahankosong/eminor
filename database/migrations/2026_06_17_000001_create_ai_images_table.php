<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('song_id')->nullable()->index();
            $table->text('prompt');
            $table->string('provider', 60)->nullable();   // pollinations / dall-e / dll
            $table->string('url', 500);                    // secure_url Cloudinary
            $table->string('public_id', 255)->nullable();  // untuk hapus di Cloudinary
            $table->string('ratio', 12)->nullable();       // 9:16 / 16:9 / 1:1
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_images');
    }
};
