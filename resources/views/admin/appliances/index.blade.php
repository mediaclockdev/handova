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
                    <a href="{{ route('admin.appliances.create') }}" class="btn add-property-btn">Add New</a>
                </div>

                <div class="card properties-table mb-4">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0" id="propertiesTable">
                            <thead>
                                <tr>
                                    <th>Appliance Name</th>
                                    <th>Product Details</th>
                                    <th>Brand Name</th>
                                    <th>Model</th>
                                    <th>Warranty Information</th>
                                    <th>Manuals</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($appliances as $appliance)
                                    <tr>
                                        <td>{{ $appliance->appliance_name }}</td>
                                        <td>{{ $appliance->product_details }}</td>
                                        <td>{{ $appliance->brand_name }}</td>
                                        <td>{{ $appliance->model }}</td>
                                        <td>{{ $appliance->warranty_information }}</td>
                                        @php
                                            $manuals = json_decode($appliance->manuals, true);
                                        @endphp
                                        <td>
                                            @if (!empty($manuals) && count($manuals))
                                                @foreach ($manuals as $file)
                                                    <a class="view_file" href="{{ asset('storage/' . $file) }}" target="_blank">
                                                        View File
                                                    </a><br>
                                                @endforeach
                                            @else
                                                No File
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.appliances.edit', $appliance->id) }}"
                                                class="btn btn-sm btn-warning"><i
                                                    class="bi bi-pencil-square action-icon"></i></a>
                                            <form action="{{ route('admin.appliances.destroy', $appliance->id) }}"
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
                                        <td colspan="4">No Appliances found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pagination_div">
                    <div class="pagination_showing">
                        Showing {{ $appliances->firstItem() }} to {{ $appliances->lastItem() }}
                        of {{ $appliances->total() }} entries
                    </div>
                    <div class="pagination_count">
                        {{ $appliances->links('pagination::bootstrap-5') }}
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
