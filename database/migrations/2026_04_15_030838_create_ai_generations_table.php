<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('song_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Hasil generate
            $table->longText('topics')->nullable();
            $table->longText('scripts')->nullable();
            $table->longText('visual_sequences')->nullable();
            $table->longText('dreamina_prompts')->nullable();
            $table->text('shorts_description')->nullable();
            $table->string('hashtags')->nullable();
            
            // Pilihan user
            $table->integer('selected_topic_id')->nullable();
            $table->integer('selected_variation_id')->nullable();
            $table->text('selected_hook')->nullable();
            $table->text('selected_caption')->nullable();
            $table->text('selected_prompt')->nullable();
            
            $table->timestamps();

        });
    }
    
    public function down()
    {
        Schema::dropIfExists('ai_generations');
    }
};