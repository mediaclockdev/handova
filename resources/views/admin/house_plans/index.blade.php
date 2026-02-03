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
                    <h1 class="h3 mb-0">{{ $formTitle }}</h1>
                    <a href="{{ route('admin.house_plans.create') }}" class="btn add-property-btn">Add New</a>
                </div>

                <div class="card properties-table mb-4">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0" id="propertiesTable">
                            <thead>
                                <tr>
                                    <th>Plan Name</th>
                                    <th>Storey</th>
                                    <th>Pricing</th>
                                    <th>House Area</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="housePlansTableBody">
                                @forelse($houseplans as $plan)
                                    <tr>
                                        <td>{{ $plan->plan_name }}</td>
                                        <td>{{ $plan->display_location }}</td>
                                        <td>${{ $plan->pricing }}</td>
                                        <td>{{ $plan->house_area }} SQ FT</td>
                                        <td>{{ $plan->created_at }}</td>

                                        <td>
                                            <a href="{{ route('admin.house_plans.edit', $plan->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('admin.house_plans.destroy', $plan->id) }}"
                                                method="POST" style="display:inline-block">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this plan ?')">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No house plans found.</td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>

                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pagination_div">
                    <div class="pagination_showing">
                        Showing {{ $houseplans->firstItem() }} to {{ $houseplans->lastItem() }}
                        of {{ $houseplans->total() }} entries
                    </div>
                    <div class="pagination_count">
                        {{ $houseplans->links('pagination::bootstrap-5') }}
                    </div>
                </div>
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
