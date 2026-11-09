<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WastageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = InventoryTransaction::with('product')
            ->where('transaction_type', 'Wastage');

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $wastages = $query->orderBy('transaction_date', 'desc')->paginate(50);
        $totalQuantity = $query->sum('quantity');

        return view('wastage.index', compact('wastages', 'totalQuantity'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('product_name')->get();
        return view('wastage.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wastage_date' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_uid' => 'required|exists:products,uid',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.reason' => 'required|string|max:200',
            'remarks' => 'nullable|string|max:500',
        ]);

        $wastageDate = $this->parseDate($validated['wastage_date']);
        if (!$wastageDate) {
            return back()->withInput()->withErrors(['wastage_date' => 'Wastage date is invalid. Use format like 10-Nov-2026.']);
        }

        DB::beginTransaction();
        try {
            $wastageNo = $this->generateWastageNo();

            foreach ($validated['items'] as $item) {
                // Deduct from inventory
                $inventory = Inventory::where('product_uid', $item['product_uid'])
                    ->whereNull('warehouse_uid')
                    ->first();

                if ($inventory) {
                    $inventory->quantity_in_stock -= $item['quantity'];
                    $inventory->quantity_available -= $item['quantity'];
                    $inventory->updated_date = now();
                    $inventory->save();
                }

                // Record wastage transaction
                InventoryTransaction::create([
                    'product_uid' => $item['product_uid'],
                    'transaction_type' => 'Wastage',
                    'reference_no' => $wastageNo,
                    'transaction_date' => $wastageDate,
                    'quantity' => -$item['quantity'], // Negative for deduction
                    'remarks' => $item['reason'],
                    'created_date' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('wastage.index')
                ->with('success', 'Wastage recorded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error recording wastage: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function generateWastageNo()
    {
        $date = now()->format('Ymd');
        $lastWastage = InventoryTransaction::where('reference_no', 'like', "WS{$date}%")
            ->where('transaction_type', 'Wastage')
            ->orderBy('reference_no', 'desc')
            ->first();

        if ($lastWastage) {
            $lastNumber = (int)substr($lastWastage->reference_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "WS{$date}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function parseDate(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        foreach (['d-M-Y', 'Y-m-d', 'd/m/Y', 'd-m-Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Throwable $e) {
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
