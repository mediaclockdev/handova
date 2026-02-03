<?php

namespace App\Http\Controllers\Admin;

use \Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\ServiceProvider;
use App\Models\IssueReport;
use \App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class ProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth'); // This will redirect to login if not authenticated
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countryCode     = '';
        $nationalNumber  = '';
        $countryIso      = '';
        $formTitle       = 'User Profile';
        $user            = Auth::user();

        $phoneUtil = PhoneNumberUtil::getInstance();

        if (!empty($user->phone)) {
            try {
                $number = $phoneUtil->parse($user->phone, null);

                $countryCode    = '+' . $number->getCountryCode();
                $nationalNumber = $number->getNationalNumber();
                $countryIso     = strtolower(
                    $phoneUtil->getRegionCodeForNumber($number)
                );
            } catch (NumberParseException $e) {
                // Optional: log invalid stored phone numbers
                Log::warning('Invalid stored phone number', [
                    'user_id' => $user->id,
                    'phone'   => $user->phone,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        return view(
            'admin.profile.index',
            compact('formTitle', 'user', 'countryCode', 'nationalNumber', 'countryIso')
        );
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        // ================= VALIDATION (outside try) =================
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'country_code1'   => 'required|string|min:2|max:6',
            'phone_number1'   => 'required|string|min:6|max:20',
            'profile_picture' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
            'existing_profile_picture' => 'nullable|string',
            'remove_profile_picture'   => 'nullable|boolean',
        ]);

        try {
            // ================= PHONE VALIDATION =================
            $phoneUtil   = PhoneNumberUtil::getInstance();
            $rawPhone    = trim($validated['phone_number1']);
            $countryCode = trim($validated['country_code1']);
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
                            'phone_number1' => 'Invalid phone number for selected country.',
                        ]);
                }

                $formattedPhone = $phoneUtil->format(
                    $number,
                    PhoneNumberFormat::E164
                );
            } catch (NumberParseException $e) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'phone_number1' => 'Invalid phone number format.',
                    ]);
            }

            // ================= USER UPDATE =================
            $user->name       = $validated['first_name'] . ' ' . $validated['last_name'];
            $user->first_name = $validated['first_name'];
            $user->last_name  = $validated['last_name'];
            $user->email      = $validated['email'];
            $user->phone      = $formattedPhone;

            $profilePicture = $validated['existing_profile_picture'] ?? $user->profile_picture;

            // Remove profile picture
            if ($request->boolean('remove_profile_picture') && $profilePicture) {
                Storage::disk('public')->delete(str_replace('storage/', '', $profilePicture));
                $profilePicture = null;
            }

            // Upload new profile picture
            if ($request->hasFile('profile_picture')) {
                if ($profilePicture) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $profilePicture));
                }

                $path = $request->file('profile_picture')->store('profile', 'public');
                $profilePicture = 'storage/' . $path;
            }

            $user->profile_picture = $profilePicture;
            $user->save();

            return back()->with('success', 'Profile updated successfully!');
        } catch (\Throwable $e) {

            Log::error('Profile update failed', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
