<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceDetail;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesReturnsController extends Controller
{
    const RETURN_STATUS = 'Return';

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = SalesInvoice::with('customer')
            ->where('invoice_status', self::RETURN_STATUS);

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('remarks', 'like', "%{$search}%");
            });
        }

        $returns = $query->orderBy('invoice_date', 'desc')->paginate(50);
        $totalAmount = $query->sum('net_amount');

        return view('sales-returns.index', compact('returns', 'totalAmount'));
    }

    public function create(Request $request)
    {
        $invoiceUid = $request->get('invoice_uid');
        $originalInvoice = null;

        if ($invoiceUid) {
            $originalInvoice = SalesInvoice::with(['customer', 'details.product'])
                ->where('invoice_status', '!=', self::RETURN_STATUS)
                ->findOrFail($invoiceUid);
        }

        $products = Product::where('is_active', true)->orderBy('product_name')->get();

        return view('sales-returns.create', compact('originalInvoice', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'original_invoice_uid' => 'nullable|exists:sales_invoices,uid',
            'customer_uid' => 'nullable|exists:customers,uid',
            'return_date' => 'required|string',
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
            'remarks' => 'nullable|string|max:500',
        ]);

        $returnDate = $this->parseDate($validated['return_date']);
        if (!$returnDate) {
            return back()->withInput()->withErrors(['return_date' => 'Return date is invalid. Use format like 10-Nov-2026.']);
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $returnNo = $this->generateReturnNo();

            // Create return invoice
            $return = SalesInvoice::create([
                'invoice_no' => $returnNo,
                'invoice_date' => $returnDate,
                'customer_uid' => $validated['customer_uid'],
                'user_uid' => $user->uid,
                'user_name' => $user->user_name,
                'branch' => $user->branch,
                'terminal' => $user->terminal_name,
                'sub_total' => $validated['sub_total'],
                'discount_amount' => $validated['discount_amount'] ?? 0,
                'tax_amount' => $validated['tax_amount'] ?? 0,
                'net_amount' => $validated['net_amount'],
                'paid_amount' => $validated['net_amount'], // Returns are fully refunded
                'balance_amount' => 0,
                'payment_status' => 'Paid',
                'invoice_status' => self::RETURN_STATUS,
                'remarks' => $validated['remarks'] ?? 'Sales Return',
                'created_date' => now(),
            ]);

            // Create return details and reverse stock
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

                SalesInvoiceDetail::create([
                    'sales_invoice_uid' => $return->uid,
                    'product_uid' => $item['product_uid'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount_percent' => $discountPercent,
                    'discount_amount' => $discountAmount,
                    'tax_percent' => $taxPercent,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                ]);

                // Reverse stock - add back to inventory
                $this->reverseInventory($item['product_uid'], $quantity, $returnNo);
            }

            DB::commit();

            return redirect()->route('sales-returns.show', $return->uid)
                ->with('success', 'Sales return processed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error processing return: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($uid)
    {
        $return = SalesInvoice::with(['customer', 'details.product', 'user'])
            ->where('invoice_status', self::RETURN_STATUS)
            ->findOrFail($uid);

        return view('sales-returns.show', compact('return'));
    }

    private function generateReturnNo()
    {
        $date = now()->format('Ymd');
        $lastReturn = SalesInvoice::where('invoice_no', 'like', "SR{$date}%")
            ->orderBy('invoice_no', 'desc')
            ->first();

        if ($lastReturn) {
            $lastNumber = (int)substr($lastReturn->invoice_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "SR{$date}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function reverseInventory($productUid, $quantity, $referenceNo)
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
            'transaction_type' => 'Return',
            'reference_no' => $referenceNo,
            'transaction_date' => now(),
            'quantity' => $quantity,
            'created_date' => now(),
        ]);
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
