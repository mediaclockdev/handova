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
                    <a href="{{ route('admin.service_provider.create') }}" class="btn add-property-btn">Add New</a>
                </div>

                <div class="card properties-table mb-4">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0" id="propertiesTable">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>Contact Person</th>
                                    <th>Services</th>
                                    <th>Email Address</th>
                                    <th>Phone Number</th>
                                    <th>Address</th>
                                    <th>Coverage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($serviceproviders as $serviceprovider)
                                    {{-- @php dd($serviceprovider->service_specialisation) @endphp --}}
                                    <tr>
                                        <td>{{ $serviceprovider->company_name ?? 'N/A' }}</td>
                                        <td>{{ $serviceprovider->first_name }} {{ $serviceprovider->last_name ?? 'N/A' }}</td>
                                        <td>{{ $serviceprovider->specialization?->specialization ?? 'N/A' }}</td>
                                        <td>{{ $serviceprovider->email ?? 'N/A' }}</td>
                                        <td>{{ $serviceprovider->phone ?? 'N/A' }}</td>
                                        <td>{{ $serviceprovider->address ?? 'N/A' }}</td>
                                        <td>{{ $serviceprovider->coverage ?? 'N/A' }}KM</td>

                                        <td>
                                            <a href="{{ route('admin.service_provider.edit', $serviceprovider->id) }}"
                                                class="btn btn-sm btn-warning"><i
                                                    class="bi bi-pencil-square action-icon"></i></a>
                                            <form
                                                action="{{ route('admin.service_provider.destroy', $serviceprovider->id) }}"
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
                                        <td colspan="12" class="text-center">No service providers found.</td>
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
