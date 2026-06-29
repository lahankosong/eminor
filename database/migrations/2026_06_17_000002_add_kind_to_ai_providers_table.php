<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ai_providers', function (Blueprint $table) {
            if (!Schema::hasColumn('ai_providers', 'kind')) {
                $table->string('kind', 12)->default('text'); // text | image
            }
        });
    }

    public function down(): void
    {
        Schema::table('ai_providers', function (Blueprint $table) {
            if (Schema::hasColumn('ai_providers', 'kind')) {
                $table->dropColumn('kind');
            }
        });
    }
};

