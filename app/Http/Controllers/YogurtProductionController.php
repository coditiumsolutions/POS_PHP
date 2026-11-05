<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class YogurtProductionController extends Controller
{
    const MILK_PRODUCT_CODE = 'MILK';
    const YOGURT_PRODUCT_CODE = 'YOGURT';
    const PRODUCTION_LOSS_PERCENT = 5; // 5% loss during production
    const MILK_TO_YOGURT_RATIO = 0.95; // 1kg milk produces 0.95kg yogurt after loss

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = InventoryTransaction::with('product')
            ->where('transaction_type', 'Yogurt Production');

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $productions = $query->orderBy('transaction_date', 'desc')->paginate(50);

        return view('yogurt-production.index', compact('productions'));
    }

    public function create()
    {
        $milkProduct = Product::where('product_code', self::MILK_PRODUCT_CODE)
            ->orWhere('product_name', 'like', '%milk%')
            ->first();
        
        $yogurtProduct = Product::where('product_code', self::YOGURT_PRODUCT_CODE)
            ->orWhere('product_name', 'like', '%yogurt%')
            ->first();

        $milkInventory = null;
        if ($milkProduct) {
            $milkInventory = Inventory::where('product_uid', $milkProduct->uid)
                ->whereNull('warehouse_uid')
                ->first();
        }

        return view('yogurt-production.create', compact('milkProduct', 'yogurtProduct', 'milkInventory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'production_date' => 'required|date',
            'milk_product_uid' => 'required|exists:products,uid',
            'yogurt_product_uid' => 'required|exists:products,uid',
            'milk_quantity' => 'required|numeric|min:0.001',
            'yogurt_quantity' => 'required|numeric|min:0.001',
            'loss_quantity' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $productionNo = $this->generateProductionNo();

            // Deduct milk from inventory
            $this->deductInventory(
                $validated['milk_product_uid'],
                $validated['milk_quantity'],
                $productionNo,
                'Milk consumed for yogurt production'
            );

            // Add yogurt to inventory
            $this->addInventory(
                $validated['yogurt_product_uid'],
                $validated['yogurt_quantity'],
                $productionNo,
                'Yogurt produced from milk'
            );

            // Record loss if any
            if (!empty($validated['loss_quantity']) && $validated['loss_quantity'] > 0) {
                InventoryTransaction::create([
                    'product_uid' => $validated['milk_product_uid'],
                    'transaction_type' => 'Production Loss',
                    'reference_no' => $productionNo,
                    'transaction_date' => $validated['production_date'],
                    'quantity' => $validated['loss_quantity'],
                    'remarks' => 'Loss during yogurt production',
                    'created_date' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('yogurt-production.index')
                ->with('success', 'Yogurt production recorded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error recording production: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function calculateYield(Request $request)
    {
        $milkQty = $request->input('milk_quantity', 0);
        $lossPercent = self::PRODUCTION_LOSS_PERCENT;
        $yogurtQty = $milkQty * self::MILK_TO_YOGURT_RATIO;
        $lossQty = $milkQty - $yogurtQty;

        return response()->json([
            'yogurt_quantity' => round($yogurtQty, 3),
            'loss_quantity' => round($lossQty, 3),
            'loss_percent' => $lossPercent,
        ]);
    }

    private function generateProductionNo()
    {
        $date = now()->format('Ymd');
        $lastProduction = InventoryTransaction::where('reference_no', 'like', "YP{$date}%")
            ->where('transaction_type', 'Yogurt Production')
            ->orderBy('reference_no', 'desc')
            ->first();

        if ($lastProduction) {
            $lastNumber = (int)substr($lastProduction->reference_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "YP{$date}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function deductInventory($productUid, $quantity, $referenceNo, $remarks)
    {
        $inventory = Inventory::where('product_uid', $productUid)
            ->whereNull('warehouse_uid')
            ->first();

        if ($inventory) {
            $inventory->quantity_in_stock -= $quantity;
            $inventory->quantity_available -= $quantity;
            $inventory->updated_date = now();
            $inventory->save();
        }

        InventoryTransaction::create([
            'product_uid' => $productUid,
            'transaction_type' => 'Yogurt Production',
            'reference_no' => $referenceNo,
            'transaction_date' => now(),
            'quantity' => -$quantity,
            'remarks' => $remarks,
            'created_date' => now(),
        ]);
    }

    private function addInventory($productUid, $quantity, $referenceNo, $remarks)
    {
        $inventory = Inventory::where('product_uid', $productUid)
            ->whereNull('warehouse_uid')
            ->first();

        if ($inventory) {
            $inventory->quantity_in_stock += $quantity;
            $inventory->quantity_available += $quantity;
            $inventory->updated_date = now();
            $inventory->save();
        } else {
            Inventory::create([
                'product_uid' => $productUid,
                'quantity_in_stock' => $quantity,
                'quantity_available' => $quantity,
                'updated_date' => now(),
            ]);
        }

        InventoryTransaction::create([
            'product_uid' => $productUid,
            'transaction_type' => 'Yogurt Production',
            'reference_no' => $referenceNo,
            'transaction_date' => now(),
            'quantity' => $quantity,
            'remarks' => $remarks,
            'created_date' => now(),
        ]);
    }
}
