<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Models\PurchaseInvoice;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Support\CurrentBranch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        $totalAmount = (clone $query)->sum('amount');
        $payments = $query->orderBy('payment_date', 'desc')->paginate(50)->withQueryString();
        $vendors = Vendor::where('is_active', true)->orderBy('vendor_name')->get();

        return view('vendor-payments.index', compact('payments', 'vendors', 'totalAmount', 'dateFromDisplay', 'dateToDisplay'));
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
                ->where('invoice_status', '!=', 'Cancelled')
                ->orderBy('purchase_date')
                ->get();
        }

        $vendors = Vendor::where('is_active', true)->orderBy('vendor_name')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('payment_method_name')->get();
        $paymentNo = $this->generatePaymentNo();
        $defaultBranchUid = CurrentBranch::uid();

        return view('vendor-payments.create', compact(
            'vendors',
            'vendor',
            'pendingInvoices',
            'paymentMethods',
            'paymentNo',
            'defaultBranchUid'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_uid' => 'required|exists:vendors,uid',
            'payment_date' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_mode' => 'required|string|max:50',
            'payment_method_uid' => 'nullable|exists:payment_methods,uid',
            'reference_no' => 'nullable|string|max:100',
            'remarks' => 'nullable|string|max:500',
            'branch_uid' => 'nullable|exists:branches,uid',
            'apply_to_invoices' => 'nullable|array',
            'apply_to_invoices.*' => 'nullable|numeric|min:0',
            'auto_apply' => 'nullable|boolean',
        ], [
            'vendor_uid.required' => 'Please select a vendor.',
            'amount.min' => 'Payment amount must be greater than 0.',
            'payment_mode.required' => 'Please select a payment mode.',
        ]);

        $paymentDate = $this->parseDate($validated['payment_date']);
        if (!$paymentDate) {
            return back()->withInput()->withErrors(['payment_date' => 'Payment date is invalid. Use format like 10-Nov-2026.']);
        }

        $amount = round((float) $validated['amount'], 2);
        $branchUid = $validated['branch_uid'] ?: CurrentBranch::uid();

        DB::beginTransaction();
        try {
            $payment = VendorPayment::create([
                'payment_no' => $this->generatePaymentNo(),
                'payment_date' => $paymentDate,
                'vendor_uid' => $validated['vendor_uid'],
                'branch_uid' => $branchUid,
                'user_uid' => Auth::id(),
                'amount' => $amount,
                'payment_mode' => $validated['payment_mode'],
                'payment_method_uid' => $validated['payment_method_uid'] ?? null,
                'reference_no' => $validated['reference_no'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'created_date' => now(),
            ]);

            $applyMap = collect($validated['apply_to_invoices'] ?? [])
                ->map(fn ($v) => (float) $v)
                ->filter(fn ($v) => $v > 0);

            $remaining = $amount;

            if ($applyMap->isNotEmpty()) {
                foreach ($applyMap as $invoiceUid => $applyAmount) {
                    if ($remaining <= 0) {
                        break;
                    }
                    $invoice = PurchaseInvoice::where('uid', $invoiceUid)
                        ->where('vendor_uid', $validated['vendor_uid'])
                        ->first();
                    if (!$invoice || (float) $invoice->balance_amount <= 0) {
                        continue;
                    }
                    $amountToApply = min($applyAmount, $remaining, (float) $invoice->balance_amount);
                    $this->applyToInvoice($invoice, $amountToApply);
                    $remaining = round($remaining - $amountToApply, 2);
                }
            } elseif ($request->boolean('auto_apply', true)) {
                $pending = PurchaseInvoice::where('vendor_uid', $validated['vendor_uid'])
                    ->where('balance_amount', '>', 0)
                    ->where('invoice_status', '!=', 'Cancelled')
                    ->orderBy('purchase_date')
                    ->get();

                foreach ($pending as $invoice) {
                    if ($remaining <= 0) {
                        break;
                    }
                    $amountToApply = min($remaining, (float) $invoice->balance_amount);
                    $this->applyToInvoice($invoice, $amountToApply);
                    $remaining = round($remaining - $amountToApply, 2);
                }
            }

            DB::commit();

            return redirect()->route('vendor-payments.index')
                ->with('success', 'Vendor payment ' . $payment->payment_no . ' saved successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Payment was not saved. ' . $e->getMessage());
        }
    }

    private function applyToInvoice(PurchaseInvoice $invoice, float $amountToApply): void
    {
        $invoice->paid_amount = round((float) $invoice->paid_amount + $amountToApply, 2);
        $invoice->balance_amount = round((float) $invoice->balance_amount - $amountToApply, 2);
        if ($invoice->balance_amount <= 0) {
            $invoice->balance_amount = 0;
            $invoice->payment_status = 'Paid';
        } elseif ((float) $invoice->paid_amount > 0) {
            $invoice->payment_status = 'Partial';
        }
        $invoice->save();
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

    private function generatePaymentNo()
    {
        $date = now()->format('Ymd');
        $lastPayment = VendorPayment::where('payment_no', 'like', "VP{$date}%")
            ->orderBy('payment_no', 'desc')
            ->first();

        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->payment_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'VP' . $date . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
