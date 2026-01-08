<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('superadmin.login'); // create blade
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt(array_merge($credentials, ['role' => 'superadmin']))) {
            return redirect()->route('superadmin.dashboard')->with('success', 'Login successful.');
        }

        return back()->withErrors(['email' => 'Invalid credentials or not authorized'])->with('error', 'Invalid credentials or not authorized');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('superadmin.login');
    }
}
