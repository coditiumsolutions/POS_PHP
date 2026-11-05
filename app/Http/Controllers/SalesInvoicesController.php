<?php

namespace App\Http\Controllers;

use App\Models\SalesInvoice;
use App\Models\SalesInvoiceDetail;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesInvoicesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = SalesInvoice::with('customer')
            ->where('invoice_status', '!=', 'Return')
            ->where('invoice_status', '!=', 'Wastage');

        if ($request->filled('customer_type')) {
            $type = $request->customer_type;
            if ($type === 'walkin') {
                $query->whereHas('customer', function($q) {
                    $q->where('customer_code', 'C001')
                      ->orWhere('customer_name', 'like', '%Walk-in%');
                });
            } elseif ($type === 'credit') {
                $query->whereHas('customer', function($q) {
                    $q->where('customer_code', '!=', 'C001')
                      ->where('customer_name', 'not like', '%Walk-in%')
                      ->whereNull('remarks');
                });
            } elseif ($type === 'monthly') {
                $query->where('remarks', 'like', 'Monthly Supply%');
            }
        }

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
                  ->orWhere('remarks', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('customer_name', 'like', "%{$search}%");
                  });
            });
        }

        $invoices = $query->orderBy('invoice_date', 'desc')
            ->orderBy('uid', 'desc')
            ->paginate(50);

        $totalNet = $query->sum('net_amount');
        $totalPaid = $query->sum('paid_amount');

        return view('sales-invoices.index', compact('invoices', 'totalNet', 'totalPaid'));
    }

    public function show($uid)
    {
        $invoice = SalesInvoice::with(['customer', 'details.product', 'paymentMethod', 'user', 'branch'])
            ->findOrFail($uid);

        return view('sales-invoices.show', compact('invoice'));
    }

    public function print($uid)
    {
        $invoice = SalesInvoice::with(['customer', 'details.product', 'paymentMethod'])
            ->findOrFail($uid);

        return view('sales-invoices.print', compact('invoice'));
    }

    public function destroy($uid)
    {
        DB::beginTransaction();
        try {
            $invoice = SalesInvoice::with('details')->findOrFail($uid);
            
            // Reverse inventory
            foreach ($invoice->details as $detail) {
                $this->updateInventory($detail->product_uid, $detail->quantity, "Void-{$invoice->invoice_no}");
            }

            $invoice->invoice_status = 'Cancelled';
            $invoice->save();

            DB::commit();
            return redirect()->route('sales-invoices.index')->with('success', 'Invoice cancelled successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error cancelling invoice: ' . $e->getMessage());
        }
    }

    private function updateInventory($productUid, $quantity, $referenceNo)
    {
        $inventory = Inventory::where('product_uid', $productUid)
            ->whereNull('warehouse_uid')
            ->first();

        if ($inventory) {
            $inventory->quantity_in_stock += $quantity;
            $inventory->quantity_available += $quantity;
            $inventory->updated_date = now();
            $inventory->save();
        }

        InventoryTransaction::create([
            'product_uid' => $productUid,
            'transaction_type' => $quantity < 0 ? 'Sale' : 'Return',
            'reference_no' => $referenceNo,
            'transaction_date' => now(),
            'quantity' => abs($quantity),
            'created_date' => now(),
        ]);
    }
}
