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
                    <a href="{{ route('admin.compliance_certificates.create') }}" class="btn add-property-btn">Add
                        New</a>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Filter by Property</label>
                        <select id="propertySelect" class="form-select">
                            <option value="">All Properties</option>
                            @foreach (\App\Models\Property::where('user_id', Auth::id())->get() as $property)
                            <option value="{{ $property->id }}">
                                {{ $property->property_title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="card properties-table mb-4">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0" id="certificatesTable">
                            <thead>
                                <tr>
                                    <th>Certification Title</th>
                                    <th>Compliance Type</th>
                                    <th>Certificate No</th>
                                    <th>Issuing Authority</th>
                                    <th>Date of Issue</th>
                                    <th>Expiry Date</th>
                                    <th>Property</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="certificatesTableBody">
                                @forelse($certificates as $c)
                                <tr>
                                    <td>{{ $c->certification_title }}</td>
                                    <td>{{ $c->compliance_type }}</td>
                                    <td>{{ $c->certificate_number }}</td>
                                    <td>{{ $c->issuing_authority }}</td>
                                    <td>{{ $c->date_of_issue?->format('Y-m-d') }}</td>
                                    <td>{{ $c->expiry_date?->format('Y-m-d') }}</td>
                                    <td>{{ $c->property?->property_title }}</td>
                                    <td>
                                        <a href="{{ route('admin.compliance_certificates.edit', $c->id) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('admin.compliance_certificates.destroy', $c->id) }}"
                                            method="POST" style="display:inline-block">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this certificate?')">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No compliance certificates found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pagination_div">
                    <div class="pagination_showing">
                        Showing {{ $certificates->firstItem() }} to {{ $certificates->lastItem() }}
                        of {{ $certificates->total() }} entries
                    </div>
                    <div class="pagination_count">
                        {{ $certificates->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                {{ $certificates->links() }}
            </div>
        </div>
    </div>
    @include('partials/scripts')
</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        const certificatesTableBody = document.getElementById("certificatesTableBody");

        document.getElementById("propertySelect").addEventListener("change", function() {
            const propertyId = this.value;

            if (!propertyId) {
                location.reload(); // reset to paginated list
                return;
            }

            fetch(`/admin/property/${propertyId}/certificates`)
                .then(res => res.json())
                .then(data => {
                    let tableHTML = "";

                    if (data.length === 0) {
                        tableHTML = `
                        <tr>
                            <td colspan="8" class="text-center">
                                No certificates found for selected property.
                            </td>
                        </tr>`;
                    } else {
                        data.forEach(c => {
                            tableHTML += `
                        <tr>
                            <td>${c.certification_title}</td>
                            <td>${c.compliance_type}</td>
                            <td>${c.certificate_number}</td>
                            <td>${c.issuing_authority}</td>
                            <td>${c.date_of_issue ?? ''}</td>
                            <td>${c.expiry_date ?? ''}</td>
                            <td>${c.property_title ?? ''}</td>
                            <td>
                                <a href="/admin/compliance_certificates/${c.id}/edit"
                                   class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="/admin/compliance_certificates/${c.id}"
                                      method="POST"
                                      style="display:inline-block">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this certificate?')">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>`;
                        });
                    }

                    certificatesTableBody.innerHTML = tableHTML;
                });
        });

    });
</script>


</html>