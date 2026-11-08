<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'branch_uid')) {
                $table->unsignedInteger('branch_uid')->nullable()->after('category_uid');
                $table->index('branch_uid');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'branch_uid')) {
                $table->dropIndex(['branch_uid']);
                $table->dropColumn('branch_uid');
            }
        });
    }
};
