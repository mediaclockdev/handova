<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $baseQuery = User::where('role', 'user');
        $totalUsers  = (clone $baseQuery)->count();
        $activeUsers = (clone $baseQuery)->where('status', 'active')->count();
        $pendingUsers = (clone $baseQuery)->where('status', 'pending')->count();
        $blockedUsers = (clone $baseQuery)->where('status', 'suspended')->count();
        $users = $baseQuery
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->specialty, function ($q) use ($request) {
                $q->where('specialty', $request->specialty);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

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
            ->withQueryString();

        // Counts
        $totalProperties = Property::count();
        $availableProperties = Property::where('property_status', 'available')->count();
        $pendingProperties = Property::where('property_status', 'pending')->count();
        $soldProperties = Property::where('property_status', 'sold')->count();

        return view('superadmin.dashboard', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'blockedUsers',
            'pendingUsers',
            'totalProperties',
            'availableProperties',
            'pendingProperties',
            'soldProperties'
        ));
    }
}
