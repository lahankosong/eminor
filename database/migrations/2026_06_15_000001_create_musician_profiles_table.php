<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('musician_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('roles')->nullable();        // koma: "vokalis,gitaris"
            $table->string('skill_level')->nullable();  // pemula|menengah|mahir|profesional
            $table->string('genres')->nullable();       // koma: "indie,rock"
            $table->string('location')->nullable();
            $table->text('bio')->nullable();
            $table->string('looking_for')->nullable();
            $table->string('spotify_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('instagram')->nullable();
            $table->boolean('open_to_band')->default(true);
            $table->boolean('open_to_collab')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('musician_profiles');
    }
};
