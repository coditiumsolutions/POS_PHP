<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceDetail;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Support\CurrentBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();
        $defaultBranchUid = CurrentBranch::uid();
        $products = Product::where('is_active', true)
            ->when($defaultBranchUid, fn ($q) => $q->where('branch_uid', $defaultBranchUid))
            ->orderBy('product_name')
            ->get();
        $invoiceNo = $this->generateInvoiceNo();

        return view('purchase-invoices.create', compact('vendors', 'branches', 'products', 'invoiceNo', 'defaultBranchUid'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'vendor_uid' => 'required|exists:vendors,uid',
                'branch_uid' => 'required|exists:branches,uid',
                'purchase_date' => 'required|date',
                'vendor_invoice_no' => 'nullable|string|max:100',
                'items' => 'required|array|min:1|max:5',
                'items.*.product_uid' => 'nullable|exists:products,uid',
                'items.*.quantity' => 'nullable|numeric|min:0',
                'items.*.unit_price' => 'nullable|numeric|min:0',
                'paid_amount' => 'nullable|numeric|min:0',
                'remarks' => 'nullable|string|max:500',
            ],
            [
                'vendor_uid.required' => 'Please select a vendor before saving.',
                'vendor_uid.exists' => 'Selected vendor is invalid.',
                'branch_uid.required' => 'Branch is required. Stock is added for that branch only.',
                'branch_uid.exists' => 'Selected branch is invalid.',
                'purchase_date.required' => 'Purchase date is required.',
                'purchase_date.date' => 'Purchase date is not a valid date.',
                'items.required' => 'Add at least one purchase item.',
                'items.max' => 'You can add a maximum of 5 items per purchase.',
                'items.*.product_uid.exists' => 'One of the selected products is invalid.',
                'items.*.quantity.numeric' => 'Quantity must be a number.',
                'items.*.quantity.min' => 'Quantity cannot be negative.',
                'items.*.unit_price.numeric' => 'Unit cost must be a number.',
                'items.*.unit_price.min' => 'Unit cost cannot be negative.',
                'paid_amount.numeric' => 'Paid amount must be a number.',
            ],
            [
                'vendor_uid' => 'vendor',
                'branch_uid' => 'branch',
                'purchase_date' => 'purchase date',
                'paid_amount' => 'paid amount',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Purchase was not saved. Please fix the highlighted issues and try again.');
        }

        $validated = $validator->validated();
        $lineErrors = [];
        $normalizedLines = [];

        foreach (($validated['items'] ?? []) as $index => $item) {
            $lineNo = $index + 1;
            $productUid = $item['product_uid'] ?? null;
            $quantity = isset($item['quantity']) ? (float) $item['quantity'] : 0;
            $unitPrice = isset($item['unit_price']) ? (float) $item['unit_price'] : null;

            // Skip completely empty rows
            if (empty($productUid) && $quantity <= 0 && ($unitPrice === null || $unitPrice === 0.0)) {
                continue;
            }

            if (empty($productUid)) {
                $lineErrors["items.$index.product_uid"] = "Line {$lineNo}: select a product.";
                continue;
            }

            if ($quantity <= 0) {
                $lineErrors["items.$index.quantity"] = "Line {$lineNo}: enter quantity greater than 0.";
                continue;
            }

            if ($unitPrice === null || $unitPrice < 0) {
                $lineErrors["items.$index.unit_price"] = "Line {$lineNo}: enter a valid unit cost (0 or more).";
                continue;
            }

            $normalizedLines[] = [
                'product_uid' => (int) $productUid,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => round($quantity * $unitPrice, 2),
            ];
        }

        if (!empty($lineErrors)) {
            return redirect()->back()
                ->withErrors($lineErrors)
                ->withInput()
                ->with('error', 'Purchase was not saved. Check the item lines for errors.');
        }

        if (count($normalizedLines) < 1) {
            return redirect()->back()
                ->withErrors(['items' => 'Add at least one item with product and quantity before saving.'])
                ->withInput()
                ->with('error', 'Purchase was not saved. No valid items were found.');
        }

        if (count($normalizedLines) > 5) {
            return redirect()->back()
                ->withErrors(['items' => 'You can add a maximum of 5 items per purchase.'])
                ->withInput()
                ->with('error', 'Purchase was not saved. Too many items.');
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \RuntimeException('You must be logged in to save a purchase.');
            }

            $invoiceNo = $this->generateInvoiceNo();
            $netAmount = round(collect($normalizedLines)->sum('total_amount'), 2);
            $paidAmount = (float) ($validated['paid_amount'] ?? 0);
            $balanceAmount = round($netAmount - $paidAmount, 2);
            $branchUid = (int) $validated['branch_uid'];

            $invoice = PurchaseInvoice::create([
                'purchase_invoice_no' => $invoiceNo,
                'vendor_invoice_no' => $validated['vendor_invoice_no'] ?? null,
                'purchase_date' => $validated['purchase_date'],
                'vendor_uid' => $validated['vendor_uid'],
                'branch_uid' => $branchUid,
                'warehouse_uid' => null,
                'user_uid' => $user->uid,
                'sub_total' => $netAmount,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'other_charges' => 0,
                'net_amount' => $netAmount,
                'paid_amount' => $paidAmount,
                'balance_amount' => $balanceAmount,
                'payment_status' => $balanceAmount <= 0 ? 'Paid' : ($paidAmount > 0 ? 'Partial' : 'Unpaid'),
                'invoice_status' => 'Completed',
                'remarks' => $validated['remarks'] ?? null,
                'created_date' => now(),
            ]);

            foreach ($normalizedLines as $item) {
                PurchaseInvoiceDetail::create([
                    'purchase_invoice_uid' => $invoice->uid,
                    'product_uid' => $item['product_uid'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount_percent' => 0,
                    'discount_amount' => 0,
                    'tax_percent' => 0,
                    'tax_amount' => 0,
                    'total_amount' => $item['total_amount'],
                ]);

                $this->updateInventory(
                    $item['product_uid'],
                    $item['quantity'],
                    $branchUid,
                    $invoiceNo,
                    $item['unit_price']
                );
            }

            DB::commit();

            return redirect()->route('purchase-invoices.show', $invoice->uid)
                ->with('success', 'Purchase invoice ' . $invoiceNo . ' saved successfully. Inventory quantities were updated.');

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Purchase invoice save failed', [
                'message' => $e->getMessage(),
                'user_uid' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Purchase was not saved. Reason: ' . $e->getMessage());
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

    private function updateInventory($productUid, $quantity, $branchUid, $referenceNo, $unitCost)
    {
        $inventory = Inventory::findForProductBranch((int) $productUid, (int) $branchUid);

        if ($inventory) {
            $previousQty = (float) $inventory->quantity_in_stock;
            $inventory->quantity_in_stock = $previousQty + $quantity;
            $inventory->quantity_available = (float) $inventory->quantity_available + $quantity;
            $inventory->quantity_in = (float) ($inventory->quantity_in ?? 0) + $quantity;

            $oldValue = (float) $inventory->average_cost * $previousQty;
            $newValue = $oldValue + ($unitCost * $quantity);
            $inventory->average_cost = $inventory->quantity_in_stock > 0
                ? round($newValue / (float) $inventory->quantity_in_stock, 2)
                : $unitCost;

            $inventory->updated_date = now();
            $inventory->save();
        } else {
            Inventory::create([
                'product_uid' => $productUid,
                'branch_uid' => $branchUid,
                'warehouse_uid' => null,
                'quantity_in_stock' => $quantity,
                'quantity_available' => $quantity,
                'quantity_reserved' => 0,
                'quantity_in' => $quantity,
                'quantity_out' => 0,
                'average_cost' => $unitCost,
                'updated_date' => now(),
            ]);
        }

        InventoryTransaction::create([
            'product_uid' => $productUid,
            'warehouse_uid' => null,
            'transaction_type' => 'Purchase',
            'reference_no' => $referenceNo,
            'transaction_date' => now(),
            'quantity' => $quantity,
            'quantity_in' => $quantity,
            'quantity_out' => 0,
            'unit_cost' => $unitCost,
            'total_cost' => round($quantity * $unitCost, 2),
            'user_uid' => Auth::id(),
            'created_date' => now(),
        ]);
    }
}
