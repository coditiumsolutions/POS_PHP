<?php

namespace App\Http\Controllers;

use App\Models\CustomerPayment;
use App\Models\Customer;
use App\Models\SalesInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerPaymentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = CustomerPayment::with(['customer', 'user', 'branch']);

        if ($request->filled('customer_uid')) {
            $query->where('customer_uid', $request->customer_uid);
        }

        $dateFromDisplay = trim((string) $request->get('date_from', ''));
        $dateToDisplay = trim((string) $request->get('date_to', ''));
        $dateFrom = $this->parseDate($dateFromDisplay);
        $dateTo = $this->parseDate($dateToDisplay);

        if ($dateFrom) {
            $query->whereDate('payment_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('payment_date', '<=', $dateTo);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(50)->withQueryString();
        $customers = Customer::where('is_active', true)->orderBy('customer_name')->get();
        $totalAmount = $query->sum('amount');

        return view('customer-payments.index', compact('payments', 'customers', 'totalAmount', 'dateFromDisplay', 'dateToDisplay'));
    }

    public function create(Request $request)
    {
        $customerUid = $request->get('customer_uid');
        $customer = null;
        $pendingInvoices = collect();

        if ($customerUid) {
            $customer = Customer::findOrFail($customerUid);
            $pendingInvoices = SalesInvoice::where('customer_uid', $customerUid)
                ->where('balance_amount', '>', 0)
                ->orderBy('invoice_date')
                ->get();
        }

        $customers = Customer::where('is_active', true)->orderBy('customer_name')->get();

        return view('customer-payments.create', compact('customers', 'customer', 'pendingInvoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_uid' => 'required|exists:customers,uid',
            'payment_date' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|string|max:50',
            'reference_no' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500',
            'apply_to_invoices' => 'nullable|array',
            'apply_to_invoices.*' => 'numeric|min:0',
        ]);

        try {
            $paymentDate = \Carbon\Carbon::createFromFormat('d-M-Y', $validated['payment_date'])->format('Y-m-d');
        } catch (\Throwable $e) {
            try {
                $paymentDate = \Carbon\Carbon::parse($validated['payment_date'])->format('Y-m-d');
            } catch (\Throwable $e2) {
                return back()->withInput()->withErrors(['payment_date' => 'Payment date is invalid. Use format like 10-Nov-2026.']);
            }
        }

        $payment = CustomerPayment::create([
            'payment_no' => $this->generatePaymentNo(),
            'payment_date' => $paymentDate,
            'customer_uid' => $validated['customer_uid'],
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
                    $invoice = SalesInvoice::find($invoiceUid);
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
            $payment->applied_to_invoice_amount = $validated['amount'] - $remainingAmount;
            $payment->save();
        }

        return redirect()->route('customer-payments.index')
            ->with('success', 'Customer payment recorded successfully!');
    }

    private function generatePaymentNo()
    {
        $date = now()->format('Ymd');
        $lastPayment = CustomerPayment::where('payment_no', 'like', "CP{$date}%")
            ->orderBy('payment_no', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = (int)substr($lastPayment->payment_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return "CP{$date}" . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    private function parseDate(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
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
