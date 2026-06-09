<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix posts table
        if (!Schema::hasColumn('posts', 'user_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            });
        }
        if (!Schema::hasColumn('posts', 'location')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('location')->nullable()->after('body');
            });
        }
        if (!Schema::hasColumn('posts', 'likes_count')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedInteger('likes_count')->default(0)->after('location');
                $table->unsignedInteger('comments_count')->default(0)->after('likes_count');
                $table->boolean('is_pinned')->default(false)->after('comments_count');
            });
        }

        // Fix post_likes table
        if (!Schema::hasColumn('post_likes', 'post_id')) {
            Schema::table('post_likes', function (Blueprint $table) {
                $table->foreignId('post_id')->after('id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->after('post_id')->constrained()->onDelete('cascade');
                $table->unique(['user_id', 'post_id']);
            });
        }
    }

    public function down(): void {}
};