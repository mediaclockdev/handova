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

class IssueReportController extends Controller
{
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
        $properties = Property::select('id', 'property_title')
            ->where('user_id', Auth::id())
            ->get();

        $serviceProviders = ServiceProvider::orderBy('company_name', 'asc')->get();
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
            'houseOwners'
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
            'customer_contact'    => 'required|string|max:20',
            'issue_details'       => 'required|string',
            'reported_by'         => 'required|exists:users,id',
            'reported_date'       => 'nullable|date',
            'assigned_to_service_provider' => 'nullable|in:yes,no',
            'service_provider'    => 'nullable|string|max:255',
            'issue_status'        => 'nullable|string|max:255',
            'issue_urgency_level' => 'nullable|string|max:255',

            'image'               => 'nullable|array',
            'image.*'             => 'file|mimes:pdf,csv,xlsx,xls,jpg,jpeg,png|max:5120',
        ]);

        // Remove file field
        $data = $request->except(['image']);

        // Store images
        if ($request->hasFile('image')) {
            $images = [];

            foreach ($request->file('image') as $file) {
                $images[] = $file->store('issue_report', 'public');
            }

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
        $properties = Property::select('id', 'property_title')->get();
        $serviceProviders = ServiceProvider::select('id', 'company_name')->get();
        $formTitle = 'Update Report Issues';
        $houseOwners = User::where('role', 'house_owner')->get(['id', 'email']);

        return view('admin.issue_report.edit', compact(
            'issueReport',
            'properties',
            'serviceProviders',
            'formTitle',
            'houseOwners'
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
            'customer_contact'    => 'required|string|max:20',
            'issue_details'       => 'required|string',
            'reported_by'         => 'required|exists:users,id',
            'reported_date'       => 'required|date',
            'assigned_to_service_provider' => 'nullable|in:yes,no',
            'service_provider'    => 'nullable|string|max:255',
            'issue_status'        => 'required|string|max:255',
            'issue_urgency_level' => 'required|string|max:255',

            'image'               => 'nullable|array',
            'image.*'             => 'file|mimes:pdf,csv,xlsx,xls,jpg,jpeg,png|max:5120',

            'existing_images'     => 'nullable|array',
            'existing_images.*'   => 'string',

            'remove_images'       => 'nullable|array',
            'remove_images.*'     => 'string',
        ]);

        // Remove file-related fields
        $data = $request->except(['image', 'existing_images', 'remove_images']);

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
}
