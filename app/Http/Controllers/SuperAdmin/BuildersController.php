<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BuildersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Base query
        $baseQuery = User::where('role', 'user');

        // Counts
        $totalUsers  = (clone $baseQuery)->count();
        $activeUsers = (clone $baseQuery)->where('status', 'active')->count();
        $pendingUsers = (clone $baseQuery)->where('status', 'pending')->count();
        $blockedUsers = (clone $baseQuery)->where('status', 'blocked')->count();

        // Filtered listing
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

        return view('superadmin.builders.index', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'blockedUsers',
            'pendingUsers'
        ));
    }




    public function export(Request $request)
    {
        $fileName = 'builders_' . now()->format('Y_m_d_His') . '.csv';

        $builders = User::query()
            ->where('role', 'user')

            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%');
                });
            })

            ->when(
                $request->status,
                fn($q) =>
                $q->where('status', $request->status)
            )

            ->when(
                $request->specialty,
                fn($q) =>
                $q->where('specialty', $request->specialty)
            )

            ->orderByDesc('id')
            ->get();

        $headers = [
            "Content-Type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $columns = [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Status',
            'Specialty',
            'Joined At'
        ];

        return new StreamedResponse(function () use ($builders, $columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($builders as $builder) {
                fputcsv($handle, [
                    $builder->id,
                    $builder->name,
                    $builder->email,
                    $builder->phone,
                    ucfirst($builder->status),
                    ucfirst($builder->specialty),
                    $builder->created_at->format('Y-m-d'),
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
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view("superadmin.builders.edit", compact("user"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            "name"  => "required|string|max:255",
            "email" => "required|email|unique:users,email," . $user->id,
            "phone" => "nullable|string|max:20",
            "status" => "required|in:active,pending,blocked",
        ]);
        

        $user->update([
            "name"  => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "status" => $request->status,
        ]);

        return redirect()->route("superadmin.builders.index")->with("success", "Builder updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route("superadmin.builders.index")->with("success", "Builder deleted successfully.");
    }
}
