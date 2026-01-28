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

                <form action="{{ route('admin.profile.update', $user->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.profile.form', ['user' => $user])

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-clear-all" onclick="clearForm()">Clear All</button>
                        <button type="submit" class="btn btn-add-property">
                            Update Profile
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @include('partials/scripts')
</body>
<script>
    function clearForm() {
        // reset form fields
        const form = document.querySelector('form[action="{{ route('admin.profile.update', $user->id) }}"]');
        if (!form) return;
        form.reset();
        // reset image preview to placeholder or existing profile picture
        document.getElementById('profilePreview').src =
            "{{ isset($user->profile_picture) ? asset($user->profile_picture) : 'https://via.placeholder.com/150' }}";
    }

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
