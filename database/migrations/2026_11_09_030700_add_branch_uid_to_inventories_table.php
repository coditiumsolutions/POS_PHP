<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('inventories', 'branch_uid')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->unsignedInteger('branch_uid')->nullable()->after('product_uid');
                $table->index('branch_uid');
            });
        }

        $defaultBranchUid = DB::table('branches')->where('is_active', 1)->orderBy('uid')->value('uid');
        $warehouses = DB::table('warehouses')->select('uid', 'warehouse_name')->get();

        foreach ($warehouses as $warehouse) {
            $branchUid = DB::table('branches')
                ->where('branch_name', $warehouse->warehouse_name)
                ->value('uid');

            if (!$branchUid) {
                $name = strtolower((string) $warehouse->warehouse_name);
                if (str_contains($name, 'lahore')) {
                    $branchUid = DB::table('branches')->where('branch_name', 'like', '%Lahore%')->value('uid');
                } elseif (str_contains($name, 'karachi')) {
                    $branchUid = DB::table('branches')->where('branch_name', 'like', '%Karachi%')->value('uid');
                } elseif (str_contains($name, 'bahria')) {
                    $branchUid = DB::table('branches')->where('branch_name', 'like', '%Bahria%')->value('uid');
                }
            }

            if (!$branchUid) {
                $branchUid = $defaultBranchUid;
            }

            if ($branchUid) {
                DB::table('inventories')
                    ->where('warehouse_uid', $warehouse->uid)
                    ->whereNull('branch_uid')
                    ->update(['branch_uid' => $branchUid]);
            }
        }

        if ($defaultBranchUid) {
            DB::table('inventories')
                ->whereNull('branch_uid')
                ->update(['branch_uid' => $defaultBranchUid]);
        }

        // Keep one inventory row per product+branch (prefer highest stock, then newest).
        $groups = DB::table('inventories')
            ->select('product_uid', 'branch_uid', DB::raw('COUNT(*) as cnt'))
            ->whereNotNull('branch_uid')
            ->groupBy('product_uid', 'branch_uid')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($groups as $group) {
            $rows = DB::table('inventories')
                ->where('product_uid', $group->product_uid)
                ->where('branch_uid', $group->branch_uid)
                ->orderByDesc('quantity_in_stock')
                ->orderByDesc('uid')
                ->get();

            $keeper = $rows->first();
            $sumIn = (float) $rows->sum('quantity_in');
            $sumOut = (float) $rows->sum('quantity_out');
            $sumStock = (float) $rows->sum('quantity_in_stock');

            DB::table('inventories')->where('uid', $keeper->uid)->update([
                'quantity_in_stock' => $sumStock,
                'quantity_available' => $sumStock,
                'quantity_in' => $sumIn,
                'quantity_out' => $sumOut,
            ]);

            $deleteIds = $rows->pluck('uid')->filter(fn ($id) => (int) $id !== (int) $keeper->uid)->all();
            if ($deleteIds) {
                DB::table('inventories')->whereIn('uid', $deleteIds)->delete();
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('inventories', 'branch_uid')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->dropIndex(['branch_uid']);
                $table->dropColumn('branch_uid');
            });
        }
    }
};
