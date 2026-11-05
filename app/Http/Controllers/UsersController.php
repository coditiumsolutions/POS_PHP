<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role_name', $request->role);
        }

        $users = $query->orderBy('full_name')->paginate(50);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();
        $roles = ['Administrator', 'Manager', 'Cashier', 'Viewer'];

        return view('users.create', compact('branches', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:100|unique:users',
            'full_name' => 'required|string|max:200',
            'password' => 'required|string|min:6',
            'mobile_no' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'role_name' => 'required|in:Administrator,Manager,Cashier,Viewer',
            'user_type' => 'nullable|string|max:50',
            'terminal_name' => 'nullable|string|max:100',
            'branch' => 'nullable|string|max:150',
            'is_active' => 'boolean',
        ]);

        $validated['password_hash'] = Hash::make($validated['password']);
        unset($validated['password']);
        $validated['created_date'] = now();

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    public function edit($uid)
    {
        $user = User::findOrFail($uid);
        $branches = Branch::where('is_active', true)->orderBy('branch_name')->get();
        $roles = ['Administrator', 'Manager', 'Cashier', 'Viewer'];

        return view('users.edit', compact('user', 'branches', 'roles'));
    }

    public function update(Request $request, $uid)
    {
        $user = User::findOrFail($uid);

        $validated = $request->validate([
            'user_name' => 'required|string|max:100|unique:users,user_name,' . $uid . ',uid',
            'full_name' => 'required|string|max:200',
            'password' => 'nullable|string|min:6',
            'mobile_no' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'role_name' => 'required|in:Administrator,Manager,Cashier,Viewer',
            'user_type' => 'nullable|string|max:50',
            'terminal_name' => 'nullable|string|max:100',
            'branch' => 'nullable|string|max:150',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password_hash'] = Hash::make($validated['password']);
        }
        unset($validated['password']);
        $validated['updated_date'] = now();

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    public function destroy($uid)
    {
        $user = User::findOrFail($uid);
        $user->update(['is_active' => false]);

        return redirect()->route('users.index')->with('success', 'User deactivated successfully!');
    }
}
