<!DOCTYPE html>
<html lang="en">

<head>
    <title>Issue Report</title>
    @include('partials.head')
</head>

<body>
    <div class="main-container">

        @include('partials.sidebar')
        <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

        <div class="main-content">
            <div class="content-wrapper">

                @include('partials.navbar')

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-0">Issue Report</h1>
                    <a href="{{ route('admin.issue_report.create') }}" class="btn add-property-btn">Add New</a>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Filter by Property</label>
                        <select id="filterProperty" class="form-select">
                            <option value="">All Properties</option>
                            @foreach ($properties as $property)
                                <option value="{{ $property->id }}">{{ $property->property_title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card properties-table mb-4">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0" id="propertiesTable">
                            <thead>
                                <tr>
                                    <th>Issue Number</th>
                                    <th>Issue Details</th>
                                    <th>Issue Reported By</th>
                                    <th>Reported Date</th>
                                    <th>Assigned</th>
                                    <th>Customer Contact</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody id="issuesTableBody">
                                @forelse($issueReports as $issueReport)
                                    <tr>
                                        <td>{{ $issueReport->issue_number }}</td>
                                        <td>{{ $issueReport->issue_details }}</td>
                                        <td>{{ $issueReport->reporter->name }}</td>
                                        <td>{{ $issueReport->reported_date->format('F jS, Y') }}</td>
                                        <td>{{ $issueReport->assigned_to_service_provider === 'yes' ? 'Assigned' : 'Not Assigned' }}
                                        </td>
                                        <td>{{ $issueReport->customer_contact }}</td>
                                        <td>{{ $issueReport->issue_status }}</td>
                                        <td>
                                            <a href="{{ route('admin.issue_report.edit', $issueReport->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square action-icon"></i>
                                            </a>

                                            <form action="{{ route('admin.issue_report.destroy', $issueReport->id) }}"
                                                method="POST" style="display:inline-block">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this Report Issue?')">
                                                    <i class="bi bi-trash3 action-icon"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No issue reports found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pagination_div">
                    <div class="pagination_showing">
                        Showing {{ $issueReports->firstItem() }} to {{ $issueReports->lastItem() }}
                        of {{ $issueReports->total() }} entries
                    </div>
                    <div class="pagination_count">
                        {{ $issueReports->links('pagination::bootstrap-5') }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('partials.scripts')

</body>


{{-- FILTER SCRIPT --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const issuesTableBody = document.getElementById("issuesTableBody");

        document.getElementById("filterProperty").addEventListener("change", function() {

            const propertyId = this.value;

            if (!propertyId) {
                location.reload(); // Reload full listing
                return;
            }

            fetch(`/admin/property/${propertyId}/issues`)
                .then(res => res.json())
                .then(data => {

                    let html = "";

                    if (data.length === 0) {
                        html = `
                        <tr>
                            <td colspan="7" class="text-center">No issue report for this property.</td>
                        </tr>
                    `;
                    } else {
                        data.forEach(issue => {

                            const assigned = issue.assigned_to_service_provider === "yes" ?
                                "Assigned" :
                                "Not Assigned";

                            html += `
                        <tr>
                            <td>${issue.issue_number}</td>
                            <td>${issue.issue_details}</td>
                            <td>${issue.reporter_name}</td>
                            <td>${new Date(issue.reported_date).toLocaleDateString()}</td>
                            <td>${assigned}</td>
                            <td>${issue.issue_status}</td>

                            <td>
                                <a href="/admin/issue_report/${issue.id}/edit" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square action-icon"></i>
                                </a>

                                <form action="/admin/issue_report/${issue.id}" 
                                      method="POST" style="display:inline-block">
                                    
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">

                                    <button class="btn btn-sm btn-danger"
                                            onclick="return confirm('Delete this Report Issue?')">
                                        <i class="bi bi-trash3 action-icon"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        `;
                        });
                    }

                    issuesTableBody.innerHTML = html;
                });
        });

    });
</script>


{{-- Toastr --}}
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
