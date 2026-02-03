<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.superadminhead') {{-- Bootstrap CSS included --}}
</head>

<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <div class="card shadow-sm border-0 rounded-3">

                {{-- Header --}}
                <div class="card-header bg-white border-bottom px-4 py-3">
                    <h4 class="mb-1 fw-semibold">Add Builder</h4>
                    <small class="text-muted">
                        Manage builder account details and credentials.
                    </small>
                </div>

                {{-- Form --}}
                <div class="card-body px-4 py-4">
                    <form action="{{ route('superadmin.builders.store') }}" method="POST">
                        @csrf

                        @include('superadmin.builders.form', [
                            'buttonText' => 'Create Builder'
                        ])
                    </form>
                </div>

            </div>

        </div>
    </div>
</div>

</body>
</html>
