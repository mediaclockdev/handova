<?php

namespace App\Http\Controllers\Admin;

use \Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\ServiceProvider;
use App\Models\IssueReport;
use \App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth'); // This will redirect to login if not authenticated
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $formTitle = 'User Profile';
        $user = Auth::user(); // fetch logged-in user
        return view('admin.profile.index', compact('formTitle', 'user'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'phone'           => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
            'existing_profile_picture' => 'nullable|string',
            'remove_profile_picture'   => 'nullable|boolean',
        ]);

        // =============================
        // 1️⃣ Update basic user info
        // =============================
        $user->name       = $request->first_name . ' ' . $request->last_name;
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        $user->phone      = $request->phone;

        // =============================
        // 2️⃣ Start with existing image
        // =============================
        $profilePicture = $request->existing_profile_picture ?? $user->profile_picture;

        // =============================
        // 3️⃣ Remove existing image (if requested)
        // =============================
        if ($request->boolean('remove_profile_picture') && $profilePicture) {
            Storage::disk('public')->delete(str_replace('storage/', '', $profilePicture));
            $profilePicture = null;
        }

        // =============================
        // 4️⃣ Upload new image
        // =============================
        if ($request->hasFile('profile_picture')) {

            // Delete old image first
            if ($profilePicture) {
                Storage::disk('public')->delete(str_replace('storage/', '', $profilePicture));
            }

            $path = $request->file('profile_picture')->store('profile', 'public');
            $profilePicture = 'storage/' . $path;
        }

        // =============================
        // 5️⃣ Save final image
        // =============================
        $user->profile_picture = $profilePicture;

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
