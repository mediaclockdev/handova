<?php

namespace App\Http\Controllers\SuperAdmin;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ServiceProvider;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServiceProvidersListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Base query
        $baseQuery = User::where('role', 'service_provider');

        // Counts
        $totalUsers  = (clone $baseQuery)->count();
        $activeUsers = (clone $baseQuery)->where('status', 'active')->count();
        $pendingUsers = (clone $baseQuery)->where('status', 'pending')->count();
        $blockedUsers = (clone $baseQuery)->where('status', 'suspended')->count();

        // Filtered listing
        $users = $baseQuery
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->specialty, function ($q) use ($request) {
                $q->where('specialty', $request->specialty);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('superadmin.providers.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'blockedUsers',
            'pendingUsers'
        ));
    }

    public function export(Request $request)
    {
        $fileName = 'service_providers_' . now()->format('Y_m_d_His') . '.csv';

        $builders = User::query()
            ->where('role', 'service_provider')

            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%');
                });
            })

            ->when(
                $request->status,
                fn($q) =>
                $q->where('status', $request->status)
            )

            ->when(
                $request->specialty,
                fn($q) =>
                $q->where('specialty', $request->specialty)
            )

            ->orderByDesc('id')
            ->get();

        $headers = [
            "Content-Type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $columns = [
            'ID',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Status',
            'Service Specialisation',
            'Service Type',
            'Joined At'
        ];

        return new StreamedResponse(function () use ($builders, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($builders as $builder) {
                fputcsv($handle, [
                    $builder->id,
                    $builder->first_name,
                    $builder->last_name,
                    $builder->email,
                    $builder->phone,
                    ucfirst($builder->status),
                    ucfirst($builder->service_specialisation),
                    ucfirst($builder->service_type),
                    $builder->created_at,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $countryCode = '';
        $nationalNumber = '';
        $countryIso = '';
        return view('superadmin.providers.create', compact('countryCode', 'nationalNumber', 'countryIso'));
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "company_name"  => "required|string",
            "name"          => "required|string|max:255",
            "email"         => "required|email|unique:users,email",
            "country_codes" => "required|string|min:2|max:6",
            "phone"         => "required|string|min:6|max:20",
            "password"      => "required|min:6",
            "status"        => "required|in:active,pending,blocked",
            "service_specialisation" => "nullable|string",
            "service_type"           => "nullable|string",
        ]);

        // ================= PHONE VALIDATION =================
        $phoneUtil   = PhoneNumberUtil::getInstance();
        $rawPhone    = trim($validated['phone']);
        $countryCode = trim($validated['country_codes']);

        try {
            if (str_starts_with($rawPhone, '+')) {
                $number = $phoneUtil->parse($rawPhone, null);
            } else {
                $cleanPhone = preg_replace('/\D+/', '', $rawPhone);
                $number = $phoneUtil->parse($countryCode . $cleanPhone, null);
            }

            if (!$phoneUtil->isValidNumber($number)) {
                return back()
                    ->withInput()
                    ->with('error', 'Invalid phone number for selected country.');
            }

            $formattedPhone = $phoneUtil->format(
                $number,
                PhoneNumberFormat::E164
            );
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Invalid phone number format.');
        }

        User::create([
            "company_name" => $validated['company_name'],
            "name"         => $validated['name'],
            "email"        => $validated['email'],
            "phone"        => $formattedPhone,
            "role"         => "service_provider",
            "password"     => bcrypt($validated['password']),
            "status"       => $validated['status'],
            "service_specialisation" => $validated['service_specialisation'] ?? null,
            "service_type"           => $validated['service_type'] ?? null,
        ]);

        return redirect()
            ->route("superadmin.providers.index")
            ->with("success", "Builder created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view("superadmin.providers.show", compact("user"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $countryCode = '';
        $nationalNumber = '';
        $countryIso = '';
        $phoneUtil = PhoneNumberUtil::getInstance();
        if (!empty($user->phone)) {
            $number = $phoneUtil->parse($user->phone, 'AU');

            $countryCode = '+' . $number->getCountryCode();
            $nationalNumber = $number->getNationalNumber();
            $countryIso = strtolower(
                $phoneUtil->getRegionCodeForNumber($number)
            );
        }
        return view("superadmin.providers.edit", compact("user", "countryCode", "nationalNumber", "countryIso"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // ✅ STORE validated data
        $validated = $request->validate([
            "company_name"            => "required|string",
            "name"                    => "required|string|max:255",
            "email"                   => "required|email|unique:users,email," . $user->id,
            "country_codes"           => "required|string|min:2|max:6",
            "phone"                   => "required|string|min:6|max:20",
            "status"                  => "required|in:active,pending,blocked",
            "service_specialisation"  => "nullable|string",
            "service_type"            => "nullable|string",
        ]);

        // ================= PHONE VALIDATION =================
        $phoneUtil   = PhoneNumberUtil::getInstance();
        $rawPhone    = trim($validated['phone']);
        $countryCode = trim($validated['country_codes']);

        try {
            if (str_starts_with($rawPhone, '+')) {
                $number = $phoneUtil->parse($rawPhone, null);
            } else {
                // country code like +91
                $number = $phoneUtil->parse($countryCode . $rawPhone, null);
            }

            if (!$phoneUtil->isValidNumber($number)) {
                return back()
                    ->withInput()
                    ->with('error', 'Invalid phone number for selected country.');
            }

            $formattedPhone = $phoneUtil->format(
                $number,
                PhoneNumberFormat::E164
            );
        } catch (NumberParseException $e) {
            return back()
                ->withInput()
                ->with('error', 'Invalid phone number format.');
        }

        // ================= UPDATE USER =================
        $user->update([
            "company_name"           => $validated['company_name'],
            "name"                   => $validated['name'],
            "email"                  => $validated['email'],
            "phone"                  => $formattedPhone,
            "status"                 => $validated['status'],
            "service_specialisation" => $validated['service_specialisation'],
            "service_type"           => $validated['service_type'],
        ]);

        return redirect()
            ->route("superadmin.providers.index")
            ->with("success", "Builder updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route("superadmin.providers.index")->with("success", "Builder deleted successfully.");
    }

    public function suspend($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'status' => 'suspended',
        ]);

        return redirect()
            ->route('superadmin.providers.index')
            ->with('success', 'Service Provider account suspended successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:active,suspended',
            'user_ids' => 'required'
        ]);

        $ids = explode(',', $request->user_ids);

        User::whereIn('id', $ids)->update([
            'status' => $request->action
        ]);

        return redirect()
            ->back()
            ->with('success', ucfirst($request->action) . ' users successfully.');
    }

    public function sendMail(Request $request)
    {
        $request->validate([
            'emails' => 'required',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        $emails = explode(',', $request->emails);

        foreach ($emails as $email) {
            Mail::raw($request->message, function ($mail) use ($email, $request) {
                $mail->to($email)
                    ->subject($request->subject);
            });
        }

        return back()->with('success', 'Email sent successfully.');
    }
}
