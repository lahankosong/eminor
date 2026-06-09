<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->text('lyrics')->nullable()->after('description');
            $table->text('chords')->nullable()->after('lyrics');
            $table->string('key_signature')->nullable()->after('chords');
            $table->integer('tempo')->nullable()->after('key_signature');
        });
    }

    public function down(): void
    {
        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['lyrics', 'chords', 'key_signature', 'tempo']);
        });
    }
};