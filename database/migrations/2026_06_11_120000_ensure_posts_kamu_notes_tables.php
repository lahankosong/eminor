<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure posts table exists with all required columns
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('body');
                $table->string('location')->nullable();
                $table->unsignedInteger('likes_count')->default(0);
                $table->unsignedInteger('comments_count')->default(0);
                $table->boolean('is_pinned')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('posts', function (Blueprint $table) {
                if (!Schema::hasColumn('posts', 'user_id'))
                    $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
                if (!Schema::hasColumn('posts', 'location'))
                    $table->string('location')->nullable()->after('body');
                if (!Schema::hasColumn('posts', 'likes_count'))
                    $table->unsignedInteger('likes_count')->default(0);
                if (!Schema::hasColumn('posts', 'comments_count'))
                    $table->unsignedInteger('comments_count')->default(0);
                if (!Schema::hasColumn('posts', 'is_pinned'))
                    $table->boolean('is_pinned')->default(false);
            });
        }

        // Ensure post_likes table exists
        if (!Schema::hasTable('post_likes')) {
            Schema::create('post_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unique(['user_id', 'post_id']);
                $table->timestamps();
            });
        }

        // Ensure post_comments table exists
        if (!Schema::hasTable('post_comments')) {
            Schema::create('post_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('body');
                $table->timestamps();
            });
        }

        // Ensure notifications table exists
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('type');
                $table->string('title');
                $table->text('body')->nullable();
                $table->string('url')->nullable();
                $table->string('icon')->nullable();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // Ensure kamu_notes table exists with all columns
        if (!Schema::hasTable('kamu_notes')) {
            Schema::create('kamu_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('title')->nullable();
                $table->text('body');
                $table->string('color')->default('#FFF8F0');
                $table->boolean('is_pinned')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('kamu_notes', function (Blueprint $table) {
                if (!Schema::hasColumn('kamu_notes', 'title'))
                    $table->string('title')->nullable()->after('user_id');
                if (!Schema::hasColumn('kamu_notes', 'color'))
                    $table->string('color')->default('#FFF8F0');
                if (!Schema::hasColumn('kamu_notes', 'is_pinned'))
                    $table->boolean('is_pinned')->default(false);
            });
        }
    }

    public function down(): void {}
};
