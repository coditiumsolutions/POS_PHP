<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop global unique on product_code if present (name may vary).
        $indexes = DB::select('SHOW INDEX FROM products WHERE Non_unique = 0 AND Column_name = ?', ['product_code']);
        foreach ($indexes as $index) {
            if (($index->Key_name ?? '') === 'PRIMARY') {
                continue;
            }
            try {
                DB::statement('ALTER TABLE products DROP INDEX `' . $index->Key_name . '`');
            } catch (\Throwable $e) {
                // continue
            }
        }

        $exists = collect(DB::select('SHOW INDEX FROM products'))
            ->contains(fn ($i) => ($i->Key_name ?? '') === 'products_product_code_branch_uid_unique');

        if (!$exists && Schema::hasColumn('products', 'branch_uid')) {
            try {
                DB::statement('ALTER TABLE products ADD UNIQUE products_product_code_branch_uid_unique (product_code, branch_uid)');
            } catch (\Throwable $e) {
                // App validation still enforces per-branch uniqueness.
            }
        }
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE products DROP INDEX products_product_code_branch_uid_unique');
        } catch (\Throwable $e) {
        }

        try {
            DB::statement('ALTER TABLE products ADD UNIQUE products_product_code_unique (product_code)');
        } catch (\Throwable $e) {
        }
    }
};
