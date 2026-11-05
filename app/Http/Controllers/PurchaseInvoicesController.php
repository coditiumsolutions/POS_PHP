<?php

namespace App\Http\Controllers;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceDetail;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseInvoicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = PurchaseInvoice::with('vendor');

        if ($request->filled('vendor_uid')) {
            $query->where('vendor_uid', $request->vendor_uid);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('purchase_invoice_no', 'like', "%{$search}%")
                  ->orWhere('vendor_invoice_no', 'like', "%{$search}%");
            });
        }

        $invoices = $query->orderBy('purchase_date', 'desc')->paginate(50);
        $vendors = Vendor::where('is_active', true)->orderBy('vendor_name')->get();

        return view('purchase-invoices.index', compact('invoices', 'vendors'));
    }

    public function create()
    {
        $vendors = Vendor::where('is_active', true)->orderBy('vendor_name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('warehouse_name')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();
        $invoiceNo = $this->generateInvoiceNo();

        return view('purchase-invoices.create', compact('vendors', 'warehouses', 'products', 'invoiceNo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_uid' => 'required|exists:vendors,uid',
            'warehouse_uid' => 'nullable|exists:warehouses,uid',
            'purchase_date' => 'required|date',
            'vendor_invoice_no' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_uid' => 'required|exists:products,uid',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'sub_total' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $invoiceNo = $this->generateInvoiceNo();

            $paidAmount = $validated['paid_amount'] ?? 0;
            $balanceAmount = $validated['net_amount'] - $paidAmount;

            $invoice = PurchaseInvoice::create([
                'purchase_invoice_no' => $invoiceNo,
                'vendor_invoice_no' => $validated['vendor_invoice_no'],
                'purchase_date' => $validated['purchase_date'],
                'vendor_uid' => $validated['vendor_uid'],
                'warehouse_uid' => $validated['warehouse_uid'],
                'user_uid' => $user->uid,
                'sub_total' => $validated['sub_total'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'net_amount' => $validated['net_amount'],
                'paid_amount' => $paidAmount,
                'balance_amount' => $balanceAmount,
                'payment_status' => $balanceAmount > 0 ? 'Partial' : 'Paid',
                'invoice_status' => 'Completed',
                'remarks' => $validated['remarks'],
                'created_date' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                $quantity = $item['quantity'];
                $unitPrice = $item['unit_price'];
                $discountPercent = $item['discount_percent'] ?? 0;
                $taxPercent = $item['tax_percent'] ?? 0;
                
                $lineTotal = $quantity * $unitPrice;
                $discountAmount = $lineTotal * ($discountPercent / 100);
                $taxableAmount = $lineTotal - $discountAmount;
                $taxAmount = $taxableAmount * ($taxPercent / 100);
                $totalAmount = $taxableAmount + $taxAmount;

                PurchaseInvoiceDetail::create([
                    'purchase_invoice_uid' => $invoice->uid,
                    'product_uid' => $item['product_uid'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $discountAmount,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                ]);

                // Stock in
                $this->updateInventory(
                    $item['product_uid'],
                    $quantity,
                    $validated['warehouse_uid'],
                    $invoiceNo,
                    $unitPrice
                );
            }

            DB::commit();

            return redirect()->route('purchase-invoices.show', $invoice->uid)
                ->with('success', 'Purchase invoice created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating purchase invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($uid)
    {
        $invoice = PurchaseInvoice::with(['vendor', 'details.product', 'warehouse', 'user'])
            ->findOrFail($uid);

        return view('purchase-invoices.show', compact('invoice'));
    }

    public function print($uid)
    {
        $invoice = PurchaseInvoice::with(['vendor', 'details.product', 'warehouse'])
            ->findOrFail($uid);

        return view('purchase-invoices.print', compact('invoice'));
    }

    private function generateInvoiceNo()
    {
        $date = now()->format('Ymd');
        $lastInvoice = PurchaseInvoice::where('purchase_invoice_no', 'like', "PI{$date}%")
            ->orderBy('purchase_invoice_no', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int)substr($lastInvoice->purchase_invoice_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "PI{$date}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function updateInventory($productUid, $quantity, $warehouseUid, $referenceNo, $unitCost)
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

        if ($inventory) {
            $inventory->quantity_in_stock += $quantity;
            $inventory->quantity_available += $quantity;
            
            // Update average cost
            $oldTotal = $inventory->average_cost * ($inventory->quantity_in_stock - $quantity);
            $newTotal = $oldTotal + ($unitCost * $quantity);
            $inventory->average_cost = $inventory->quantity_in_stock > 0 
                ? $newTotal / $inventory->quantity_in_stock 
                : $unitCost;
            
            $inventory->updated_date = now();
            $inventory->save();
        } else {
            Inventory::create([
                'product_uid' => $productUid,
                'warehouse_uid' => $warehouseUid,
                'quantity_in_stock' => $quantity,
                'quantity_available' => $quantity,
                'average_cost' => $unitCost,
                'updated_date' => now(),
            ]);
        }

        InventoryTransaction::create([
            'product_uid' => $productUid,
            'warehouse_uid' => $warehouseUid,
            'transaction_type' => 'Purchase',
            'reference_no' => $referenceNo,
            'transaction_date' => now(),
            'quantity' => $quantity,
            'unit_cost' => $unitCost,
            'total_cost' => $quantity * $unitCost,
            'created_date' => now(),
        ]);
    }
}
