<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $query = Vendor::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('vendor_name', 'like', "%{$search}%")
                  ->orWhere('vendor_code', 'like', "%{$search}%")
                  ->orWhere('mobile_no', 'like', "%{$search}%");
            });
        }

        $vendors = $query->orderBy('vendor_name')->paginate(50);
        return view('vendors.index', compact('vendors'));
    }

    public function create()
    {
        $vendorCode = $this->generateVendorCode();
        return view('vendors.create', compact('vendorCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name' => 'required|string|max:200',
            'contact_person' => 'nullable|string|max:150',
            'mobile_no' => 'nullable|string|max:30',
            'phone_no' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'ntn' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric',
            'credit_limit' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        $validated['vendor_code'] = $this->generateVendorCode();
        $validated['created_date'] = now();
        Vendor::create($validated);

        return redirect()->route('vendors.index')->with('success', 'Vendor created successfully!');
    }

    public function edit($uid)
    {
        $vendor = Vendor::findOrFail($uid);
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, $uid)
    {
        $vendor = Vendor::findOrFail($uid);

        $validated = $request->validate([
            'vendor_code' => 'required|string|max:50|unique:vendors,vendor_code,' . $uid . ',uid',
            'vendor_name' => 'required|string|max:200',
            'contact_person' => 'nullable|string|max:150',
            'mobile_no' => 'nullable|string|max:30',
            'phone_no' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'ntn' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric',
            'credit_limit' => 'nullable|numeric',
            'is_active' => 'boolean',
        ]);

        $validated['updated_date'] = now();
        $vendor->update($validated);

        return redirect()->route('vendors.index')->with('success', 'Vendor updated successfully!');
    }

    public function show($uid)
    {
        $vendor = Vendor::with(['purchaseInvoices', 'payments'])->findOrFail($uid);
        
        $totalPurchases = $vendor->purchaseInvoices()->sum('net_amount');
        $totalPaid = $vendor->purchaseInvoices()->sum('paid_amount');
        $balance = $totalPurchases - $totalPaid;

        return view('vendors.show', compact('vendor', 'totalPurchases', 'totalPaid', 'balance'));
    }

    public function destroy($uid)
    {
        $vendor = Vendor::findOrFail($uid);
        $vendor->update(['is_active' => false]);
        return redirect()->route('vendors.index')->with('success', 'Vendor deactivated successfully!');
    }

    private function generateVendorCode()
    {
        $lastVendor = Vendor::where('vendor_code', 'like', 'VEN-%')
            ->orderBy('vendor_code', 'desc')
            ->first();

        if ($lastVendor) {
            $lastNumber = (int)str_replace('VEN-', '', $lastVendor->vendor_code);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1000;
        }

        return 'VEN-' . $newNumber;
    }
}
