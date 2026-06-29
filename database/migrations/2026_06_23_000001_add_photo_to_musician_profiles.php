<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('musician_profiles') && !Schema::hasColumn('musician_profiles', 'photo')) {
            Schema::table('musician_profiles', function (Blueprint $table) {
                $table->string('photo')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('musician_profiles', 'photo')) {
            Schema::table('musician_profiles', function (Blueprint $table) {
                $table->dropColumn('photo');
            });
        }
    }
};

