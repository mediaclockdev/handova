<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ServiceProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formTitle = 'Service Providers';
        $serviceproviders = User::where('role', 'service_provider')->latest()->paginate(10);
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
            'company_name'           => 'nullable|string|max:255',
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'email'                  => 'required|email|max:255|unique:service_providers,email_address',
            'phone'                  => 'nullable|string|max:20',
            'service_specialisation' => 'nullable|string|max:255',
            'service_type'           => 'nullable|string|max:255',
        ]);
        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $validated['role'] = 'service_provider';
        $validated['password'] = bcrypt('password');
        User::create($validated);
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
        $serviceProvider = \App\Models\User::findOrFail($id);
        $formTitle = 'Update Service Provider';
        return view('admin.service_provider.edit', compact('serviceProvider', 'formTitle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $serviceProvider = User::findOrFail($id);
            $validated = $request->validate([
                'company_name'           => 'nullable|string|max:255',
                'first_name'             => 'required|string|max:255',
                'last_name'              => 'required|string|max:255',
                'email'                  => 'required|email|max:255|unique:users,email,' . $serviceProvider->id,
                'phone'                  => 'nullable|string|max:20',
                'service_specialisation' => 'nullable|string|max:255',
                'service_type'           => 'nullable|string|max:255',
            ]);
            $serviceProvider->update($validated);
            return redirect()->route('admin.service_provider.index')->with('success', 'Service provider updated successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Something went wrong while updating the service provider.');
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $serviceProvider = User::findOrFail($id);
        $serviceProvider->delete();
        return redirect()->route('admin.service_provider.index')->with('success', 'Service Provider deleted successfully.');
    }
}
