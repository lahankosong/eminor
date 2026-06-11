<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('kamu_notes')) return; // sudah dibuat oleh ensure migration
        Schema::create('kamu_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('body');
            $table->string('color')->default('#FFF8F0');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kamu_notes'); }
};