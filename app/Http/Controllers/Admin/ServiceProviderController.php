<?php

namespace App\Http\Controllers\Admin;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ServiceProviderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formTitle = 'Service Providers';
        $serviceproviders = User::where('role', 'service_provider')->latest()->paginate(10);
        return view('admin.service_provider.index', compact('formTitle', 'serviceproviders'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formTitle = 'New Service Providers';
        $specializations = \App\Models\Specialization::where('status', 'active')
            ->orderBy('specialization')
            ->get();
        $countryCode = '';
        $nationalNumber = '';
        $countryIso = '';
        return view('admin.service_provider.create', compact(
            'formTitle',
            'specializations',
            'countryCode',
            'nationalNumber',
            'countryIso'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'company_name'           => 'nullable|string|max:255',
                'first_name'             => 'required|string|max:255',
                'last_name'              => 'required|string|max:255',
                'email'                  => 'required|email|max:255|unique:users,email',
                'country_codes'          => 'required|string|min:2|max:6',
                'phone'                  => 'required|string|min:6|max:20',
                'service_specialisation' => 'nullable|string|max:255',
                'service_type'           => 'nullable|string|max:255',
                'coverage'               => 'nullable|integer|min:1|max:1000',
                'address'                => 'required|string|max:2000',
                'latitude'               => 'nullable|numeric|between:-90,90',
                'longitude'              => 'nullable|numeric|between:-180,180',
            ],
            [
                'email.unique' => 'This email is already registered as a service provider.',
            ]
        );

        try {
            DB::beginTransaction();

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

                // overwrite phone in E.164 format
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

            // ================= USER DATA =================
            $validated['name']     = $validated['first_name'] . ' ' . $validated['last_name'];
            $validated['role']     = 'service_provider';
            $validated['password'] = bcrypt('password');

            User::create($validated);

            DB::commit();

            return redirect()
                ->route('admin.service_provider.index')
                ->with('success', 'Service provider created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Service Provider store failed', [
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong. Please try again.');
        }
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
        $serviceProvider = \App\Models\User::findOrFail($id);
        $phoneUtil = PhoneNumberUtil::getInstance();

        $countryCode = '';
        $nationalNumber = '';
        $countryIso = '';

        if (!empty($serviceProvider->phone)) {
            $number = $phoneUtil->parse($serviceProvider->phone, 'AU');

            $countryCode = '+' . $number->getCountryCode();
            $nationalNumber = $number->getNationalNumber();
            $countryIso = strtolower(
                $phoneUtil->getRegionCodeForNumber($number)
            );
        }

        $specializations = \App\Models\Specialization::where('status', 'active')
            ->orderBy('specialization')
            ->get();
        $formTitle = 'Update Service Provider';
        return view('admin.service_provider.edit', compact(
            'serviceProvider',
            'formTitle',
            'specializations',
            'countryCode',
            'nationalNumber',
            'countryIso'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $serviceProvider = User::findOrFail($id);

        // ✅ VALIDATION (outside try/catch)
        $validated = $request->validate([
            'company_name'           => 'nullable|string|max:255',
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'email'                  => 'required|email|max:255|unique:users,email,' . $serviceProvider->id,
            'country_codes'          => 'required|string|min:2|max:6',
            'phone'                  => 'required|string|min:6|max:20',
            'service_specialisation' => 'nullable|string|max:255',
            'service_type'           => 'nullable|string|max:255',
            'coverage'               => 'nullable|integer|min:1|max:100000',
            'address'                => 'required|string|max:2000',
            'latitude'               => 'nullable|numeric|between:-90,90',
            'longitude'              => 'nullable|numeric|between:-180,180',
        ]);

        try {
            // ================= PHONE VALIDATION =================
            $phoneUtil   = PhoneNumberUtil::getInstance();
            $rawPhone    = trim($validated['phone']);
            $countryCode = trim($validated['country_codes']);
            $cleanPhone  = preg_replace('/\D+/', '', $rawPhone);

            if (str_starts_with($rawPhone, '+')) {
                $number = $phoneUtil->parse($rawPhone, null);
            } else {
                $number = $phoneUtil->parse($countryCode . $cleanPhone, null);
            }

            if (! $phoneUtil->isValidNumber($number)) {
                return back()
                    ->withInput()
                    ->withErrors(['phone' => 'Invalid phone number for selected country.']);
            }

            $validated['phone'] = $phoneUtil->format($number, PhoneNumberFormat::E164);

            // ================= USER DATA =================
            $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

            $serviceProvider->update($validated);

            return redirect()
                ->route('admin.service_provider.index')
                ->with('success', 'Service provider updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Service Provider update failed', [
                'error' => $e->getMessage(),
                'id'    => $id,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the service provider.');
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $serviceProvider = User::findOrFail($id);
        $serviceProvider->delete();
        return redirect()->route('admin.service_provider.index')->with('success', 'Service Provider deleted successfully.');
    }
}
