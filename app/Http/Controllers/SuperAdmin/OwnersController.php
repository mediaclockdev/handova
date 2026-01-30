<?php

namespace App\Http\Controllers\SuperAdmin;

use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Property;
use App\Models\HouseOwner;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OwnersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $houseowners = HouseOwner::with('property')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('first_name', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('email_address', 'LIKE', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('property_id'), function ($query) use ($request) {
                $query->where('properties_id', $request->property_id);
            })
            ->when($request->filled('property_status'), function ($query) use ($request) {
                $query->whereHas('property', function ($q) use ($request) {
                    $q->where('property_status', $request->property_status);
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Property list for dropdown
        $properties = Property::select('id', 'property_title')->orderBy('property_title')->get();

        // Counts
        $totalProperties = HouseOwner::count();
        $availableProperties = Property::where('property_status', 'available')->count();
        $pendingProperties = Property::where('property_status', 'pending')->count();
        $soldProperties = Property::where('property_status', 'sold')->count();

        return view('superadmin.owners.index', compact(
            'houseowners',
            'properties',
            'totalProperties',
            'availableProperties',
            'pendingProperties',
            'soldProperties'
        ));
    }


    public function export(Request $request)
    {
        $fileName = 'house_owners_' . now()->format('Y_m_d_His') . '.csv';

        $houseowners = HouseOwner::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('first_name', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $request->search . '%')
                        ->orWhere('email_address', 'LIKE', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('property_id'), function ($query) use ($request) {
                $query->where('properties_id', $request->property_id);
            })
            ->when($request->filled('property_status'), function ($query) use ($request) {
                $query->whereHas('property', function ($q) use ($request) {
                    $q->where('property_status', $request->property_status);
                });
            })
            ->orderByDesc('id')
            ->get();

        $headers = [
            "Content-Type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $columns = [
            'ID',
            'House Owner Id',
            'Property Title',
            'First Name',
            'Last Name',
            'Phone Number',
            'Email',
            'Address Of Property',
            'House Plan Name',
            'Tags',
            'Internal Notes',
            'Created At',
        ];

        return new StreamedResponse(function () use ($houseowners, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($houseowners as $houseowner) {
                fputcsv($handle, [
                    $houseowner->id,
                    $houseowner->house_owner_id,
                    $houseowner->property->property_title ?? 'N/A',
                    $houseowner->first_name,
                    $houseowner->last_name,
                    $houseowner->phone_number,
                    $houseowner->email_address,
                    $houseowner->address_of_property,
                    $houseowner->house_plan_name,
                    $houseowner->tags,
                    $houseowner->internal_notes,
                    $houseowner->created_at->format('Y-m-d'),
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
        return view('superadmin.builders.create');
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name"  => "required|string|max:255",
            "email" => "required|email|unique:users,email",
            "phone" => "nullable|string|max:20",
            "password" => "required|min:6",
            "status" => "required|in:active,pending,blocked",
        ]);

        User::create([
            "name"     => $request->name,
            "email"    => $request->email,
            "phone"    => $request->phone,
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
    public function edit(string $id)
    {
        $owner = HouseOwner::findOrFail($id);
        $properties = Property::select('id', 'property_title')->get();
        $phoneUtil = PhoneNumberUtil::getInstance();

        $countryCode = '';
        $nationalNumber = '';
        $countryIso = '';

        if (!empty($owner->phone_number)) {
            $number = $phoneUtil->parse($owner->phone_number, 'AU');

            $countryCode = '+' . $number->getCountryCode();
            $nationalNumber = $number->getNationalNumber();
            $countryIso = strtolower(
                $phoneUtil->getRegionCodeForNumber($number)
            );
        }
        $formTitle = 'Update House Owner';
        $houseOwnerId = $owner->house_owner_id;
        return view('superadmin.owners.edit', compact('owner', 'properties', 'formTitle', 'houseOwnerId', 'countryCode', 'nationalNumber', 'countryIso'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $houseOwner = HouseOwner::findOrFail($id);

        $request->validate([
            'properties_id'             => 'required|exists:properties,id',
            'first_name'                => 'required|string|max:255',
            'last_name'                 => 'required|string|max:255',
            'email_address'             => 'required|email|unique:house_owners,email_address,' . $id,

            'country_code'              => 'nullable|string|max:5',
            'phone_number'              => 'required|string|min:6|max:20',

            'address_of_property'       => 'required|string',
            'house_plan_name'           => 'nullable|string|max:255',
            'build_completion_date'     => 'nullable|date',
            'assigned_builder_site_manager' => 'nullable|string|max:255',
            'number_of_bedrooms'        => 'nullable|integer',
            'number_of_bathrooms'       => 'nullable|integer',
            'parking'                   => 'nullable|string|max:255',
            'property_status'           => 'required|string|max:255',
            'tags'                      => 'nullable|string',
            'internal_notes'            => 'nullable|string',

            'handover_documents'        => 'nullable|array',
            'handover_documents.*'      => 'file|mimes:jpg,jpeg,png,pdf,csv,doc,docx|max:2048',

            'floor_plan_upload'         => 'nullable|array',
            'floor_plan_upload.*'       => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'existing_handover_documents' => 'nullable|array',
            'existing_handover_documents.*' => 'string',

            'existing_floor_plan_upload' => 'nullable|array',
            'existing_floor_plan_upload.*' => 'string',

            'remove_handover_documents' => 'nullable|array',
            'remove_handover_documents.*' => 'string',

            'remove_floor_plan_upload'  => 'nullable|array',
            'remove_floor_plan_upload.*' => 'string',
        ]);

        // Remove file fields
        $data = $request->except([
            'handover_documents',
            'floor_plan_upload',
            'existing_handover_documents',
            'existing_floor_plan_upload',
            'remove_handover_documents',
            'remove_floor_plan_upload',
        ]);

        $data['properties_id'] = (int) $data['properties_id'];

        $phoneUtil   = PhoneNumberUtil::getInstance();
        $rawPhone    = trim($request->phone_number);
        $countryCode = trim($request->country_code);

        try {
            if (str_starts_with($rawPhone, '+')) {
                // Already E.164
                $number = $phoneUtil->parse($rawPhone, null);
            } else {
                if (empty($countryCode)) {
                    return back()
                        ->withInput()
                        ->with('error', 'Country code is required.');
                }

                $cleanPhone = preg_replace('/\D+/', '', $rawPhone);
                $fullPhone  = $countryCode . $cleanPhone;

                $number = $phoneUtil->parse($fullPhone, null);
            }

            if (!$phoneUtil->isValidNumber($number)) {
                return back()
                    ->withInput()
                    ->with('error', 'Invalid phone number for selected country.');
            }

            $data['phone_number'] = $phoneUtil->format(
                $number,
                PhoneNumberFormat::E164
            );
        } catch (NumberParseException $e) {
            return back()
                ->withInput()
                ->with('error', 'Invalid phone number format.');
        }

        $handoverDocs = $request->input('existing_handover_documents', []);
        $floorPlans   = $request->input('existing_floor_plan_upload', []);

        if ($request->filled('remove_handover_documents')) {
            foreach ($request->remove_handover_documents as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        if ($request->filled('remove_floor_plan_upload')) {
            foreach ($request->remove_floor_plan_upload as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        if ($request->hasFile('handover_documents')) {
            foreach ($request->file('handover_documents') as $file) {
                $handoverDocs[] = $file->store('handover_docs', 'public');
            }
        }

        if ($request->hasFile('floor_plan_upload')) {
            foreach ($request->file('floor_plan_upload') as $file) {
                $floorPlans[] = $file->store('floor_plans', 'public');
            }
        }

        $data['handover_documents'] = json_encode($handoverDocs);
        $data['floor_plan_upload']  = json_encode($floorPlans);

        $houseOwner->update($data);
        return redirect()
            ->route('superadmin.owners.index')
            ->with('success', 'House Owner updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = HouseOwner::findOrFail($id);
        $user->delete();

        return redirect()->route("superadmin.owners.index")->with("success", "House Owners deleted successfully.");
    }

    public function suspend($id)
    {
        $user = Property::findOrFail($id);

        $user->update([
            'property_status' => 'pending',
        ]);

        return redirect()
            ->route('superadmin.properties.index')
            ->with('success', 'Properties status updated successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|string',
            'property_ids' => 'required|string',
        ]);

        $ids = explode(',', $request->property_ids);

        Property::whereIn('id', $ids)->update([
            'property_status' => $request->action
        ]);

        $statusText = $request->action == 1 ? 'Available' : 'Sold';

        return redirect()
            ->back()
            ->with('success', $statusText . ' properties updated successfully.');
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
