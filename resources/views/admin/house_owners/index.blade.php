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
                    <h1 class="h3 mb-0">House Owner</h1>
                    <a href="{{ route('admin.house_owners.create') }}" class="btn add-property-btn">Add New</a>
                </div>

                <div class="card properties-table mb-4">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Property</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Email Address</th>
                                    <th>Phone Number</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="ownersTableBody">
                                @forelse($houseOwners as $owner)
                                <tr>
                                    <td>{{ $owner->property?->property_title }}</td>
                                    <td>{{ $owner->house_owner_id }}</td>
                                    <td>{{ $owner->first_name }}</td>
                                    <td>{{ $owner->email_address }}</td>
                                    <td>{{ $owner->phone_number }}</td>
                                    <td>
                                        <a href="{{ route('admin.house_owners.edit', $owner->id) }}"
                                            class="btn btn-sm btn-warning"><i
                                                class="bi bi-pencil-square action-icon"></i></a>
                                        <form action="{{ route('admin.house_owners.destroy', $owner->id) }}"
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
                                    <td colspan="4">No house owner found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pagination_div">
                    <div class="pagination_showing">
                        Showing {{ $houseOwners->firstItem() }} to {{ $houseOwners->lastItem() }}
                        of {{ $houseOwners->total() }} entries
                    </div>
                    <div class="pagination_count">
                        {{ $houseOwners->links('pagination::bootstrap-5') }}
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

    <!-- Property Selection Modal -->
    {{-- <div class="modal fade show" id="propertyPopup" tabindex="-1" aria-hidden="true" style="display:block;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Select Property</h5>
                </div>

                <div class="modal-body">
                    <label class="form-label">Choose Property</label>
                    <select id="propertySelect" class="form-control">
                        <option value="">-- Select Property --</option>
                        @foreach ($properties as $property)
                            <option value="{{ $property->id }}">
    {{ $property->property_title }}
    </option>
    @endforeach
    </select>
    </div>

    <div class="modal-footer">
        <button id="closePopup" class="btn add-property-btn">Close</button>
    </div>
    </div>
    </div>
    </div>

    <style>
        .modal.fade.show {
            display: block;
            background: rgba(0, 0, 0, 0.6);
        }
    </style> --}}

    @include('partials/scripts')
</body>
<script>
    // document.addEventListener("DOMContentLoaded", function() {

    //     document.getElementById("closePopup").addEventListener("click", function() {
    //         document.getElementById("propertyPopup").style.display = "none";
    //     });

    //     const ownersTableBody = document.getElementById("ownersTableBody");

    //     document.getElementById("propertySelect").addEventListener("change", function() {
    //         const propertyId = this.value;

    //         if (!propertyId) {
    //             location.reload();
    //             return;
    //         }

    //         fetch(`/admin/property/${propertyId}/owners`)
    //             .then(res => res.json())
    //             .then(data => {
    //                 let tableHTML = "";

    //                 if (data.length === 0) {
    //                     tableHTML = `
    //                     <tr>
    //                         <td colspan="6">No house owner found for selected property.</td>
    //                     </tr>`;
    //                 } else {
    //                     data.forEach(owner => {
    //                         tableHTML += `
    //                     <tr>
    //                         <td>${owner.property_title ?? ""}</td>
    //                         <td>${owner.house_owner_id}</td>
    //                         <td>${owner.first_name}</td>
    //                         <td>${owner.email_address}</td>
    //                         <td>${owner.phone_number}</td>
    //                         <td>
    //                             <a href="/admin/house_owners/${owner.id}/edit" class="btn btn-sm btn-warning">
    //                                 <i class="bi bi-pencil-square action-icon"></i>
    //                             </a>
    //                             <form action="/admin/house_owners/${owner.id}" method="POST" style="display:inline-block">
    //                                 <input type="hidden" name="_token" value="{{ csrf_token() }}">
    //                                 <input type="hidden" name="_method" value="DELETE">
    //                                 <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this owner?')">
    //                                     <i class="bi bi-trash3 action-icon"></i>
    //                                 </button>
    //                             </form>
    //                         </td>
    //                     </tr>`;
    //                     });
    //                 }

    //                 // Only update tbody, keep thead intact
    //                 ownersTableBody.innerHTML = tableHTML;
    //             });
    //     });

    // });
</script>

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