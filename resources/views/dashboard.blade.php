<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @include('partials.head')
</head>

<body>
    <div class="main-container">
        @include('partials.sidebar')

        <!-- Main Content -->
        <div class="main-content">
            <div class="content-wrapper">
                <!-- Header -->
                @include('partials.navbar')

                <!-- Page Title and Date -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Welcome Back {{ auth()->user()->name }}!</h1>
                    <div class="text-muted d-none d-sm-block">{{ now()->format('F d, Y h:i A') }}</div>
                </div>

                <!-- Summary Cards -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card p-4">
                            <div class="h2 text-orange">{{ $totalProperties }}</div>
                            <div>Total Properties</div>
                        </div>
                    </div>
                    {{-- <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card p-4">
                            <div class="h2 text-orange">{{ $totalActiveUsersCount }}</div>
                            <div>Active users</div>
                        </div>
                    </div> --}}
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card p-4">
                            <div class="h2 text-orange">{{ $totalIssuesCount }}</div>
                            <div>Open Issues</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card p-4">
                            <div class="h2 text-orange">{{ $totalHouseOwners }}</div>
                            <div>Total House Owners</div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card p-4">
                            <div class="h2 text-orange">{{ $totalHousePlans }}</div>
                            <div>Total House Plans</div>
                        </div>
                    </div>
                    {{-- <div class="col-12 col-sm-6 col-lg-3">
                        <div class="card summary-card p-4">
                            <div class="h2 text-orange">0</div>
                            <div>Messages</div>
                        </div>
                    </div> --}}
                </div>

                <!-- Summary Cards -->
                {{-- <div class="row g-4 mb-4">

                    <!-- Bar Chart -->
                    <div class="col-12 col-lg-3">
                        <div class="card p-3">
                            <h5 class="mb-3">Monthly Issues (Bar Chart)</h5>
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>

                    <!-- Line Chart -->
                    <div class="col-12 col-lg-3">
                        <div class="card p-3">
                            <h5 class="mb-3">Issues Trend (Line Chart)</h5>
                            <canvas id="lineChart"></canvas>
                        </div>
                    </div>

                    <!-- Pie Chart -->
                    <div class="col-12 col-lg-3">
                        <div class="card p-3">
                            <h5 class="mb-3">Issues Distribution (Pie Chart)</h5>
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>

                    <!-- Doughnut Chart -->
                    <div class="col-12 col-lg-3">
                        <div class="card p-3">
                            <h5 class="mb-3">User Stats (Doughnut Chart)</h5>
                            <canvas id="doughnutChart"></canvas>
                        </div>
                    </div>

                </div> --}}


                <!-- Recent Activity & Recent Issues -->
                <div class="row g-4 mb-4">
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Recent Activity</h5>

                                <div class="chart-container d-flex justify-content-around align-items-end">
                                    @foreach ($activityData as $activity)
                                        <div class="chart-bar-wrapper text-center">
                                            <div class="chart-bar" style="height: {{ $activity['percentage'] }}%;">
                                            </div>

                                            <div class="chart-bar-label">
                                                {{ $activity['count'] }}
                                            </div>

                                            <small class="text-muted">
                                                {{ $activity['percentage'] }}%
                                            </small>

                                            <div class="mt-1 fw-semibold">
                                                {{ $activity['label'] }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-header card-header-orange">
                                <h5 class="card-title text-white mb-0">Recent Issues</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="text-white">
                                            <tr>
                                                <th>Issue</th>
                                                <th>Location</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($recentIssues as $issue)
                                                <tr>
                                                    <td>{{ $issue->issue_title ?? 'N/A' }}</td>

                                                    <td>{{ $issue->issue_details ?? 'N/A' }}</td>

                                                    <td>
                                                        <span
                                                            class="badge
                    @if ($issue->status === 'Open') bg-warning
                    @elseif($issue->status === 'Resolved') bg-success
                    @elseif($issue->status === 'Closed') bg-secondary
                    @else bg-light text-dark @endif">
                                                            {{ ucfirst($issue->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center text-muted">
                                                        No issue reports found
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Messages & Action Shortcuts -->
                <div class="row g-4">
                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Messages</h5>
                                <div class="message-item">
                                    <img src="https://placehold.co/50x50/f89d31/fff?text=JD" alt="John Doe">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">John Doe</div>
                                        <small class="text-muted">Thanks a lot, Andrew and...</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">5 min ago</small>
                                        <span class="badge rounded-pill bg-danger">2</span>
                                    </div>
                                </div>
                                <div class="message-item">
                                    <img src="https://placehold.co/50x50/f89d31/fff?text=JD" alt="John Doe">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">John Doe</div>
                                        <small class="text-muted">Thanks a lot, Andrew and...</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">5 min ago</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Action Shortcuts</h5>
                                <a href="{{ route('admin.properties.create') }}"><button class="btn action-shortcut-btn">Add Property</button></a>
                                <a href="{{ route('admin.issue_report.index') }}"> <button class="btn action-shortcut-btn">View All issues</button></a>
                                <a href="{{ route('admin.appliances.create') }}"> <button class="btn action-shortcut-btn">Manage appliances</button></a>
                                <a href="{{ route('admin.compliance_certificates.create') }}"><button class="btn action-shortcut-btn">Upload certification</button></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials/scripts')
</body>
<script>
    // Laravel data passed from controller
    const monthlyIssues = @json(array_values($monthlyIssues));
    const issueMonths = @json(array_keys($monthlyIssues));

    // Convert month numbers to names
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const labels = issueMonths.map(m => monthNames[m - 1]);

    /* ----------------- BAR CHART ------------------ */
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: "Issues",
                data: monthlyIssues,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
            }]
        }
    });

    /* ---------------- LINE CHART ------------------ */
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: "Issues",
                data: monthlyIssues,
                borderColor: 'rgba(255, 99, 132, 0.8)',
                borderWidth: 3,
                fill: false
            }]
        }
    });

    /* ---------------- PIE CHART ------------------ */
    new Chart(document.getElementById('pieChart'), {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: monthlyIssues,
                backgroundColor: [
                    '#ff6384',
                    '#36a2eb',
                    '#ffcd56',
                    '#4bc0c0',
                    '#9966ff',
                    '#ff9f40'
                ]
            }]
        }
    });

    /* -------------- DOUGHNUT CHART --------------- */
    new Chart(document.getElementById('doughnutChart'), {
        type: 'doughnut',
        data: {
            labels: ["Users", "Properties", "Issues"],
            datasets: [{
                data: [
                    {{ $totalActiveUsers }},
                    {{ $totalProperties }},
                    {{ $totalIssues }}
                ],
                backgroundColor: [
                    "#36a2eb",
                    "#ffcd56",
                    "#ff6384"
                ]
            }]
        }
    });
</script>

<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000"
    };

    @if (session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if (session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>

</html>
