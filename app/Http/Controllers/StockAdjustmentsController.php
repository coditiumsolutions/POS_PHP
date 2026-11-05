<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockAdjustmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = StockAdjustment::with(['warehouse', 'user']);

        if ($request->filled('date_from')) {
            $query->whereDate('adjustment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('adjustment_date', '<=', $request->date_to);
        }

        if ($request->filled('adjustment_type')) {
            $query->where('adjustment_type', $request->adjustment_type);
        }

        $adjustments = $query->orderBy('adjustment_date', 'desc')->paginate(50);

        return view('stock-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('product_name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('warehouse_name')->get();
        $adjustmentNo = $this->generateAdjustmentNo();

        return view('stock-adjustments.create', compact('products', 'warehouses', 'adjustmentNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'adjustment_date' => 'required|date',
            'warehouse_uid' => 'nullable|exists:warehouses,uid',
            'adjustment_type' => 'required|in:Increase,Decrease,Physical Count',
            'items' => 'required|array|min:1',
            'items.*.product_uid' => 'required|exists:products,uid',
            'items.*.quantity' => 'required|numeric',
            'items.*.remarks' => 'nullable|string|max:500',
            'remarks' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $adjustment = StockAdjustment::create([
                'adjustment_no' => $this->generateAdjustmentNo(),
                'adjustment_date' => $validated['adjustment_date'],
                'warehouse_uid' => $validated['warehouse_uid'],
                'user_uid' => Auth::id(),
                'adjustment_type' => $validated['adjustment_type'],
                'status' => 'Completed',
                'remarks' => $validated['remarks'],
                'created_date' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                StockAdjustmentDetail::create([
                    'stock_adjustment_uid' => $adjustment->uid,
                    'product_uid' => $item['product_uid'],
                    'quantity' => $item['quantity'],
                    'remarks' => $item['remarks'] ?? null,
                ]);

                // Update inventory
                $this->adjustInventory(
                    $item['product_uid'],
                    $item['quantity'],
                    $validated['adjustment_type'],
                    $validated['warehouse_uid'],
                    $adjustment->adjustment_no
                );
            }

            DB::commit();

            return redirect()->route('stock-adjustments.show', $adjustment->uid)
                ->with('success', 'Stock adjustment completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating adjustment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($uid)
    {
        $adjustment = StockAdjustment::with(['details.product', 'warehouse', 'user'])
            ->findOrFail($uid);

        return view('stock-adjustments.show', compact('adjustment'));
    }

    private function generateAdjustmentNo()
    {
        $date = now()->format('Ymd');
        $lastAdjustment = StockAdjustment::where('adjustment_no', 'like', "SA{$date}%")
            ->orderBy('adjustment_no', 'desc')
            ->first();

        if ($lastAdjustment) {
            $lastNumber = (int)substr($lastAdjustment->adjustment_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "SA{$date}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function adjustInventory($productUid, $quantity, $type, $warehouseUid, $referenceNo)
    {
        $inventory = Inventory::where('product_uid', $productUid)
            ->where(function($q) use ($warehouseUid) {
                if ($warehouseUid) {
                    $q->where('warehouse_uid', $warehouseUid);
                } else {
                    $q->whereNull('warehouse_uid');
                }
            })
            ->first();

        $adjustedQuantity = $quantity;
        if ($type === 'Decrease') {
            $adjustedQuantity = -abs($quantity);
        } elseif ($type === 'Physical Count' && $inventory) {
            $adjustedQuantity = $quantity - $inventory->quantity_in_stock;
        }

        if ($inventory) {
            $inventory->quantity_in_stock += $adjustedQuantity;
            $inventory->quantity_available += $adjustedQuantity;
            $inventory->updated_date = now();
            $inventory->save();
        } else {
            Inventory::create([
                'product_uid' => $productUid,
                'warehouse_uid' => $warehouseUid,
                'quantity_in_stock' => max(0, $adjustedQuantity),
                'quantity_available' => max(0, $adjustedQuantity),
                'updated_date' => now(),
            ]);
        }

        InventoryTransaction::create([
            'product_uid' => $productUid,
            'warehouse_uid' => $warehouseUid,
            'transaction_type' => 'Adjustment-' . $type,
            'reference_no' => $referenceNo,
            'transaction_date' => now(),
            'quantity' => abs($adjustedQuantity),
            'remarks' => "Stock {$type}",
            'created_date' => now(),
        ]);
    }
}
