<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['messages', 'group_messages'] as $t) {
            if (Schema::hasTable($t)) {
                Schema::table($t, function (Blueprint $table) use ($t) {
                    if (!Schema::hasColumn($t, 'media_url'))  $table->string('media_url')->nullable()->after('body');
                    if (!Schema::hasColumn($t, 'media_type')) $table->string('media_type', 20)->nullable()->after('media_url');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['messages', 'group_messages'] as $t) {
            if (Schema::hasTable($t)) {
                Schema::table($t, function (Blueprint $table) use ($t) {
                    if (Schema::hasColumn($t, 'media_url'))  $table->dropColumn('media_url');
                    if (Schema::hasColumn($t, 'media_type')) $table->dropColumn('media_type');
                });
            }
        }
    }
};
