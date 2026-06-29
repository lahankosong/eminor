<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix posts table â€” only if table exists
        if (Schema::hasTable('posts')) {
            if (!Schema::hasColumn('posts', 'user_id')) {
                Schema::table('posts', function (Blueprint $table) {
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                });
            }
            if (!Schema::hasColumn('posts', 'location')) {
                Schema::table('posts', function (Blueprint $table) {
                    $table->string('location')->nullable();
                });
            }
            if (!Schema::hasColumn('posts', 'likes_count')) {
                Schema::table('posts', function (Blueprint $table) {
                    $table->unsignedInteger('likes_count')->default(0);
                    $table->unsignedInteger('comments_count')->default(0);
                    $table->boolean('is_pinned')->default(false);
                });
            }
        }

        // Fix post_likes table â€” only if table exists
        if (Schema::hasTable('post_likes')) {
            if (!Schema::hasColumn('post_likes', 'post_id')) {
                Schema::table('post_likes', function (Blueprint $table) {
                    $table->foreignId('post_id')->constrained()->onDelete('cascade');
                    $table->foreignId('user_id')->constrained()->onDelete('cascade');
                    $table->unique(['user_id', 'post_id']);
                });
            }
        }
    }

    public function down(): void {}
};
