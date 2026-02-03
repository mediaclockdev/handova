<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $plans = SubscriptionPlan::all();
        return view('superadmin.plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'plan_name' => 'required|string|max:255',
            'plan_type' => 'required|string|max:255',
            'plan_price' => 'required|numeric',
            'plan_duration' => 'required|integer',
            'plan_duration_unit' => 'required|string|max:255',
            'plan_allowed_listing' => 'required|integer',
            'plan_video_upload_limit' => 'required|integer',
            'plan_featured_properties' => 'required|integer',
            'plan_photo_upload_limit' => 'required|integer',
            'plan_additional_feature' => 'required|array',
            'plan_description' => 'required|string',
        ]);
        SubscriptionPlan::create($data);
        return redirect()->route('superadmin.plans.index')->with('success', 'Plan created successfully');
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
        $plan = SubscriptionPlan::findOrFail($id);
        return view('superadmin.plans.edit', compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'plan_name' => 'required|string|max:255',
            'plan_type' => 'required|string|max:255',
            'plan_price' => 'required|numeric',
            'plan_duration' => 'required|integer',
            'plan_duration_unit' => 'required|string',
            'plan_allowed_listing' => 'required|integer',
            'plan_video_upload_limit' => 'required|integer',
            'plan_additional_feature' => 'required|array',
            'plan_photo_upload_limit' => 'required|integer',
            'plan_additional_feature' => 'required|array',
            'plan_description' => 'required|string',
        ]);


        $plan = SubscriptionPlan::findOrFail($id);
        $plan->update($data);

        return redirect()->route('superadmin.plans.index')->with('success', 'Plan updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        SubscriptionPlan::findOrFail($id)->delete();
        return redirect()->route('superadmin.plans.index')->with('success', 'Plan deleted successfully');
    }
}
