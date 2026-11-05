<?php

namespace App\Http\Controllers;

use App\Models\CustomerMonthlySupply;
use App\Models\CustomerMonthlySupplyOverride;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MonthlySupplyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $supplies = CustomerMonthlySupply::with(['customer', 'product'])->paginate(50);
        return view('monthly-supply.index', compact('supplies'));
    }

    public function create()
    {
        $customers = Customer::where('is_active', true)->orderBy('customer_name')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();

        return view('monthly-supply.create', compact('customers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_uid' => 'required|exists:customers,uid',
            'product_uid' => 'required|exists:products,uid',
            'day_of_month' => 'required|integer|min:1|max:31',
            'default_quantity' => 'required|numeric|min:0.001',
            'is_active' => 'boolean',
        ]);

        CustomerMonthlySupply::create($validated);

        return redirect()->route('monthly-supply.index')->with('success', 'Monthly supply template created!');
    }

    public function edit($uid)
    {
        $supply = CustomerMonthlySupply::findOrFail($uid);
        $customers = Customer::where('is_active', true)->orderBy('customer_name')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();

        return view('monthly-supply.edit', compact('supply', 'customers', 'products'));
    }

    public function update(Request $request, $uid)
    {
        $supply = CustomerMonthlySupply::findOrFail($uid);

        $validated = $request->validate([
            'customer_uid' => 'required|exists:customers,uid',
            'product_uid' => 'required|exists:products,uid',
            'day_of_month' => 'required|integer|min:1|max:31',
            'default_quantity' => 'required|numeric|min:0.001',
            'is_active' => 'boolean',
        ]);

        $supply->update($validated);

        return redirect()->route('monthly-supply.index')->with('success', 'Monthly supply template updated!');
    }

    public function overrides(Request $request)
    {
        $query = CustomerMonthlySupplyOverride::with(['customer', 'product']);

        if ($request->filled('date_from')) {
            $query->whereDate('override_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('override_date', '<=', $request->date_to);
        }

        $overrides = $query->orderBy('override_date', 'desc')->paginate(50);

        return view('monthly-supply.overrides', compact('overrides'));
    }

    public function storeOverride(Request $request)
    {
        $validated = $request->validate([
            'customer_uid' => 'required|exists:customers,uid',
            'product_uid' => 'required|exists:products,uid',
            'override_date' => 'required|date',
            'override_quantity' => 'required|numeric|min:0',
        ]);

        CustomerMonthlySupplyOverride::create($validated);

        return redirect()->route('monthly-supply.overrides')->with('success', 'Override created!');
    }

    public function generateInvoices(Request $request)
    {
        $validated = $request->validate([
            'generation_date' => 'required|date',
        ]);

        $date = Carbon::parse($validated['generation_date']);
        $dayOfMonth = $date->day;

        $supplies = CustomerMonthlySupply::with(['customer', 'product'])
            ->where('is_active', true)
            ->where('day_of_month', $dayOfMonth)
            ->get();

        DB::beginTransaction();
        try {
            $generatedCount = 0;
            $user = Auth::user();

            foreach ($supplies as $supply) {
                // Check for override
                $override = CustomerMonthlySupplyOverride::where('customer_uid', $supply->customer_uid)
                    ->where('product_uid', $supply->product_uid)
                    ->whereDate('override_date', $date)
                    ->first();

                $quantity = $override ? $override->override_quantity : $supply->default_quantity;

                if ($quantity > 0) {
                    $product = $supply->product;
                    $unitPrice = $product->sale_price ?? 0;
                    $lineTotal = $quantity * $unitPrice;

                    $invoiceNo = $this->generateInvoiceNo();

                    $invoice = SalesInvoice::create([
                        'invoice_no' => $invoiceNo,
                        'invoice_date' => $date,
                        'customer_uid' => $supply->customer_uid,
                        'user_uid' => $user->uid,
                        'user_name' => $user->user_name,
                        'branch' => $user->branch,
                        'sub_total' => $lineTotal,
                        'discount_amount' => 0,
                        'tax_amount' => 0,
                        'net_amount' => $lineTotal,
                        'paid_amount' => 0,
                        'balance_amount' => $lineTotal,
                        'payment_status' => 'Pending',
                        'invoice_status' => 'Completed',
                        'remarks' => 'Auto-generated from Monthly Supply',
                        'created_date' => now(),
                    ]);

                    SalesInvoiceDetail::create([
                        'sales_invoice_uid' => $invoice->uid,
                        'product_uid' => $supply->product_uid,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'discount_percent' => 0,
                        'discount_amount' => 0,
                        'tax_percent' => 0,
                        'tax_amount' => 0,
                        'total_amount' => $lineTotal,
                    ]);

                    $generatedCount++;
                }
            }

            DB::commit();

            return redirect()->route('monthly-supply.index')
                ->with('success', "Generated {$generatedCount} invoices for {$date->format('Y-m-d')}!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error generating invoices: ' . $e->getMessage());
        }
    }

    private function generateInvoiceNo()
    {
        $date = now()->format('Ymd');
        $lastInvoice = SalesInvoice::where('invoice_no', 'like', "MS{$date}%")
            ->orderBy('invoice_no', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int)substr($lastInvoice->invoice_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "MS{$date}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
