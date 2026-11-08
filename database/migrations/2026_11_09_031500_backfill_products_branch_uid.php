<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $bahria = DB::table('branches')->where('branch_name', 'Bahria Town')->value('uid');
        $fallback = $bahria ?: DB::table('branches')->where('is_active', 1)->orderBy('uid')->value('uid');

        if ($fallback) {
            DB::table('products')
                ->whereNull('branch_uid')
                ->update(['branch_uid' => $fallback]);
        }

        // Ensure every product has an inventory row for its branch.
        $products = DB::table('products')->whereNotNull('branch_uid')->select('uid', 'branch_uid', 'purchase_price')->get();
        foreach ($products as $product) {
            $exists = DB::table('inventories')
                ->where('product_uid', $product->uid)
                ->where('branch_uid', $product->branch_uid)
                ->exists();

            if (!$exists) {
                DB::table('inventories')->insert([
                    'product_uid' => $product->uid,
                    'branch_uid' => $product->branch_uid,
                    'warehouse_uid' => null,
                    'quantity_in_stock' => 0,
                    'quantity_available' => 0,
                    'quantity_reserved' => 0,
                    'average_cost' => $product->purchase_price ?? 0,
                    'quantity_in' => 0,
                    'quantity_out' => 0,
                    'updated_date' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // non-destructive
    }
};
