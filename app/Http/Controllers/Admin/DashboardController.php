<?php

namespace App\Http\Controllers\Admin;

use \Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\ServiceProvider;
use App\Models\IssueReport;
use \App\Models\User;
use App\Models\HouseOwner;
use App\Models\HousePlan;

class DashboardController extends Controller
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
        $totalPropertiesCount = Property::count();
        $totalProperties = Property::where('user_id', Auth::id())->count();
        $totalIssuesCount = IssueReport::count();
        $totalActiveUsersCount = User::count();
        $totalIssues = IssueReport::count();
        $totalActiveUsers = User::count();
        $totalHouseOwners = HouseOwner::where('user_id', Auth::id())->count();
        $totalHousePlans = HousePlan::where('user_id', Auth::id())->count();

        // Example Graph Data (You can replace with real DB data)
        $monthlyIssues = IssueReport::selectRaw('MONTH(created_at) as month, count(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $recentActivities = [
            'Properties'    => Property::where('user_id', Auth::id())->count(),
            'Issues' => IssueReport::join('properties', 'issue_reports.properties_id', '=', 'properties.id')->where('properties.user_id', Auth::id())->count(),
            'House Owners'  => HouseOwner::where('user_id', Auth::id())->count(),
            'House Plans'   => HousePlan::where('user_id', Auth::id())->count(),
        ];

        $totalActivity = array_sum($recentActivities);

        $activityData = [];
        foreach ($recentActivities as $label => $count) {
            $activityData[] = [
                'label' => $label,
                'count' => $count,
                'percentage' => $totalActivity > 0
                    ? round(($count / $totalActivity) * 100, 1)
                    : 0
            ];
        }
        $recentIssues = IssueReport::latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalProperties',
            'totalIssues',
            'totalActiveUsers',
            'monthlyIssues',
            'totalPropertiesCount',
            'totalIssuesCount',
            'totalActiveUsersCount',
            'totalHouseOwners',
            'totalHousePlans',
            'activityData',
            'recentIssues'
        ));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
