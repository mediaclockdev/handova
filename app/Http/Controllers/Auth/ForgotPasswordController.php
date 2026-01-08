<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use App\Mail\PasswordResetCodeMail;


class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.forgotpassword'); // keep your existing form
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We can\'t find a user with that email.']);
        }

        $code = rand(100000, 999999);

        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $request->email],
            ['code' => $code, 'created_at' => now()]
        );

        // Mail::raw("Your verification code is: $code", function ($message) use ($request) {
        //     $message->to($request->email)
        //         ->subject('Password Reset Verification Code');
        // });

        Mail::to($request->email)->send(new PasswordResetCodeMail($user->name, $request->email, $code));

        session(['password_reset_verified_email' => $request->email]);

        return redirect()
            ->route('password.verify-code-form')
            ->with('email', $request->email)
            ->with('success', 'A verification code has been sent to your email address.');
    }

    public function showVerifyCodeForm(Request $request)
    {
        return view('auth.passwords.verify-code')->with('email', session('email'));
    }

    public function verifyCode(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'code' => 'required|array|size:6',
            'code.*' => 'required|string|size:1'
        ]);

        $inputCode = implode('', $request->input('code'));

        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $inputCode)
            ->first();

        if (!$record) {
            return back()->withErrors(['code' => 'Invalid verification code.'])->with('error', 'Invalid verification code.');
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return back()->withErrors(['code' => 'Verification code expired.']->with('error', 'Verification code expired.'));
        }

        DB::table('password_reset_codes')->where('email', $request->email)->delete();

        session(['password_reset_verified_email' => $request->email]);

        return redirect()->route('password.reset')->with('success', 'A verification code is successfully verified.');;
    }
}
