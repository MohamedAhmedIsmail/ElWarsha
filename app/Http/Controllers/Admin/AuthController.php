<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && $this->isAdmin(Auth::user()->role)) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('phone'))
                ->with('error', 'Invalid phone or password.');
        }

        $request->session()->regenerate();

        if (! $this->isAdmin($request->user()->role)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(response()->view('admin.errors.403', [], 403));
        }

        return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome back.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out.');
    }

    private function isAdmin(UserRole|string|null $role): bool
    {
        $roleValue = $role instanceof UserRole ? $role->value : $role;

        return in_array($roleValue, [UserRole::Admin->value, UserRole::SuperAdmin->value], true);
    }
}
