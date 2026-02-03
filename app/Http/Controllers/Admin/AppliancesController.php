<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appliance;
use Illuminate\Support\Facades\Log;

class AppliancesController extends Controller
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
        $formTitle = "Appliances";
        $appliances = Appliance::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('admin.appliances.index', compact('formTitle', 'appliances'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $lastAppliance = \App\Models\Appliance::orderBy('id', 'desc')->first();
        $nextNumber = $lastAppliance ? ((int) filter_var($lastAppliance->appliance_id, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
        $nextApplianceId = 'APP' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        $formTitle = 'Add New Appliances';
        return view('admin.appliances.create', compact('formTitle', 'nextApplianceId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'appliance_id'        => 'required|unique:appliances,appliance_id',
            'appliance_name'      => 'required|string',
            'product_details'     => 'nullable|string',
            'brand_name'          => 'required|string',
            'model'               => 'nullable|string',
            'warranty_information' => 'nullable|string',

            'manuals'             => 'nullable|array',
            'manuals.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,csv,xls,xlsx|max:10240',

            'appliances_images'   => 'nullable|array',
            'appliances_images.*' => 'image|mimes:jpeg,png,jpg,gif,webp',
        ]);

        $data = $request->except(['manuals', 'appliances_images']);

        // Store appliance images
        if ($request->hasFile('appliances_images')) {
            $images = [];

            foreach ($request->file('appliances_images') as $image) {
                $images[] = $image->store('appliances_images', 'public');
            }

            $data['appliances_images'] = json_encode($images);
        }

        // Store manuals
        if ($request->hasFile('manuals')) {
            $manuals = [];

            foreach ($request->file('manuals') as $manual) {
                $manuals[] = $manual->store('manuals', 'public');
            }

            $data['manuals'] = json_encode($manuals);
        }

        $data['user_id'] = auth()->id();

        Appliance::create($data);

        return redirect()
            ->route('admin.appliances.index')
            ->with('success', 'Appliance created successfully.');
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
        $appliances = Appliance::findOrFail($id);
        $appliances = \App\Models\Appliance::findOrFail($id);
        $nextApplianceId = $appliances->appliance_id;
        $formTitle = 'Update Appliance Details';
        return view('admin.appliances.edit', compact('appliances', 'formTitle', 'nextApplianceId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $appliance = Appliance::findOrFail($id);

            $request->validate([
                'appliance_id'          => 'required|max:255|unique:appliances,appliance_id,' . $id,
                'appliance_name'        => 'required|string',
                'product_details'       => 'nullable|string',
                'brand_name'            => 'required|string',
                'model'                 => 'nullable|string',
                'warranty_information'  => 'nullable|string',

                'manuals'               => 'nullable|array',
                'manuals.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png,gif,csv,xls,xlsx|max:10240',

                'appliances_images'     => 'nullable|array',
                'appliances_images.*'   => 'image|mimes:jpeg,png,jpg,gif,,webp',
            ]);

            $data = $request->except(['manuals', 'appliances_images']);

            /* -------- EXISTING FILES -------- */
            $existingImages  = $request->input('existing_appliances_images', []);
            $existingManuals = $request->input('existing_manuals', []);

            /* -------- NEW IMAGE UPLOADS -------- */
            if ($request->hasFile('appliances_images')) {
                foreach ($request->file('appliances_images') as $image) {
                    $existingImages[] = $image->store('appliances_images', 'public');
                }
            }

            /* -------- NEW MANUAL UPLOADS -------- */
            if ($request->hasFile('manuals')) {
                foreach ($request->file('manuals') as $manual) {
                    $existingManuals[] = $manual->store('manuals', 'public');
                }
            }

            $data['appliances_images'] = json_encode($existingImages);
            $data['manuals'] = json_encode($existingManuals);
            $data['user_id'] = auth()->id();

            $appliance->update($data);

            return redirect()
                ->route('admin.appliances.index')
                ->with('success', 'Appliance updated successfully.');
        } catch (\Throwable $e) {

            Log::error('Appliance update failed', [
                'appliance_id' => $id,
                'user_id'      => auth()->id(),
                'message'      => $e->getMessage(),
                'file'         => $e->getFile(),
                'line'         => $e->getLine(),
                'trace'        => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the appliance.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Appliances = Appliance::findOrFail($id);
        $Appliances->delete();
        return redirect()->route('admin.appliances.index')->with('success', 'Appliances deleted successfully.');
    }
}
