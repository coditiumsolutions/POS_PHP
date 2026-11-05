<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Customer::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%")
                  ->orWhere('mobile_no', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('customer_name')->paginate(50);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_code' => 'required|string|max:50|unique:customers',
            'customer_name' => 'required|string|max:200',
            'mobile_no' => 'nullable|string|max:30',
            'phone_no' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'area' => 'nullable|string|max:100',
            'ntn' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric',
            'credit_limit' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['created_date'] = now();
        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created successfully!');
    }

    public function edit($uid)
    {
        $customer = Customer::findOrFail($uid);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $uid)
    {
        $customer = Customer::findOrFail($uid);

        $validated = $request->validate([
            'customer_code' => 'required|string|max:50|unique:customers,customer_code,' . $uid . ',uid',
            'customer_name' => 'required|string|max:200',
            'mobile_no' => 'nullable|string|max:30',
            'phone_no' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'area' => 'nullable|string|max:100',
            'ntn' => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric',
            'credit_limit' => 'nullable|numeric',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['updated_date'] = now();
        $customer->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }

    public function destroy($uid)
    {
        $customer = Customer::findOrFail($uid);
        $customer->update(['is_active' => false]);
        return redirect()->route('customers.index')->with('success', 'Customer deactivated successfully!');
    }
}
