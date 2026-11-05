<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $branches = Branch::orderBy('branch_name')->paginate(50);
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        $branchCode = $this->generateBranchCode();
        return view('branches.create', compact('branchCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_name' => 'required|string|max:150',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'contact_person' => 'nullable|string|max:150',
            'mobile_no' => 'nullable|string|max:30',
            'phone_no' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'is_active' => 'boolean',
        ]);

        $validated['branch_code'] = $this->generateBranchCode();
        $validated['created_date'] = now();

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'Branch created successfully!');
    }

    public function edit($uid)
    {
        $branch = Branch::findOrFail($uid);
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, $uid)
    {
        $branch = Branch::findOrFail($uid);

        $validated = $request->validate([
            'branch_code' => 'required|string|max:50|unique:branches,branch_code,' . $uid . ',uid',
            'branch_name' => 'required|string|max:150',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'contact_person' => 'nullable|string|max:150',
            'mobile_no' => 'nullable|string|max:30',
            'phone_no' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'is_active' => 'boolean',
        ]);

        $validated['updated_date'] = now();
        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully!');
    }

    public function destroy($uid)
    {
        $branch = Branch::findOrFail($uid);
        $branch->update(['is_active' => false]);

        return redirect()->route('branches.index')->with('success', 'Branch deactivated successfully!');
    }

    private function generateBranchCode()
    {
        $lastBranch = Branch::where('branch_code', 'like', 'BR%')
            ->orderBy('branch_code', 'desc')
            ->first();

        if ($lastBranch) {
            $lastNumber = (int)str_replace('BR', '', $lastBranch->branch_code);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'BR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
