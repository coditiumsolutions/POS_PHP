<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('sales_invoices', 'pos_type')) {
            Schema::table('sales_invoices', function (Blueprint $table) {
                $table->string('pos_type', 30)->default('Walk-In')->after('invoice_status');
                $table->index('pos_type');
            });
        }

        DB::table('sales_invoices')->whereNull('pos_type')->orWhere('pos_type', '')->update([
            'pos_type' => 'Walk-In',
        ]);

        // Ensure every existing row is Walk-In for now, as requested.
        DB::table('sales_invoices')->update(['pos_type' => 'Walk-In']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('sales_invoices', 'pos_type')) {
            Schema::table('sales_invoices', function (Blueprint $table) {
                $table->dropIndex(['pos_type']);
                $table->dropColumn('pos_type');
            });
        }
    }
};
