<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceDetail;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\PaymentMethod;
use App\Models\ProductCategory;
use App\Support\CurrentBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return redirect()->route('pos.cash');
    }

    public function cash()
    {
        return $this->salePage('POS - Cash Sale', false);
    }

    /**
     * Cash POS shell with barcode scanner field (POS_MT POSController::Barcode parity).
     */
    public function barcode()
    {
        return $this->salePage('POS Barcode', true);
    }

    private function salePage(string $pageTitle, bool $enableBarcodeScan)
    {
        $branchUid = CurrentBranch::uid();
        $branchName = CurrentBranch::name() ?: (Auth::user()->branch ?? null);

        $productsQuery = Product::with(['category', 'unit', 'branch'])
            ->where('is_active', true);

        // POS shows only products for the logged-in user's branch.
        if ($branchUid) {
            $productsQuery->where('branch_uid', $branchUid);
        }

        $products = $productsQuery->orderBy('product_name')->get();

        $categories = ProductCategory::where('is_active', true)
            ->orderBy('category_name')
            ->get();

        // Cash POS always settles as Cash / Walk-in (no customer or payment dropdowns).
        $cashPaymentMethod = PaymentMethod::where('is_active', true)
            ->whereRaw('LOWER(payment_method_name) = ?', ['cash'])
            ->orderBy('uid')
            ->first()
            ?? PaymentMethod::where('is_active', true)->orderBy('uid')->first();

        return view('pos.cash', [
            'products' => $products,
            'categories' => $categories,
            'cashPaymentMethodUid' => $cashPaymentMethod?->uid,
            'pageTitle' => $pageTitle,
            'enableBarcodeScan' => $enableBarcodeScan,
            'currentBranchName' => $branchName,
            'currentBranchUid' => $branchUid,
        ]);
    }

    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');
        $branchUid = CurrentBranch::uid();

        $products = Product::with(['category', 'unit'])
            ->where('is_active', true)
            ->when($branchUid, fn ($q) => $q->where('branch_uid', $branchUid))
            ->where(function ($query) use ($search) {
                $query->where('product_name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->limit(50)
            ->get()
            ->map(function ($product) {
                return [
                    'uid' => $product->uid,
                    'product_code' => $product->product_code,
                    'product_name' => $product->product_name,
                    'barcode' => $product->barcode,
                    'sale_price' => $product->sale_price,
                    'tax_percent' => $product->tax_percent,
                    'discount_percent' => $product->discount_percent,
                    'category_name' => $product->category ? $product->category->category_name : '',
                    'unit_name' => $product->unit ? $product->unit->unit_name : '',
                ];
            });

        return response()->json($products);
    }

    public function createSale(Request $request)
    {
        $validated = $request->validate([
            'customer_uid' => 'nullable|integer|exists:customers,uid',
            'payment_method_uid' => 'required|integer|exists:payment_methods,uid',
            'items' => 'required|array|min:1',
            'items.*.product_uid' => 'required|integer|exists:products,uid',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
            'sub_total' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'net_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'customer_given_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $branchUid = CurrentBranch::requireUid($user);
            $branchName = CurrentBranch::name($user) ?: $user->branch;

            $invoiceNo = $this->generateInvoiceNo();

            $paidAmount = $validated['paid_amount'];
            $netAmount = $validated['net_amount'];
            $balanceAmount = $netAmount - $paidAmount;

            $customerGivenAmount = $validated['customer_given_amount'] ?? null;
            $changeGivenAmount = $customerGivenAmount ? ($customerGivenAmount - $netAmount) : null;

            $invoice = SalesInvoice::create([
                'invoice_no' => $invoiceNo,
                'invoice_date' => now(),
                'customer_uid' => $validated['customer_uid'] ?? null,
                'user_uid' => $user->uid,
                'user_name' => $user->user_name,
                'branch' => $branchName,
                'terminal' => $user->terminal_name,
                'payment_method_uid' => $validated['payment_method_uid'],
                'sub_total' => $validated['sub_total'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'net_amount' => $netAmount,
                'paid_amount' => $paidAmount,
                'customer_given_amount' => $customerGivenAmount,
                'change_given_amount' => $changeGivenAmount,
                'balance_amount' => $balanceAmount,
                'payment_status' => $balanceAmount > 0 ? 'Partial' : 'Paid',
                'invoice_status' => 'Completed',
                'pos_type' => 'Walk-In',
                'remarks' => $validated['remarks'] ?? null,
                'created_date' => now(),
            ]);

            foreach ($validated['items'] as $item) {
                $quantity = $item['quantity'];
                $unitPrice = $item['unit_price'];
                $discountPercent = $item['discount_percent'] ?? 0;
                $taxPercent = $item['tax_percent'] ?? 0;

                $product = Product::find($item['product_uid']);
                if ($product && $product->branch_uid && (int) $product->branch_uid !== (int) $branchUid) {
                    throw new \RuntimeException(
                        'Product "' . $product->product_name . '" belongs to another branch and cannot be sold here.'
                    );
                }

                $lineTotal = $quantity * $unitPrice;
                $discountAmount = $lineTotal * ($discountPercent / 100);
                $taxableAmount = $lineTotal - $discountAmount;
                $taxAmount = $taxableAmount * ($taxPercent / 100);
                $totalAmount = $taxableAmount + $taxAmount;

                SalesInvoiceDetail::create([
                    'sales_invoice_uid' => $invoice->uid,
                    'product_uid' => $item['product_uid'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $discountAmount,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                ]);

                // Deduct stock from the logged-in branch inventory only.
                $this->updateInventory($item['product_uid'], -$quantity, $invoiceNo, $branchUid);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice_uid' => $invoice->uid,
                'invoice_no' => $invoice->invoice_no,
                'message' => 'Sale completed successfully!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating sale: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function generateInvoiceNo()
    {
        $date = now()->format('Ymd');
        $lastInvoice = SalesInvoice::where('invoice_no', 'like', "SI{$date}%")
            ->orderBy('invoice_no', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int)substr($lastInvoice->invoice_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "SI{$date}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function updateInventory($productUid, $quantity, $referenceNo, $branchUid)
    {
        $inventory = Inventory::findForProductBranch((int) $productUid, (int) $branchUid);

        if ($inventory) {
            $inventory->quantity_in_stock = (float) $inventory->quantity_in_stock + $quantity;
            $inventory->quantity_available = (float) $inventory->quantity_available + $quantity;
            if ($quantity < 0) {
                $inventory->quantity_out = (float) ($inventory->quantity_out ?? 0) + abs($quantity);
            } else {
                $inventory->quantity_in = (float) ($inventory->quantity_in ?? 0) + $quantity;
            }
            $inventory->updated_date = now();
            $inventory->save();
        } else {
            $startQty = $quantity; // may be negative for first sale without prior stock
            Inventory::create([
                'product_uid' => $productUid,
                'branch_uid' => $branchUid,
                'warehouse_uid' => null,
                'quantity_in_stock' => $startQty,
                'quantity_available' => $startQty,
                'quantity_reserved' => 0,
                'quantity_in' => $quantity > 0 ? $quantity : 0,
                'quantity_out' => $quantity < 0 ? abs($quantity) : 0,
                'updated_date' => now(),
            ]);
        }

        InventoryTransaction::create([
            'product_uid' => $productUid,
            'warehouse_uid' => null,
            'transaction_type' => $quantity < 0 ? 'Sale' : 'Return',
            'reference_no' => $referenceNo,
            'transaction_date' => now(),
            'quantity' => abs($quantity),
            'quantity_in' => $quantity > 0 ? abs($quantity) : 0,
            'quantity_out' => $quantity < 0 ? abs($quantity) : 0,
            'user_uid' => Auth::id(),
            'created_date' => now(),
        ]);
    }

    public function printInvoice($uid)
    {
        $invoice = SalesInvoice::with(['customer', 'details.product', 'paymentMethod'])
            ->findOrFail($uid);

        return view('pos.print-invoice', compact('invoice'));
    }
}

