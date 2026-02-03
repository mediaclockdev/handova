<?php

namespace App\Http\Controllers\SuperAdmin;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;

class BuildersController extends Controller
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
        $baseQuery = User::where('role', 'user');

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

        return view('superadmin.builders.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'blockedUsers',
            'pendingUsers'
        ));
    }

    public function export(Request $request)
    {
        $fileName = 'builders_' . now()->format('Y_m_d_His') . '.csv';

        $builders = User::query()
            ->where('role', 'user')

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
            'Name',
            'Email',
            'Phone',
            'Status',
            'Specialty',
            'Joined At'
        ];

        return new StreamedResponse(function () use ($builders, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($builders as $builder) {
                fputcsv($handle, [
                    $builder->id,
                    $builder->name,
                    $builder->email,
                    $builder->phone,
                    ucfirst($builder->status),
                    ucfirst($builder->specialty),
                    $builder->created_at->format('Y-m-d'),
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
        return view('superadmin.builders.create', compact('countryCode', 'nationalNumber', 'countryIso'));
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name"  => "required|string|max:255",
            "email" => "required|email|unique:users,email",
            "country_codes" => "required|string|min:2|max:6",
            "phone"         => "required|string|min:6|max:20",
            "password" => "required|min:6",
            "status" => "required|in:active,pending,blocked",
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
            "name"     => $request->name,
            "email"    => $request->email,
            "phone"    => $formattedPhone,
            "role"     => "user",
            "password" => bcrypt($request->password),
            "status" => $request->status,
        ]);

        return redirect()->route("superadmin.builders.index")->with("success", "Builder created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view("superadmin.builders.show", compact("user"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $phoneUtil = PhoneNumberUtil::getInstance();
        $countryCode = '';
        $nationalNumber = '';
        $countryIso = '';

        if (!empty($user->phone)) {
            $number = $phoneUtil->parse($user->phone, 'AU');

            $countryCode = '+' . $number->getCountryCode();
            $nationalNumber = $number->getNationalNumber();
            $countryIso = strtolower(
                $phoneUtil->getRegionCodeForNumber($number)
            );
        }
        return view("superadmin.builders.edit", compact("user", "countryCode", "nationalNumber", "countryIso"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            "name"           => "required|string|max:255",
            "email"          => "required|email|unique:users,email," . $user->id,
            "phone"          => "nullable|string|max:20",
            "country_codes"  => "nullable|string|max:5",
            "status"         => "required|in:active,pending,blocked",
        ]);

        // ================= PHONE VALIDATION =================
        $phoneUtil   = PhoneNumberUtil::getInstance();
        $rawPhone    = trim($validated['phone'] ?? '');
        $countryCode = trim($validated['country_codes'] ?? '');

        try {
            if (!empty($rawPhone)) {

                if (str_starts_with($rawPhone, '+')) {
                    $number = $phoneUtil->parse($rawPhone, null);
                } else {
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
            } else {
                $formattedPhone = null;
            }
        } catch (NumberParseException $e) {
            return back()
                ->withInput()
                ->with('error', 'Invalid phone number format.');
        }

        $user->update([
            'name'   => $validated['name'],
            'email'  => $validated['email'],
            'phone'  => $formattedPhone,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route("superadmin.builders.index")
            ->with("success", "Builder updated successfully.");
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route("superadmin.builders.index")->with("success", "Builder deleted successfully.");
    }

    public function suspend($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'status' => 'suspended',
        ]);

        return redirect()
            ->route('superadmin.builders.index')
            ->with('success', 'Builder account suspended successfully.');
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
