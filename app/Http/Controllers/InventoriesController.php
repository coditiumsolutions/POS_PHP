<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Inventory;
use App\Models\Product;
use App\Support\CurrentBranch;
use Illuminate\Http\Request;

class InventoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $query = Inventory::with(['product', 'branch', 'warehouse']);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($pq) use ($search) {
                    $pq->where('product_name', 'like', "%{$search}%")
                        ->orWhere('product_code', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                })->orWhereHas('branch', function ($bq) use ($search) {
                    $bq->where('branch_name', 'like', "%{$search}%");
                })->orWhereHas('warehouse', function ($wq) use ($search) {
                    $wq->where('warehouse_name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            });
        }

        $inventories = $query->orderByDesc('uid')->paginate(50)->withQueryString();

        return view('inventories.index', compact('inventories', 'search'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('product_name')->get();
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();
        $defaultBranchUid = CurrentBranch::uid();

        return view('inventories.create', compact('products', 'branches', 'defaultBranchUid'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_uid' => 'required|integer|exists:products,uid',
            'branch_uid' => 'required|integer|exists:branches,uid',
            'quantity_in_stock' => 'nullable|numeric',
            'average_cost' => 'nullable|numeric|min:0',
        ], [
            'branch_uid.required' => 'Branch is required. One inventory record per product and branch.',
        ]);

        $exists = Inventory::where('product_uid', $validated['product_uid'])
            ->where('branch_uid', $validated['branch_uid'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors(['product_uid' => 'Inventory already exists for this product and branch. Edit the existing record instead.']);
        }

        $qty = (float) ($validated['quantity_in_stock'] ?? 0);

        Inventory::create([
            'product_uid' => $validated['product_uid'],
            'branch_uid' => $validated['branch_uid'],
            'warehouse_uid' => null,
            'quantity_in_stock' => $qty,
            'quantity_available' => $qty,
            'quantity_reserved' => 0,
            'average_cost' => $validated['average_cost'] ?? 0,
            'quantity_in' => 0,
            'quantity_out' => 0,
            'updated_date' => now(),
        ]);

        return redirect()
            ->route('inventories.index')
            ->with('success', 'Inventory created successfully.');
    }

    public function show($uid)
    {
        $inventory = Inventory::with(['product', 'branch', 'warehouse'])->findOrFail($uid);

        return view('inventories.show', compact('inventory'));
    }

    public function edit($uid)
    {
        $inventory = Inventory::with(['product', 'branch', 'warehouse'])->findOrFail($uid);
        $products = Product::where('is_active', true)->orderBy('product_name')->get();
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();

        return view('inventories.edit', compact('inventory', 'products', 'branches'));
    }

    public function update(Request $request, $uid)
    {
        $inventory = Inventory::findOrFail($uid);

        $validated = $request->validate([
            'product_uid' => 'required|integer|exists:products,uid',
            'branch_uid' => 'required|integer|exists:branches,uid',
            'quantity_in_stock' => 'required|numeric',
            'average_cost' => 'nullable|numeric|min:0',
        ], [
            'branch_uid.required' => 'Branch is required. One inventory record per product and branch.',
        ]);

        $duplicate = Inventory::where('product_uid', $validated['product_uid'])
            ->where('branch_uid', $validated['branch_uid'])
            ->where('uid', '!=', $inventory->uid)
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors(['product_uid' => 'Another inventory record already exists for this product and branch.']);
        }

        $qty = (float) $validated['quantity_in_stock'];

        $inventory->product_uid = $validated['product_uid'];
        $inventory->branch_uid = $validated['branch_uid'];
        $inventory->quantity_in_stock = $qty;
        $inventory->quantity_available = $qty;
        $inventory->average_cost = $validated['average_cost'] ?? $inventory->average_cost;
        $inventory->updated_date = now();
        $inventory->save();

        return redirect()
            ->route('inventories.index')
            ->with('success', 'Inventory updated successfully.');
    }

    public function destroy($uid)
    {
        $inventory = Inventory::with('product')->findOrFail($uid);
        $label = $inventory->product->product_name ?? ('#' . $inventory->product_uid);
        $inventory->delete();

        return redirect()
            ->route('inventories.index')
            ->with('success', 'Inventory deleted: ' . $label);
    }

    public function printBalance()
    {
        $inventories = Inventory::with(['product', 'branch', 'warehouse'])
            ->get()
            ->sortBy(function ($inv) {
                return strtolower(($inv->product->product_name ?? '') . '|' . $inv->branch_name);
            })
            ->values();

        return view('inventories.print-balance', compact('inventories'));
    }
}
