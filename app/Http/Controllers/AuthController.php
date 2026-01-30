<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone' => 'required|numeric|digits_between:8,15',
            'password'   => 'required|min:8|confirmed',
            'terms'      => 'accepted',
        ], [
            'terms.accepted' => 'You must agree to the Terms & Conditions before continuing.',
        ]);


        $token = Str::random(64);

        $user = User::create([
            'first_name'    =>  $request->first_name,
            'last_name'     =>  $request->first_name,
            'name'          => $request->first_name . ' ' . $request->last_name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'password'      => Hash::make($request->password),
            'verification_token' => $token,
            'role'  => 'user'
        ]);

        Mail::send('emails.verify', ['token' => $token], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Verify your email address');
        });

        return redirect()->route('login')
            ->with('success', 'Account created successfully. Please check your email to verify.');
    }

    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid or expired verification link.');
        }

        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        return redirect()->route('login')->with('success', 'Email verified successfully. You can now login.');
    }


    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->with('error', 'Invalid email or password.');
        }

        if ($user->role !== 'user') {
            return back()
                ->withErrors(['email' => 'You are not allowed to login from here.'])
                ->with('error', 'You are not allowed to login from here.');
        }

        if (! $user->hasVerifiedEmail()) {
            return back()
                ->withErrors(['email' => 'Please verify your email before logging in.'])
                ->with('error', 'Please verify your email before logging in.');
        }

        \Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Welcome back!');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
