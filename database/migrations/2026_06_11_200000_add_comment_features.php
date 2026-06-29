<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // parent_id & likes_count ke post_comments
        if (Schema::hasTable('post_comments')) {
            Schema::table('post_comments', function (Blueprint $table) {
                if (!Schema::hasColumn('post_comments', 'parent_id'))
                    $table->unsignedBigInteger('parent_id')->nullable();
                if (!Schema::hasColumn('post_comments', 'likes_count'))
                    $table->unsignedInteger('likes_count')->default(0);
            });
        }

        // post_comment_likes
        if (!Schema::hasTable('post_comment_likes')) {
            Schema::create('post_comment_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('comment_id')->constrained('post_comments')->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->unique(['comment_id', 'user_id']);
                $table->timestamps();
            });
        }
    }
    public function down(): void {}
};

