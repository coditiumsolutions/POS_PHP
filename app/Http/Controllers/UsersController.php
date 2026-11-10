<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\SalesInvoice;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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
            $query->where(function ($q) use ($search) {
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
        $validated = $request->validate(
            $this->userRules(),
            $this->userMessages()
        );

        $validated['password_hash'] = Hash::make($validated['password']);
        unset($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['created_date'] = now();

        try {
            User::create($validated);
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors($this->mapQueryException($e));
        }

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    public function show($uid)
    {
        $user = User::findOrFail($uid);
        $salesCount = SalesInvoice::where('user_uid', $uid)->count();

        return view('users.show', compact('user', 'salesCount'));
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

        $validated = $request->validate(
            $this->userRules($uid),
            $this->userMessages()
        );

        if (!empty($validated['password'])) {
            $validated['password_hash'] = Hash::make($validated['password']);
        }
        unset($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['updated_date'] = now();

        try {
            $user->update($validated);
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors($this->mapQueryException($e));
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    public function destroy($uid)
    {
        $user = User::findOrFail($uid);

        if ((int) Auth::id() === (int) $user->uid) {
            return redirect()
                ->route('users.show', $user->uid)
                ->with('error', 'You cannot delete the account you are currently logged in with.');
        }

        $hasSales = SalesInvoice::where('user_uid', $user->uid)->exists();

        if ($hasSales) {
            $user->update([
                'is_active' => false,
                'updated_date' => now(),
            ]);

            return redirect()
                ->route('users.index')
                ->with('success', 'User has sales history, so it was deactivated instead of permanently deleted.');
        }

        try {
            DB::transaction(function () use ($user) {
                DB::table('user_sessions')->where('user_uid', $user->uid)->delete();
                DB::table('audit_logs')->where('user_uid', $user->uid)->delete();
                $user->delete();
            });
        } catch (QueryException $e) {
            $user->update([
                'is_active' => false,
                'updated_date' => now(),
            ]);

            return redirect()
                ->route('users.index')
                ->with('success', 'User could not be permanently deleted because it is referenced elsewhere, so it was deactivated instead.');
        }

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    private function userRules(?int $ignoreUid = null): array
    {
        $usernameUnique = Rule::unique('users', 'user_name');
        if ($ignoreUid !== null) {
            $usernameUnique->ignore($ignoreUid, 'uid');
        }

        return [
            'user_name' => ['required', 'string', 'max:100', $usernameUnique],
            'full_name' => ['required', 'string', 'max:200'],
            'password' => [$ignoreUid ? 'nullable' : 'required', 'string', 'min:6', 'max:100'],
            'mobile_no' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'role_name' => ['required', Rule::in(['Administrator', 'Manager', 'Cashier', 'Viewer'])],
            'user_type' => ['nullable', 'string', 'max:50'],
            'terminal_name' => ['nullable', 'string', 'max:100'],
            'branch' => ['nullable', 'string', 'max:150'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    private function userMessages(): array
    {
        return [
            'user_name.required' => 'Username is required.',
            'user_name.max' => 'Username cannot be longer than 100 characters.',
            'user_name.unique' => 'This username is already taken. Choose a different username.',
            'full_name.required' => 'Full name is required.',
            'full_name.max' => 'Full name cannot be longer than 200 characters.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
            'password.max' => 'Password cannot be longer than 100 characters.',
            'email.email' => 'Enter a valid email address.',
            'email.max' => 'Email cannot be longer than 150 characters.',
            'mobile_no.max' => 'Mobile number cannot be longer than 30 characters.',
            'role_name.required' => 'Role is required.',
            'role_name.in' => 'Selected role is invalid. Choose Administrator, Manager, Cashier, or Viewer.',
            'branch.max' => 'Branch name cannot be longer than 150 characters.',
            'terminal_name.max' => 'Terminal name cannot be longer than 100 characters.',
        ];
    }

    private function mapQueryException(QueryException $e): array
    {
        $message = $e->getMessage();

        if (str_contains($message, 'users_user_name_unique') || str_contains($message, 'user_name')) {
            return ['user_name' => 'This username is already taken. Choose a different username.'];
        }

        if (str_contains($message, 'email')) {
            return ['email' => 'This email cannot be saved. Check the value and try again.'];
        }

        return ['form' => 'Could not save this user. ' . $this->friendlyDbReason($message)];
    }

    private function friendlyDbReason(string $message): string
    {
        if (preg_match("/Duplicate entry '(.+?)' for key '(.+?)'/", $message, $matches)) {
            return "Duplicate value \"{$matches[1]}\" already exists.";
        }

        if (str_contains($message, 'cannot be null') || str_contains($message, "doesn't have a default value")) {
            return 'A required field is missing.';
        }

        if (str_contains($message, 'Data too long')) {
            return 'One of the values is too long for the database field.';
        }

        return 'Please review the form and try again.';
    }
}
