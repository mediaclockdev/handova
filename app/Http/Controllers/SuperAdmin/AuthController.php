<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'superadmin') {
            return redirect()->route('superadmin.dashboard');
        }

        return view('superadmin.login');
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt(
            array_merge($credentials, ['role' => 'superadmin']),
            $remember
        )) {
            return redirect()
                ->route('superadmin.dashboard')
                ->with('success', 'Login successful.');
        }

        return back()
            ->withErrors(['email' => 'Invalid credentials or not authorized'])
            ->with('error', 'Invalid credentials or not authorized');
    }


    public function logout()
    {
        Auth::logout();
        return redirect()->route('superadmin.login');
    }
}
