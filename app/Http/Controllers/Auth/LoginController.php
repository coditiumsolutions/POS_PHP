<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('pos.cash');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('user_name', $request->user_name)
            ->where('is_active', true)
            ->first();

        if ($user && Hash::check($request->password, $user->password_hash)) {
            Auth::login($user);
            
            $user->last_login_date = now();
            $user->save();

            return redirect()->intended(route('pos.cash'));
        }

        return back()->withErrors([
            'user_name' => 'Invalid credentials or user is inactive.',
        ])->withInput($request->only('user_name'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
