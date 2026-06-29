<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'roles')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('roles')->nullable();
            });
        }
        if (Schema::hasTable('musician_profiles') && !Schema::hasColumn('musician_profiles', 'tip_url')) {
            Schema::table('musician_profiles', function (Blueprint $table) {
                $table->string('tip_url')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'roles')) {
            Schema::table('users', fn (Blueprint $t) => $t->dropColumn('roles'));
        }
        if (Schema::hasColumn('musician_profiles', 'tip_url')) {
            Schema::table('musician_profiles', fn (Blueprint $t) => $t->dropColumn('tip_url'));
        }
    }
};

