<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;

class ServiceProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formTitle = 'Service Providers';
        $serviceproviders = ServiceProvider::where('user_id', auth()->id())->latest()->paginate(10);
        return view('admin.service_provider.index', compact('formTitle', 'serviceproviders'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formTitle = 'New Service Providers';
        return view('admin.service_provider.create', compact('formTitle'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name'           => 'required|string|max:255',
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'email_address'          => 'required|email|max:255|unique:service_providers,email_address',
            'phone_number'           => 'nullable|string|max:20',
            'service_specialisation' => 'required|string|max:255',
            'service_type'           => 'required|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();
        ServiceProvider::create($validated);
        return redirect()->route('admin.service_provider.index')->with('success', 'Service provider created successfully.');
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
        $serviceProvider = ServiceProvider::findOrFail($id);
        $formTitle = 'Update Service Provider';
        return view('admin.service_provider.edit', compact('serviceProvider', 'formTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $serviceProvider = ServiceProvider::findOrFail($id);

        $validated = $request->validate([
            'company_name'           => 'required|string|max:255',
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'email_address'          => 'required|email|max:255|unique:service_providers,email_address,' . $serviceProvider->id,
            'phone_number'           => 'nullable|string|max:20',
            'service_specialisation' => 'required|string|max:255',
            'service_type'           => 'required|string|max:255',
        ]);

        $serviceProvider->update($validated);
        $validated['user_id'] = auth()->id();
        return redirect()->route('admin.service_provider.index')
            ->with('success', 'Service provider updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $serviceProvider = ServiceProvider::findOrFail($id);
        $serviceProvider->delete();
        return redirect()->route('admin.service_provider.index')->with('success', 'Service Provider deleted successfully.');
    }
}
