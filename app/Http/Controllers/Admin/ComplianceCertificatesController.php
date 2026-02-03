<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplianceCertificate;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ComplianceCertificatesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $formTitle = 'Compliance Certificates';
        $certificates = ComplianceCertificate::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        // Fetch properties belonging to the user
        $properties = Property::select('id', 'property_title')
            ->where('user_id', auth()->id())
            ->get();

        return view('admin.compliance_certificates.index', compact('formTitle', 'certificates', 'properties'));
    }


    public function getCertificatesByProperty($propertyId)
    {
        $certificates = ComplianceCertificate::where('property_id', $propertyId)
            ->with('property')
            ->get()
            ->map(function ($c) {
                return [
                    'id' => $c->id,
                    'certification_title' => $c->certification_title,
                    'compliance_type' => $c->compliance_type,
                    'certificate_number' => $c->certificate_number,
                    'issuing_authority' => $c->issuing_authority,
                    'date_of_issue' => $c->date_of_issue?->format('Y-m-d'),
                    'expiry_date' => $c->expiry_date?->format('Y-m-d'),
                    'property_title' => $c->property?->property_title,
                ];
            });

        return response()->json($certificates);
    }


    public function create()
    {
        $formTitle = 'New Compliance Certificate';
        $properties = Property::select('id', 'property_title')->where('user_id', Auth::id())->get();
        return view('admin.compliance_certificates.create', compact('formTitle', 'properties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id'         => 'required|exists:properties,id',
            'certification_title' => 'required|string|max:255',
            'compliance_type'     => 'nullable|string|max:255',
            'certificate_number'  => 'nullable|string|max:255',
            'issuing_authority'   => 'nullable|string|max:255',
            'date_of_issue'       => 'nullable|date',
            'expiry_date'         => 'nullable|date|after_or_equal:date_of_issue',
            'property_area'       => 'nullable|string|max:255',

            'attachments'         => 'nullable|array',
            'attachments.*'       => 'file|mimes:pdf,csv,xlsx,xls,jpg,jpeg,png|max:5120',

            'notes'               => 'nullable|string',
        ]);

        // Remove file fields
        $data = $request->except(['attachments']);

        // Store attachments
        if ($request->hasFile('attachments')) {
            $files = [];

            foreach ($request->file('attachments') as $file) {
                $files[] = $file->store('compliance_certificates', 'public');
            }

            // Store as JSON (same as appliances_images & manuals)
            $data['attachments'] = json_encode($files);
        }

        $data['user_id'] = auth()->id();

        ComplianceCertificate::create($data);

        return redirect()
            ->route('admin.compliance_certificates.index')
            ->with('success', 'Compliance Certificate created successfully.');
    }


    public function edit($id)
    {
        $certificate = ComplianceCertificate::findOrFail($id);
        $properties = Property::select('id', 'property_title')->where('user_id', Auth::id())->get();
        $formTitle = 'Edit Compliance Certificate';
        return view('admin.compliance_certificates.edit', compact('certificate', 'formTitle', 'properties'));
    }

    public function update(Request $request, $id)
    {
        $certificate = ComplianceCertificate::findOrFail($id);

        $request->validate([
            'property_id'         => 'required|exists:properties,id',
            'certification_title' => 'required|string|max:255',
            'compliance_type'     => 'nullable|string|max:255',
            'certificate_number'  => 'nullable|string|max:255',
            'issuing_authority'   => 'nullable|string|max:255',
            'date_of_issue'       => 'nullable|date',
            'expiry_date'         => 'nullable|date|after_or_equal:date_of_issue',
            'property_area'       => 'nullable|string|max:255',

            'attachments'         => 'nullable|array',
            'attachments.*'       => 'file|mimes:pdf,csv,xlsx,xls,jpg,jpeg,png|max:5120',

            'notes'               => 'nullable|string',
        ]);

        // Remove attachments from request data
        $data = $request->except(['attachments', 'existing_attachments']);

        // Existing attachments from hidden inputs (already stored paths)
        $existingAttachments = $request->input('existing_attachments', []);

        // Upload new attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $existingAttachments[] = $file->store('compliance_certificates', 'public');
            }
        }

        // Save as JSON (same as store method)
        $data['attachments'] = json_encode($existingAttachments);

        $data['user_id'] = auth()->id();

        $certificate->update($data);

        return redirect()
            ->route('admin.compliance_certificates.index')
            ->with('success', 'Compliance Certificate updated successfully.');
    }


    public function destroy($id)
    {
        $certificate = ComplianceCertificate::findOrFail($id);

        // delete files from storage
        if (!empty($certificate->attachments) && is_array($certificate->attachments)) {
            foreach ($certificate->attachments as $file) {
                // stored path is 'uploads/compliance_certificates/xxx', Storage expects path without 'uploads/'
                $diskPath = str_replace('uploads/', '', $file);
                if (Storage::disk('public')->exists($diskPath)) {
                    Storage::disk('public')->delete($diskPath);
                }
            }
        }

        $certificate->delete();

        return redirect()->route('admin.compliance_certificates.index')
            ->with('success', 'Compliance Certificate deleted successfully.');
    }
}
