<?php

namespace App\Http\Controllers;

use App\Models\VendorPayment;
use App\Models\Vendor;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorPaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = VendorPayment::with(['vendor', 'user', 'branch']);

        if ($request->filled('vendor_uid')) {
            $query->where('vendor_uid', $request->vendor_uid);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(50);
        $vendors = Vendor::where('is_active', true)->orderBy('vendor_name')->get();
        $totalAmount = $query->sum('amount');

        return view('vendor-payments.index', compact('payments', 'vendors', 'totalAmount'));
    }

    public function create(Request $request)
    {
        $vendorUid = $request->get('vendor_uid');
        $vendor = null;
        $pendingInvoices = collect();

        if ($vendorUid) {
            $vendor = Vendor::findOrFail($vendorUid);
            $pendingInvoices = PurchaseInvoice::where('vendor_uid', $vendorUid)
                ->where('balance_amount', '>', 0)
                ->orderBy('purchase_date')
                ->get();
        }

        $vendors = Vendor::where('is_active', true)->orderBy('vendor_name')->get();

        return view('vendor-payments.create', compact('vendors', 'vendor', 'pendingInvoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_uid' => 'required|exists:vendors,uid',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|string|max:50',
            'reference_no' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500',
            'apply_to_invoices' => 'nullable|array',
            'apply_to_invoices.*' => 'numeric|min:0',
        ]);

        $payment = VendorPayment::create([
            'payment_no' => $this->generatePaymentNo(),
            'payment_date' => $validated['payment_date'],
            'vendor_uid' => $validated['vendor_uid'],
            'user_uid' => Auth::id(),
            'amount' => $validated['amount'],
            'payment_mode' => $validated['payment_mode'],
            'reference_no' => $validated['reference_no'],
            'remarks' => $validated['remarks'],
            'created_date' => now(),
        ]);

        // Apply to specific invoices if provided
        if (!empty($validated['apply_to_invoices'])) {
            $remainingAmount = $validated['amount'];
            foreach ($validated['apply_to_invoices'] as $invoiceUid => $applyAmount) {
                if ($applyAmount > 0 && $remainingAmount > 0) {
                    $invoice = PurchaseInvoice::find($invoiceUid);
                    if ($invoice && $invoice->balance_amount > 0) {
                        $amountToApply = min($applyAmount, $remainingAmount, $invoice->balance_amount);
                        $invoice->paid_amount += $amountToApply;
                        $invoice->balance_amount -= $amountToApply;
                        if ($invoice->balance_amount <= 0) {
                            $invoice->payment_status = 'Paid';
                        }
                        $invoice->save();
                        $remainingAmount -= $amountToApply;
                    }
                }
            }
        }

        return redirect()->route('vendor-payments.index')
            ->with('success', 'Vendor payment recorded successfully!');
    }

    private function generatePaymentNo()
    {
        $date = now()->format('Ymd');
        $lastPayment = VendorPayment::where('payment_no', 'like', "VP{$date}%")
            ->orderBy('payment_no', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = (int)substr($lastPayment->payment_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "VP{$date}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
