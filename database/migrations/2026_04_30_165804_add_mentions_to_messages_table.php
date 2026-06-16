<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'mentioned_user_ids')) {
                $table->json('mentioned_user_ids')->nullable()->after('body');
            }
            if (!Schema::hasColumn('messages', 'is_invitation')) {
                $table->boolean('is_invitation')->default(false)->after('mentioned_user_ids');
            }
            if (!Schema::hasColumn('messages', 'invitation_accepted_at')) {
                $table->timestamp('invitation_accepted_at')->nullable()->after('is_invitation');
            }
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['mentioned_user_ids', 'is_invitation', 'invitation_accepted_at']);
        });
    }
};
