<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('content_plans', 'content_type')) {
                $table->string('content_type')->default('short');
            }
        });
    }

    public function down(): void
    {
        Schema::table('content_plans', function (Blueprint $table) {
            if (Schema::hasColumn('content_plans', 'content_type')) {
                $table->dropColumn('content_type');
            }
        });
    }
};

