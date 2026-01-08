<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('superadmin.auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)
            ->where('role', 'superadmin')
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'This email is not registered as a Super Admin'])->with('error', 'This email is not registered as a Super Admin');
        }

        $status = Password::sendResetLink($request->only('email'));
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Reset password link has been sent successfully! Please check your inbox.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}
