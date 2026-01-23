<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ServiceProvider;
use \App\Models\Specialization;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServiceSpecializationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $baseQuery = Specialization::query();
        $totalSpecializations = (clone $baseQuery)->count();

        $specializations = $baseQuery
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('specialization', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'superadmin.specialization.index',
            compact('specializations', 'totalSpecializations')
        );
    }


    public function export(Request $request)
    {
        $fileName = 'service_specializations_' . now()->format('Y_m_d_His') . '.csv';

        $specializations = Specialization::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('specialization', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderByDesc('id')
            ->get();

        $headers = [
            "Content-Type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}",
        ];

        $columns = [
            'ID',
            'Specialization Name',
            'Status',
            'Created At',
        ];

        return new StreamedResponse(function () use ($specializations, $columns) {
            $handle = fopen('php://output', 'w');

            // CSV Header
            fputcsv($handle, $columns);

            // CSV Rows
            foreach ($specializations as $specialization) {
                fputcsv($handle, [
                    $specialization->id,
                    $specialization->specialization,
                    ucfirst($specialization->status),
                    $specialization->created_at->format('Y-m-d H:i:s'),
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
        return view('superadmin.specialization.create');
    }

    /**
     * Store a newly created resource.
     */
    public function store(Request $request)
    {
        $request->validate([
            'specialization' => 'required|string|max:255',
            'status'         => 'required|in:active,inactive',
        ]);

        Specialization::create([
            'specialization' => $request->specialization,
            'status'         => $request->status,
        ]);

        return redirect()->route("superadmin.specialization.index")->with("success", "Service Specialization created successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view("superadmin.providers.show", compact("user"));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $specialization = Specialization::findOrFail($id);
        return view("superadmin.specialization.edit", compact("specialization"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Specialization::findOrFail($id);

        $request->validate([
            'specialization' => 'required|string|max:255',
            'status'         => 'required|in:active,inactive',
        ]);

        $user->update([
            'specialization' => $request->specialization,
            'status'         => $request->status,
        ]);

        return redirect()->route("superadmin.specialization.index")->with("success", "Service Specialization updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Specialization::findOrFail($id);
        $user->delete();

        return redirect()->route("superadmin.specialization.index")->with("success", "Specialization deleted successfully.");
    }

    public function suspend($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'status' => 'suspended',
        ]);

        return redirect()
            ->route('superadmin.providers.index')
            ->with('success', 'Service Provider account suspended successfully.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:active,inactive',
            'user_ids' => 'required'
        ]);

        $ids = explode(',', $request->user_ids);

        Specialization::whereIn('id', $ids)->update([
            'status' => $request->action
        ]);

        return redirect()
            ->back()
            ->with('success', ucfirst($request->action) . ' users successfully.');
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
