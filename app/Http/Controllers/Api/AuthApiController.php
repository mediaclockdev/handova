<?php

namespace App\Http\Controllers\Api;

use App\Services\FcmService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use App\Mail\PasswordResetCodeMail;
use Carbon\Carbon;

class AuthApiController extends Controller
{

    /* Register API */
    public function register(Request $request)
    {
        // Custom validation messages
        $messages = [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Invalid email format.',
            'phone.required' => 'Phone number is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password_confirmation.required' => 'Confirm password is required.'
        ];

        // Validate request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email',
            'phone'      => 'required|string|max:20',
            'password'   => 'required|min:8',
            'password_confirmation' => 'required',
            'service_specialisation' => 'nullable|exists:specializations,id',
        ], $messages);

        if ($validator->fails()) {
            // Flatten all validation error messages into a single array
            $messages = collect($validator->errors()->all());

            return response()->json([
                'status' => false,
                'data' => [
                    'messages' => $messages
                ],
            ], 420);
        }

        // Check if password confirmation matches
        if ($request->password !== $request->password_confirmation) {
            return response()->json([
                'status' => false,
                'data' => [
                    'messages' => ['Password and confirm password do not match.']
                ],
            ], 420);
        }

        // Check if email already exists
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            if ($existingUser->email_verified_at) {
                return response()->json([
                    'status' => false,
                    'data' => [
                        'messages' => ['Email is already registered. Please login instead.']
                    ],
                ], 420);
            } else {
                $existingUser->verification_token = Str::random(64);
                $existingUser->save();

                Mail::send('emails.verify', ['token' => $existingUser->verification_token], function ($message) use ($existingUser) {
                    $message->to($existingUser->email);
                    $message->subject('Verify your email address');
                });

                return response()->json([
                    'status' => false,
                    'data' => [
                        'messages' => ['This email is already registered but not verified. We have resent the verification link.']
                    ],
                ], 420);
            }
        }

        // Create new user and send verification email
        $token = Str::random(64);

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'verification_token' => $token,
            'role' => $request->role ?? 'house_owner',
            'service_specialisation' => $request->role === 'service_provider' ? $request->service_specialisation : null,
        ]);

        Mail::send('emails.verify', ['token' => $token], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Verify your email address');
        });

        return response()->json([
            'status'  => true,
            'message' => 'Account created successfully. Please check your email for verification link.',
        ], 201);
    }

    /* Verify Email */
    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or expired verification link.',
            ], 422);
        }

        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Email verified successfully. You can now login.',
        ]);
    }

    /* Login API */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'fcm_token'  => 'nullable|string',
            'device_id'  => 'nullable|string',
            'device_type'  => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Check your login credential and try again',
            ], 422);
        }

        //  Check if email is verified
        if (! $user->hasVerifiedEmail()) {
            return response()->json([
                'status' => false,
                'message' => 'Please verify your email before logging in.',
                'resend_verification_url' => url('/api/resend-verification?email=' . $user->email),
            ], 422);
        }

        // Update FCM token and device ID
        $user->update([
            'fcm_token' => $request->fcm_token,
            'device_id' => $request->device_id,
            'device_type' => $request->device_type,
        ]);

        // Create API token
        $user->tokens()->delete();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => $user,
        ], 200);
    }

    /* Dashboard API */
    public function dashboard(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => $request->user(),
        ]);
    }

    /* Logout API */
    public function logout(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();
        $user->update([
            'fcm_token'     => null,
            'device_type'   => null,
            'token'         => null,
            'device_id'     => null,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /* User Profile API */
    public function getUserProfile(Request $request)
    {
        try {
            // Step 0: Check for Bearer Token
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['status' => false, 'message' => 'Token is required.',], 401);
            }

            // Step 1: Check if token is valid (user authenticated)
            $authUser = $request->user();

            if (!$authUser) {
                return response()->json(['status' => false, 'message' => 'Invalid or expired token.',], 401);
            }

            // Step 2: Fetch user directly from authenticated token
            $user = User::find($authUser->id);

            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found.',], 404);
            }

            // Step 3: Return user profile
            return response()->json([
                'status' => true,
                'response_code' => 200,
                'message' => 'User profile fetched successfully.',
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Something went wrong.', 'error' => $e->getMessage(),], 500);
        }
    }

    /* Forgot Password API */
    public function forgotPassword(Request $request)
    {
        // Validate email
        $request->validate(['email' => 'required|email',]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'response_code' => 422,
                'status' => false,
                'message' => "We can't find a user with that email."
            ], 200);
        }

        // Generate 6-digit verification code
        $code = rand(100000, 999999);

        // Store code in DB
        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $request->email],
            ['code' => $code, 'created_at' => now()]
        );

        // Send email with the code
        Mail::to($request->email)->send(new PasswordResetCodeMail($user->name, $request->email, $code));

        return response()->json([
            'response_code' => 200,
            'status' => true,
            'message' => 'A verification code has been sent to your email address.',
            'email' => $request->email,
        ], 200);
    }

    /* Verify Code API */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|size:6',
            //'code.*' => 'required|string|size:1',
        ]);

        $inputCode =  $request->input('code');

        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $inputCode)
            ->first();

        if (!$record) {
            return response()->json([
                'response_code' => 422,
                'status' => false,
                'message' => 'Invalid verification code.'
            ], 400);
        }

        if (Carbon::parse($record->created_at)->addMinutes(15)->isPast()) {
            return response()->json([
                'response_code' => 422,
                'status' => false,
                'message' => 'Verification code expired.'
            ], 400);
        }

        // Delete used code
        DB::table('password_reset_codes')->where('email', $request->email)->delete();

        return response()->json([
            'response_code' => 200,
            'status' => true,
            'message' => 'Verification code successfully verified.'
        ], 200);
    }

    /* Reset Password API*/
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'response_code' => 422,
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json([
            'response_code' => 200,
            'status' => true,
            'message' => 'Password successfully updated.'
        ], 200);
    }
}
