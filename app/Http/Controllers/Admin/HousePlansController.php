<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HousePlan;
use App\Models\Appliance;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HousePlansController extends Controller
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
        $formTitle = 'House Plans';
        $houseplans = HousePlan::with('property')->where('user_id', auth()->id())->latest()->paginate(10);
        $properties = Property::select('id', 'property_title')->where('user_id', Auth::id())->get();
        return view('admin.house_plans.index', compact('formTitle', 'houseplans', 'properties'));
    }

    public function getHousePlansByProperty($propertyId)
    {
        $plans = HousePlan::where('property_id', $propertyId)->get();
        return response()->json($plans);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formTitle = 'New House Plan';
        $properties = Property::select('id', 'property_title')->where('user_id', Auth::id())->get();
        $appliances = Appliance::where('user_id', Auth::id())->get();
        $housePlan = new HousePlan();
        return view('admin.house_plans.create', compact('formTitle', 'properties', 'appliances', 'housePlan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_name'          => 'required|string|max:255',
            'storey'             => 'required|string',
            'pricing'            => 'required|string',
            'house_area'         => 'nullable|string',
            'suburbs'            => 'nullable|string',
            'display_location'   => 'required|string',

            'floor'                      => 'nullable|array',
            'floor.*.bedrooms'           => 'nullable|integer',
            'floor.*.bathrooms'          => 'nullable|integer',
            'floor.*.parking'            => 'nullable|string',
            'floor.*.swimming_pool'      => 'nullable|string',
            'floor.*.appliances'         => 'nullable|array',
            'floor.*.appliances.*'       => 'exists:appliances,id',

            'floor_plan'                 => 'nullable|array',
            'floor_plan.*.*'             => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $floorDetails = [];

        if ($request->has('floor')) {
            foreach ($request->floor as $floorKey => $floorData) {

                // Skip floor if all fields are empty
                $hasData =
                    !empty($floorData['bedrooms']) ||
                    !empty($floorData['bathrooms']) ||
                    !empty($floorData['parking']) ||
                    !empty($floorData['swimming_pool']) ||
                    !empty($floorData['appliances']) ||
                    $request->hasFile("floor_plan.$floorKey");

                if (!$hasData) {
                    continue; // 🚫 Skip this floor completely
                }

                $images = [];

                if ($request->hasFile("floor_plan.$floorKey")) {
                    foreach ($request->file("floor_plan.$floorKey") as $file) {
                        $images[] = $file->store('house_plan', 'public');
                    }
                }

                $floorDetails[$floorKey] = [
                    'bedrooms'      => $floorData['bedrooms'] ?? null,
                    'bathrooms'     => $floorData['bathrooms'] ?? null,
                    'parking'       => $floorData['parking'] ?? null,
                    'swimming_pool' => $floorData['swimming_pool'] ?? null,
                    'appliances'    => $floorData['appliances'] ?? [],
                    'floor_plan'    => $images,
                ];
            }
        }

        if ($request->pricing <= 0) {
            return back()->withInput()->with('error', 'Pricing must be greater than zero.');
        }

        if ($request->pricing > 500000000) {
            return back()->withInput()->with('error', 'Pricing exceeds allowed limit.');
        }

        HousePlan::create([
            'plan_name'        => $request->plan_name,
            'pricing'          => $request->pricing,
            'house_area'       => $request->house_area,
            'suburbs'          => $request->suburbs,
            'display_location' => $request->display_location,
            'storey'           => $request->storey,

            // Laravel will JSON encode if casted
            'floor_details'    => $floorDetails,

            'user_id'          => auth()->id(),
        ]);

        return redirect()
            ->route('admin.house_plans.index')
            ->with('success', 'House Plan created successfully');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $housePlan = HousePlan::findOrFail($id);

        // Normalize floor data for form
        $housePlan->floor = $housePlan->floor_details ?? [];

        $properties = Property::select('id', 'property_title')
            ->where('user_id', Auth::id())
            ->get();

        $appliances = Appliance::select('id', 'brand_name', 'model')->where('user_id', Auth::id())->get();

        $formTitle = 'Edit House Plan';

        return view(
            'admin.house_plans.edit',
            compact('housePlan', 'formTitle', 'properties', 'appliances')
        );
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $housePlan = HousePlan::findOrFail($id);

        $request->validate([
            'plan_name'        => 'required|string|max:255',
            'storey'           => 'required|string',
            'pricing'          => 'required|string',
            'house_area'       => 'nullable|string',
            'suburbs'          => 'nullable|string',
            'display_location' => 'required|string',

            'floor'                    => 'nullable|array',
            'floor.*.bedrooms'         => 'nullable|integer',
            'floor.*.bathrooms'        => 'nullable|integer',
            'floor.*.parking'          => 'nullable|string',
            'floor.*.swimming_pool'    => 'nullable|string',

            'floor.*.appliances'       => 'nullable|array',
            'floor.*.appliances.*'     => 'exists:appliances,id',

            'floor_plan'               => 'nullable|array',
            'floor_plan.*.*'           => 'image|mimes:jpg,jpeg,png,webp|max:2048',

            'existing_floor_plan'      => 'nullable|array',
            'existing_floor_plan.*.*'  => 'nullable|string',
        ]);

        $floorDetails = [];

        if ($request->has('floor')) {
            foreach ($request->floor as $floorKey => $floorData) {

                $existingImages = $request->input("existing_floor_plan.$floorKey", []);

                $hasData =
                    !empty($floorData['bedrooms']) ||
                    !empty($floorData['bathrooms']) ||
                    !empty($floorData['parking']) ||
                    !empty($floorData['swimming_pool']) ||
                    !empty($floorData['appliances']) ||
                    !empty($existingImages) ||
                    $request->hasFile("floor_plan.$floorKey");

                // 🚫 Skip unselected / empty floors
                if (!$hasData) {
                    continue;
                }

                $images = $existingImages;

                // Store new uploads
                if ($request->hasFile("floor_plan.$floorKey")) {
                    foreach ($request->file("floor_plan.$floorKey") as $file) {
                        $images[] = $file->store('house_plan', 'public');
                    }
                }

                $floorDetails[$floorKey] = [
                    'bedrooms'      => $floorData['bedrooms'] ?? null,
                    'bathrooms'     => $floorData['bathrooms'] ?? null,
                    'parking'       => $floorData['parking'] ?? null,
                    'swimming_pool' => $floorData['swimming_pool'] ?? null,
                    'appliances'    => $floorData['appliances'] ?? [],
                    'floor_plan'    => $images,
                ];
            }
        }

        if ($request->pricing <= 0) {
            return back()->withInput()->with('error', 'Pricing must be greater than zero.');
        }

        if ($request->pricing > 5000000) {
            return back()->withInput()->with('error', 'Pricing exceeds allowed limit.');
        }

        $housePlan->update([
            'plan_name'        => $request->plan_name,
            'pricing'          => $request->pricing,
            'house_area'       => $request->house_area,
            'suburbs'          => $request->suburbs,
            'display_location' => $request->display_location,
            'storey'           => $request->storey,
            'floor_details'    => $floorDetails,
            'user_id'          => auth()->id(),
        ]);

        return redirect()
            ->route('admin.house_plans.index')
            ->with('success', 'House Plan updated successfully');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $housePlan = HousePlan::findOrFail($id);
        if ($housePlan->floor_plan && Storage::disk('public')->exists($housePlan->floor_plan)) {
            Storage::disk('public')->delete($housePlan->floor_plan);
        }
        $housePlan->delete();

        return redirect()->route('admin.house_plans.index')->with('success', 'House Plan deleted successfully.');
    }
}
