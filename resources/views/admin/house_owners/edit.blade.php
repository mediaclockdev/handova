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
                <form action="{{ route('admin.house_owners.update', $owner->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.house_owners.form', ['owner' => $owner])

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-clear-all" onclick="clearForm()">Clear All</button>
                        <button type="submit" class="btn btn-add-property">
                            Update House Owner
                        </button>
                    </div>
                </form>

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