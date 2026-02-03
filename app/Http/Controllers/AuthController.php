<?php

namespace App\Http\Controllers;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
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
        $countryCode = '';
        $nationalNumber = '';
        $countryIso = '';
        return view('auth.register', compact(
            'countryCode',
            'nationalNumber',
            'countryIso'
        ));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name'     => 'required|string|max:100',
            'last_name'      => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'country_codes'  => 'required|string|min:2|max:6',
            'phone'          => 'required|string|min:6|max:20',
            'password'       => 'required|min:8|confirmed',
            'terms'          => 'accepted',
        ], [
            'terms.accepted' => 'You must agree to the Terms & Conditions before continuing.',
        ]);

        $token = Str::random(64);

        // ================= PHONE VALIDATION =================
        $phoneUtil   = PhoneNumberUtil::getInstance();
        $rawPhone    = trim($validated['phone']);
        $countryCode = trim($validated['country_codes']);
        $cleanPhone  = preg_replace('/\D+/', '', $rawPhone);

        try {
            if (str_starts_with($rawPhone, '+')) {
                $number = $phoneUtil->parse($rawPhone, null);
            } else {
                $number = $phoneUtil->parse($countryCode . $cleanPhone, null);
            }

            if (! $phoneUtil->isValidNumber($number)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'phone' => 'Invalid phone number for selected country.',
                    ]);
            }

            // Store phone in E.164 format
            $validated['phone'] = $phoneUtil->format(
                $number,
                PhoneNumberFormat::E164
            );
        } catch (NumberParseException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'phone' => 'Invalid phone number format.',
                ]);
        }

        $user = User::create([
            'first_name'          => $validated['first_name'],
            'last_name'           => $validated['last_name'],
            'name'                => $validated['first_name'] . ' ' . $validated['last_name'],
            'email'               => $validated['email'],
            'phone'               => $validated['phone'],
            'password'            => Hash::make($validated['password']),
            'verification_token'  => $token,
            'role'                => 'user',
        ]);

        Mail::send('emails.verify', ['token' => $token], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Verify your email address');
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
