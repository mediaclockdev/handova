<!DOCTYPE html>
<html lang="en">

<head>
    <title>Dashboard</title>
    @include('partials.head')
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                <div class="card shadow-sm border-0 rounded-3">

                    {{-- Header --}}
                    <div class="card-header bg-white border-bottom px-4 py-3 superadmin_back_div">
                        <div>
                            <a href="{{ route('superadmin.owners.index') }}" class="btn btn-dark px-4">
                                Back
                            </a>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-semibold">Edit House Owner Details</h4>
                        </div>
                    </div>

                    {{-- Form --}}
                    <div class="card-body px-4 py-4">
                        <form action="{{ route('superadmin.owners.update', $owner->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('superadmin.owners.form', ['owner' => $owner])

                            <div class="d-flex justify-content-between align-items-center">
                                <button type="reset" class="btn btn-clear-all" onclick="clearForm()">Clear
                                    All</button>
                                <button type="submit" class="btn btn-add-property">
                                    Update Tenants
                                </button>
                            </div>
                        </form>

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
