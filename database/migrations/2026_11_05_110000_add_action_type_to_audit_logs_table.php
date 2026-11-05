<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('audit_logs', 'action_type')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->string('action_type', 50)->nullable()->after('user_uid');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('audit_logs', 'action_type')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->dropColumn('action_type');
            });
        }
    }
};
