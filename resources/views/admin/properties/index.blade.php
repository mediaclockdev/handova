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
                    <h1 class="h3 mb-0">Properties</h1>
                    <a href="{{ url('admin/properties/create') }}" class="btn add-property-btn">Add Property</a>
                </div>

                <div class="card properties-table mb-4">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0" id="propertiesTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>House Type</th>
                                    <th>House Plan Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse($properties as $property)
                                <tr>
                                    <td>{{ $property->property_title }}</td>
                                    <td>{{ $property->address }}</td>
                                    <td>{{ $property->property_type }}</td>
                                    <td>{{ $property->house_plan_name }}</td>
                                    <td>
                                        <a href="{{ route('admin.properties.edit', $property) }}"
                                            class="btn btn-sm btn-warning"><i
                                                class="bi bi-pencil-square action-icon"></i></a>
                                        <form action="{{ route('admin.properties.destroy', $property) }}"
                                            method="POST" style="display:inline-block">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this property?')"><i
                                                    class="bi bi-trash3 action-icon"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4">No properties found.</td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pagination_div">
                    <div class="pagination_showing">
                        Showing {{ $properties->firstItem() }} to {{ $properties->lastItem() }}
                        of {{ $properties->total() }} entries
                    </div>
                    <div class="pagination_count">
                        {{ $properties->links('pagination::bootstrap-5') }}
                    </div>
                </div>

                {{-- <div class="row g-4">
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
                </div> --}}
            </div>
        </div>
    </div>
    @include('partials/scripts')
</body>
<script>
    @if(session('success'))
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000"
    };
    toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
    toastr.error("{{ session('error') }}");
    @endif
</script>

</html>