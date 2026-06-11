<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('conversation_invites')) {
            Schema::create('conversation_invites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
                $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
                $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
                $table->timestamp('joined_at')->nullable();
                $table->timestamps();
                $table->unique(['conversation_id', 'to_user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_invites');
    }
};
