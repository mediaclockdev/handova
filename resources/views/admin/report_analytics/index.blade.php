<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    @include('partials.head')
</head>

<body>
    <div class="main-container">
        @include('partials.sidebar')

        <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

        <div class="main-content">
            <div class="content-wrapper">
                @include('partials.navbar')

                <div class="properties-header d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Report & Analytics</h1>
                    <!-- <a href="{{ route('admin.issue_report.create') }}" class="btn add-property-btn">Add New</a> -->
                </div>

                <div class="card properties-table mb-4">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0" id="propertiesTable">
                            <thead>
                                <tr>
                                    <th>House Owner</th>
                                    <th>House Address</th>
                                    <th>Issue details</th>
                                    <th>Issue Category</th>
                                    <th>Issue Number</th>
                                    <th>Location in house</th>
                                    <th>Urgency Level</th>
                                    <th>Service Provider</th>
                                    <th>Reported Date</th>
                                    <th>Issue Resolved Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($issueReports as $report)
                                    <tr>
                                        <td>{{ $report->property?->houseOwner?->first_name }}
                                            {{ $report->property?->houseOwner?->last_name }}</td>
                                        <td>{{ $report->property->address }}</td>
                                        <td>{{ $report->issue_details }}</td>
                                        <td>{{ $report->assignedServiceProvider->service_specialisation }}</td>
                                        <td>{{ $report->issue_number }}</td>
                                        <td>Ground Floor</td>
                                        <td>{{ $report->issue_urgency_level }}</td>
                                        <td>{{ $report->assignedServiceProvider->first_name }}
                                            {{ $report->assignedServiceProvider->last_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($report->reported_date)->format('M d, Y') }}</td>
                                        <td>29th May 2025</td>
                                        <td>{{ $report->issue_status }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center">No Reports found.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>

                <!-- <div class="row g-4">
                    <div class="col-12 col-lg-12">
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
                </div> -->
            </div>
        </div>
    </div>
    @include('partials/scripts')
</body>
<script>
    @if (session('success'))
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000"
        };
        toastr.success("{{ session('success') }}");
    @endif

    @if (session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>

</html>
