<?php

namespace App\Http\Controllers\Admin;

use \Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use \App\Models\Appliance;
use App\Models\HousePlan;
use Illuminate\Support\Facades\Storage;


class PropertiesController extends Controller
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
        $properties = Property::where('user_id', Auth::id())->latest()->paginate(10);
        return view('admin.properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formTitle = 'Add Properties';
        $smallHeading = 'Basic property details';
        $appliances = Appliance::where('user_id', Auth::id())->get();
        $housePlans = HousePlan::where('user_id', Auth::id())->get();
        return view('admin.properties.create', compact('formTitle', 'smallHeading', 'appliances', 'housePlans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'property_title'       => 'required|max:255',
            'property_type'        => 'required|string',
            'address'              => 'required|string',
            'house_plan_id'        => 'required|exists:house_plans,id',
            'house_plan_name'      => 'nullable|string|max:255',
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

            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
        ]);

        // Remove file fields
        $data = $request->except(['floor_plan_upload']);

        // Store floor plan images
        if ($request->hasFile('floor_plan_upload')) {
            $floorPlans = [];

            foreach ($request->file('floor_plan_upload') as $image) {
                $floorPlans[] = $image->store('floor_plans', 'public');
            }

            $data['floor_plan_upload'] = json_encode($floorPlans);
        }

        // Cast swimming_pool properly
        $data['swimming_pool'] = $request->boolean('swimming_pool');

        $data['user_id'] = Auth::id();

        Property::create($data);

        return redirect()
            ->route('admin.properties.index')
            ->with('success', 'Property created successfully.');
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
    public function edit(Property $property)
    {
        $floorPlans = $property->floor_plan_upload;
        $appliances =Appliance::where('user_id', Auth::id())->get();
        $formTitle = 'Edit Properties';
        $smallHeading = 'Basic property details';
        $housePlans = HousePlan::where('user_id', Auth::id())->get();
        return view('admin.properties.edit', compact('property', 'floorPlans', 'formTitle', 'smallHeading', 'appliances', 'housePlans'));
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

            'latitude'       => 'nullable|numeric|between:-90,90',
            'longitude'      => 'nullable|numeric|between:-180,180',
        ]);

        // Remove file-related fields
        $data = $request->except([
            'floor_plan_upload',
            'existing_floor_plans',
            'removed_existing_images'
        ]);

        // =============================
        // 1️⃣ Start with existing images
        // =============================
        $floorPlans = $request->input('existing_floor_plans', []);

        // =============================
        // 2️⃣ Delete removed images
        // =============================
        if ($request->filled('removed_existing_images')) {
            foreach ($request->removed_existing_images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        // =============================
        // 3️⃣ Upload new images
        // =============================
        if ($request->hasFile('floor_plan_upload')) {
            foreach ($request->file('floor_plan_upload') as $image) {
                $floorPlans[] = $image->store('floor_plans', 'public');
            }
        }

        // =============================
        // 4️⃣ Save final images as JSON
        // =============================
        $data['floor_plan_upload'] = json_encode($floorPlans);

        $data['swimming_pool'] = $request->boolean('swimming_pool');
        $data['user_id'] = Auth::id();

        $property->update($data);

        return redirect()
            ->route('admin.properties.index')
            ->with('success', 'Property updated successfully.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Property $property)
    {
        $property->delete();
        return redirect()->route('admin.properties.index')
            ->with('success', 'Property deleted successfully.');
    }
}
