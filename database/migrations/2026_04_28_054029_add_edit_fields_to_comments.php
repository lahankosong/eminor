<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // AkuComments — sudah ada, skip
        // PostComments — tambah kolom jika belum ada
        if (!Schema::hasColumn('post_comments', 'updated_at')) {
            Schema::table('post_comments', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable();
            });
        }
    }
    public function down(): void {}
};