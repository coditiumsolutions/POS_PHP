<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories', 'quantity_in')) {
                $table->decimal('quantity_in', 18, 3)->default(0)->after('quantity_reserved');
            }
            if (!Schema::hasColumn('inventories', 'quantity_out')) {
                $table->decimal('quantity_out', 18, 3)->default(0)->after('quantity_in');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (Schema::hasColumn('inventories', 'quantity_out')) {
                $table->dropColumn('quantity_out');
            }
            if (Schema::hasColumn('inventories', 'quantity_in')) {
                $table->dropColumn('quantity_in');
            }
        });
    }
};
