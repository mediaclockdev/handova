<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IssueReport;
use App\Models\Property;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use Illuminate\Support\Facades\DB;

class IssueReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $issueReports = IssueReport::with(['property', 'reporter'])
            ->whereHas('property', function ($q) {
                $q->where('user_id', Auth::id());   // Only properties owned by this user
            })
            ->latest()
            ->paginate(10);

        $properties = Property::select('id', 'property_title')
            ->where('user_id', Auth::id())
            ->get();

        return view('admin.issue_report.index', compact('issueReports', 'properties'));
    }

    public function filter(Request $request)
    {
        $query = IssueReport::with(['property', 'reporter'])
            ->whereHas('property', function ($q) {
                $q->where('user_id', Auth::id());
            });

        if ($request->property_id) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->status) {
            $query->where('issue_status', $request->status);
        }

        return response()->json(
            $query->latest()->get()
        );
    }

    // IssueReportController.php
    public function getIssuesByProperty($propertyId)
    {
        $issues = IssueReport::with('reporter')
            ->where('properties_id', $propertyId)
            ->get()
            ->map(function ($issue) {
                return [
                    'id' => $issue->id,
                    'issue_number' => $issue->issue_number,
                    'issue_details' => $issue->issue_details,
                    'reporter_name' => $issue->reporter->name,
                    'reported_date' => $issue->reported_date,
                    'issue_status' => $issue->issue_status,
                    'assigned_to_service_provider' => $issue->assigned_to_service_provider, // added
                ];
            });

        return response()->json($issues);
    }

    public function create()
    {
        $countryCode = '';
        $nationalNumber = '';
        $countryIso = '';
        $properties = Property::select('id', 'property_title')->where('user_id', Auth::id())->get();
        $serviceProviders = User::select('id', 'company_name')->where('role', 'service_provider')->get();
        $formTitle = 'Report Issues';
        $issueReport = new IssueReport();
        $lastIssueNumber = IssueReport::select('issue_number')
            ->where('issue_number', 'LIKE', 'Issue-%')
            ->orderByRaw('CAST(SUBSTRING(issue_number, 7) AS UNSIGNED) DESC')
            ->first();

        if ($lastIssueNumber && preg_match('/Issue-(\d+)/', $lastIssueNumber->issue_number, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        } else {
            $nextNumber = 1;
        }

        $newIssueNumber = 'Issue-' . $nextNumber;
        $houseOwners = User::where('role', 'house_owner')->get(['id', 'email']);

        return view('admin.issue_report.create', compact(
            'properties',
            'formTitle',
            'serviceProviders',
            'issueReport',
            'newIssueNumber',
            'houseOwners',
            'countryCode',
            'nationalNumber',
            'countryIso'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'issue_number'        => 'nullable|string|max:255',
            'properties_id'       => 'required|exists:properties,id',
            'issue_title'         => 'required|string|max:255',
            'issue_category'      => 'nullable|string|max:255',
            'issue_location'      => 'required|string|max:255',

            'report_country_code' => 'required|string|min:2|max:6',
            'customer_contact'          => 'required|string|min:6|max:20',

            'issue_details'       => 'required|string',
            'reported_by'         => 'required|exists:users,id',
            'reported_date'       => 'nullable|date',
            'assigned_to_service_provider' => 'nullable|in:yes,no',
            'service_provider'    => 'nullable|string|max:255',
            'issue_status'        => 'nullable|string|max:255',
            'issue_urgency_level' => 'nullable|string|max:255',

            // Multiple file upload validation
            'image'               => 'nullable|array',
            'image.*'             => 'file|mimes:jpg,jpeg,png,pdf,csv,xlsx,xls|max:5120',
            'status' => 'required|in:pending,accepted,declined',
        ]);

        // ================= PHONE VALIDATION =================
        $phoneUtil   = PhoneNumberUtil::getInstance();
        $rawPhone    = trim($request->customer_contact);
        $countryCode = trim($request->report_country_code);

        try {
            if (str_starts_with($rawPhone, '+')) {
                $number = $phoneUtil->parse(
                    str_starts_with($rawPhone, '+')
                        ? $rawPhone
                        : $countryCode . preg_replace('/\D+/', '', $rawPhone),
                    null
                );
            } else {
                $cleanPhone = preg_replace('/\D+/', '', $rawPhone);
                $number = $phoneUtil->parse($countryCode . $cleanPhone, null);
            }

            if (!$phoneUtil->isValidNumber($number)) {
                return back()
                    ->withInput()
                    ->with('error', 'Invalid phone number for selected country.');
            }
            $data = $request->except(['image']);
            $data['customer_contact'] = $phoneUtil->format(
                $number,
                PhoneNumberFormat::E164
            );
        } catch (NumberParseException $e) {
            return back()
                ->withInput()
                ->with('error', 'Invalid phone number format.');
        }

        // Remove file field from request data


        // Store uploaded files
        if ($request->hasFile('image')) {
            $images = [];

            foreach ($request->file('image') as $file) {
                $images[] = $file->store('issue_reports', 'public');
            }

            // Store file paths as JSON (same as property)
            $data['image'] = json_encode($images);
        }

        IssueReport::create($data);

        return redirect()
            ->route('admin.issue_report.index')
            ->with('success', 'Issue report created successfully.');
    }


    public function edit(string $id)
    {
        $issueReport = IssueReport::findOrFail($id);

        $phoneUtil = PhoneNumberUtil::getInstance();
        $countryCode = '';
        $nationalNumber = '';
        $countryIso = '';

        $properties = Property::select('id', 'property_title')->get();

        $property = Property::findOrFail($issueReport->properties_id);

        $lat = $property->latitude;
        $lng = $property->longitude;

        $distanceFormula = "(6371 * acos(
        cos(radians($lat))
        * cos(radians(latitude))
        * cos(radians(longitude) - radians($lng))
        + sin(radians($lat))
        * sin(radians(latitude))
    ))";

        $serviceProviders = User::select(
            'id',
            'company_name',
            'coverage',
            DB::raw("$distanceFormula AS distance")
        )
            ->where('role', 'service_provider')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotNull('coverage')
            ->whereRaw("$distanceFormula <= coverage") // ✅ FIX
            ->orderBy('distance')
            ->get();

        $formTitle = 'Update Report Issues';

        $houseOwners = User::where('role', 'house_owner')
            ->get(['id', 'email']);

        if (!empty($issueReport->customer_contact)) {
            $number = $phoneUtil->parse($issueReport->customer_contact, null);

            $countryCode = '+' . $number->getCountryCode();
            $nationalNumber = $number->getNationalNumber();
            $countryIso = strtolower(
                $phoneUtil->getRegionCodeForNumber($number)
            );
        }

        return view('admin.issue_report.edit', compact(
            'issueReport',
            'properties',
            'serviceProviders',
            'formTitle',
            'houseOwners',
            'countryCode',
            'nationalNumber',
            'countryIso'
        ));
    }


    public function update(Request $request, $id)
    {
        $issueReport = IssueReport::findOrFail($id);

        $request->validate([
            'issue_number'        => 'required|string|max:255',
            'properties_id'       => 'required|exists:properties,id',
            'issue_title'         => 'required|string|max:255',
            'issue_category'      => 'nullable|string|max:255',
            'issue_location'      => 'required|string|max:255',
            'report_country_code' => 'required|string|min:2|max:6',
            'customer_contact'          => 'required|string|min:6|max:20',
            'issue_details'       => 'required|string',
            'reported_by'         => 'required|exists:users,id',
            'reported_date'       => 'required|date',
            'assigned_to_service_provider' => 'nullable|in:yes,no',
            'service_provider'    => 'nullable|string|max:255',
            'issue_status'        => 'required|string|max:255',
            'issue_urgency_level' => 'required|string|max:255',
            'status'              => 'required|in:pending,accepted,declined',

            'image'               => 'nullable|array',
            'image.*'             => 'file|mimes:pdf,csv,xlsx,xls,jpg,jpeg,png|max:5120',

            'existing_images'     => 'nullable|array',
            'existing_images.*'   => 'string',

            'remove_images'       => 'nullable|array',
            'remove_images.*'     => 'string',
        ]);

        // Remove file-related fields
        $data = $request->except(['image', 'existing_images', 'remove_images']);

        // ================= PHONE VALIDATION =================
        $phoneUtil   = PhoneNumberUtil::getInstance();
        $rawPhone    = trim($request->customer_contact);
        $countryCode = trim($request->report_country_code);

        try {
            if (str_starts_with($rawPhone, '+')) {
                $number = $phoneUtil->parse(
                    $rawPhone,
                    null
                );
            } else {
                $cleanPhone = preg_replace('/\D+/', '', $rawPhone);
                $number = $phoneUtil->parse($countryCode . $cleanPhone, null);
            }

            if (!$phoneUtil->isValidNumber($number)) {
                return back()
                    ->withInput()
                    ->with('error', 'Invalid phone number for selected country.');
            }

            // ✅ Save in SAME column used in store()
            $data['customer_contact'] = $phoneUtil->format(
                $number,
                PhoneNumberFormat::E164
            );
        } catch (NumberParseException $e) {
            return back()
                ->withInput()
                ->with('error', 'Invalid phone number format.');
        }


        // =============================
        // 1️⃣ Start with existing images
        // =============================
        $finalImages = $request->input('existing_images', []);

        // =============================
        // 2️⃣ Delete removed images
        // =============================
        if ($request->filled('remove_images')) {
            foreach ($request->remove_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        // =============================
        // 3️⃣ Upload new images
        // =============================
        if ($request->hasFile('image')) {
            foreach ($request->file('image') as $file) {
                $finalImages[] = $file->store('issue_report', 'public');
            }
        }

        // =============================
        // 4️⃣ Save as JSON
        // =============================
        $data['image'] = json_encode($finalImages);

        $issueReport->update($data);

        return redirect()
            ->route('admin.issue_report.index')
            ->with('success', 'Issue report updated successfully.');
    }

    public function destroy(string $id)
    {
        $issueReport = IssueReport::findOrFail($id);
        $issueReport->delete();
        return redirect()->route('admin.issue_report.index')
            ->with('success', 'Issue report deleted successfully.');
    }

    public function getServiceProvidersByProperty(Request $request)
    {
        $property = Property::find($request->property_id);

        if (!$property || !$property->latitude || !$property->longitude) {
            return response()->json([]);
        }

        $lat = $property->latitude;
        $lng = $property->longitude;

        $distanceFormula = "(6371 * acos(
        cos(radians($lat))
        * cos(radians(latitude))
        * cos(radians(longitude) - radians($lng))
        + sin(radians($lat))
        * sin(radians(latitude))
    ))";

        $providers = User::select(
            'id',
            'company_name',
            'coverage',
            DB::raw("$distanceFormula AS distance")
        )
            ->where('role', 'service_provider')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereNotNull('coverage')
            ->whereRaw("$distanceFormula <= coverage") // ✅ FIX
            ->orderBy('distance')
            ->get();

        return response()->json($providers);
    }
}
