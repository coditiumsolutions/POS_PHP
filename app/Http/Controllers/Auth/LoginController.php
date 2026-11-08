<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AspNetIdentityPasswordHasher;
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

        if ($user && $this->passwordMatches($request->password, $user->password_hash)) {
            // Upgrade ASP.NET / legacy SHA-256 hashes to bcrypt after a successful login.
            if (AspNetIdentityPasswordHasher::isAspNetHash($user->password_hash)
                || AspNetIdentityPasswordHasher::isLegacySha256Hash($user->password_hash)) {
                $user->password_hash = Hash::make($request->password);
            }

            Auth::login($user);

            $user->last_login_date = now();
            $user->save();

            return redirect()->intended(route('pos.cash'));
        }

        return back()->withErrors([
            'user_name' => 'Invalid credentials or user is inactive.',
        ])->withInput($request->only('user_name'));
    }

    private function passwordMatches(string $plain, ?string $hashed): bool
    {
        if ($hashed === null || $hashed === '') {
            return false;
        }

        if (AspNetIdentityPasswordHasher::isAspNetHash($hashed)) {
            return AspNetIdentityPasswordHasher::verify($plain, $hashed);
        }

        // bcrypt / argon (Laravel Hash::make)
        if (str_starts_with($hashed, '$2y$')
            || str_starts_with($hashed, '$2a$')
            || str_starts_with($hashed, '$2b$')
            || str_starts_with($hashed, '$argon')) {
            return Hash::check($plain, $hashed);
        }

        // Older POS_MT passwords: uppercase SHA-256 hex
        if (AspNetIdentityPasswordHasher::isLegacySha256Hash($hashed)) {
            return AspNetIdentityPasswordHasher::verifyLegacySha256($plain, $hashed);
        }

        return false;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
