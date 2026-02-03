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

                    <div class="card-header bg-white border-bottom px-4 py-3 superadmin_back_div">
                        <div>
                            <a href="{{ route('superadmin.builders.index') }}" class="btn btn-dark px-4">
                                Back
                            </a>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-semibold">Add Service Specialization</h4>
                        </div>
                    </div>


                    {{-- Form --}}
                    <div class="card-body px-4 py-4">
                        <form action="{{ route('superadmin.specialization.store') }}" method="POST">
                            @csrf

                            @include('superadmin.specialization.form', [
                                'buttonText' => 'Create Service Specialization',
                            ])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
