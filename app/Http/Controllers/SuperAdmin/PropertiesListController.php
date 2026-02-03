<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Property;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertiesListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $properties = Property::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('property_title', 'LIKE', '%' . $request->search . '%');
            })
            ->when($request->filled('property_type'), function ($query) use ($request) {
                $query->where('property_type', $request->property_type);
            })
            ->when($request->filled('property_status'), function ($query) use ($request) {
                $query->where('property_status', $request->property_status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString(); // keeps search text during pagination

        // Counts
        $totalProperties = Property::count();
        $availableProperties = Property::where('property_status', 'available')->count();
        $pendingProperties = Property::where('property_status', 'pending')->count();
        $soldProperties = Property::where('property_status', 'sold')->count();

        return view('superadmin.properties.index', compact(
            'properties',
            'totalProperties',
            'availableProperties',
            'pendingProperties',
            'soldProperties'
        ));
    }


    public function export(Request $request)
    {
        $fileName = 'properties_' . now()->format('Y_m_d_His') . '.csv';

        $properties = Property::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('property_type'), function ($q) use ($request) {
                $q->where('property_type', $request->property_type);
            })
            ->orderByDesc('id')
            ->get();

        $headers = [
            "Content-Type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $columns = [
            'ID',
            'Property Title',
            'Property Type',
            'Address',
            'House Plan Name',
            'Build Completion Date',
            'Property Status',
            'No Of Bathroom',
            'No Of Bedroom',
            'Parking',
            'Swimming Pool',
            'Tags',
            'Internal Notes',
            'Compliance Certificates',
            'Created At',
        ];

        return new StreamedResponse(function () use ($properties, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($properties as $property) {
                fputcsv($handle, [
                    $property->id,
                    $property->property_title,
                    ucfirst($property->property_type),
                    $property->address,
                    $property->house_plan_name,
                    $property->build_completion_date,
                    ucfirst($property->property_status),
                    $property->number_of_bathrooms,
                    $property->number_of_bedrooms,
                    $property->parking,
                    $property->swimming_pool,
                    $property->tags,
                    $property->internal_notes,
                    $property->compliance_certificate,
                    $property->created_at->format('Y-m-d'),
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
    public function edit(Property $property)
    {
        $floorPlans   = $property->floor_plan_upload;
        $appliances   = \App\Models\Appliance::all();
        $housePlans   = \App\Models\HousePlan::all();

        $formTitle    = 'Edit Properties';
        $smallHeading = 'Basic property details';

        return view(
            'superadmin.properties.edit',
            compact(
                'property',
                'floorPlans',
                'formTitle',
                'smallHeading',
                'appliances',
                'housePlans'
            )
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Property $property)
    {
        $request->validate([
            'property_title'       => 'required|max:255',
            'property_type'        => 'required|string',
            'address'              => 'required|string',
            'house_plan_name'      => 'nullable|string|max:255',
            'house_plan_id'        => 'required|exists:house_plans,id',
            'build_completion_date' => 'nullable|date',
            'assigned_builder_site_manager' => 'nullable|string',

            'number_of_bedrooms'   => 'nullable|integer|min:0',
            'number_of_bathrooms'  => 'nullable|integer|min:0',
            'parking'              => 'nullable|string|max:255',
            'swimming_pool'        => 'nullable|boolean',

            'floor_plan_upload'    => 'nullable|array',
            'floor_plan_upload.*'  => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',

            'property_status'      => 'required|string',
            'appliance_id'         => 'nullable|array',
            'tags'                 => 'nullable|string',
            'internal_notes'       => 'nullable|string',
            'compliance_certificate' => 'required|string',

            'existing_floor_plans' => 'nullable|array',
            'existing_floor_plans.*' => 'string',

            'removed_existing_images' => 'nullable|array',
            'removed_existing_images.*' => 'string',
        ]);

        // Remove file-related fields
        $data = $request->except([
            'floor_plan_upload',
            'existing_floor_plans',
            'removed_existing_images'
        ]);

        $floorPlans = $request->input('existing_floor_plans', []);

        if ($request->filled('removed_existing_images')) {
            foreach ($request->removed_existing_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        if ($request->hasFile('floor_plan_upload')) {
            foreach ($request->file('floor_plan_upload') as $image) {
                $floorPlans[] = $image->store('floor_plans', 'public');
            }
        }

        $data['floor_plan_upload'] = json_encode($floorPlans);

        $data['swimming_pool'] = $request->boolean('swimming_pool');
        

        $property->update($data);

        return redirect()
            ->route('superadmin.properties.index')
            ->with('success', 'Property updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Property::findOrFail($id);
        $user->delete();

        return redirect()->route("superadmin.properties.index")->with("success", "Properties deleted successfully.");
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
