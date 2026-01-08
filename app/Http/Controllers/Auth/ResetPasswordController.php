<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ResetPasswordController extends Controller
{
    public function showResetForm()
    {
        $email = session('password_reset_verified_email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Unauthorized access.']);
        }

        return view('auth.passwords.reset', ['email' => $email]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ], [
            'password.confirmed' => 'Password and Confirm Password must match.',
        ]);

        $email = session('password_reset_verified_email');

        if (!$email || $email !== $request->email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Unauthorized or expired session.'])->with('error', 'Unauthorized or expired session');;
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.'])->with('error', 'User not found.');;
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_codes')->where('email', $email)->delete();
        session()->forget('password_reset_verified_email');

        return redirect()->route('login')->with('status', 'Password successfully reset.')->with('success', 'Password successfully reset.');
    }
}
