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
                <div class="header">
                    <div class="d-flex align-items-center w-100">
                        <button class="btn btn-outline-secondary d-md-none me-3" id="sidebar-toggle-btn">
                            <i class="bi bi-list"></i>
                        </button>

                        <form class="d-flex me-3 search-form">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search..." aria-label="Search">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                            </div>
                        </form>
                    </div>

                    <div class="d-flex align-items-center flex-shrink-0">
                        <div class="dropdown notifications-dropdown">
                            <a href="#" class="icon-btn dropdown-toggle" role="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="bi bi-bell"></i>
                                <span class="badge rounded-pill bg-danger">3</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <h6 class="dropdown-header">New Notifications</h6>
                                </li>
                                <li><a class="dropdown-item" href="#">
                                        <i class="bi bi-info-circle"></i> You have a new issue report
                                    </a></li>
                                <li><a class="dropdown-item" href="#">
                                        <i class="bi bi-check-circle"></i> A service request has been resolved
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-center" href="#">View all notifications</a></li>
                            </ul>
                        </div>

                        <div class="dropdown ms-3">
                            <div class="profile-info d-none d-sm-flex dropdown-toggle" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="https://placehold.co/40x40/f89d31/fff?text=MM" alt="Michelle Munro">
                                <div>
                                    <div class="font-weight-bold">{{ auth()->user()->name }}</div>
                                    <div class="text-sm">ID: 1234567</div>
                                </div>
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">
                                        <i class="bi bi-person-circle"></i> My Profile
                                    </a></li>
                                <li><a class="dropdown-item" href="#">
                                        <i class="bi bi-gear"></i> Settings
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="#">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <form action="{{ route('admin.service_provider.update', $serviceProvider->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('admin.service_provider.form', ['serviceProvider' => $serviceProvider])

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="reset" class="btn btn-clear-all" onclick="clearForm()">Clear All</button>
                        <button type="submit" class="btn btn-add-property">
                            Update Service provider
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @include('partials/scripts')
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBcZaByAZLe7qQZAYhKWtc2O9Bn22PAD2E&libraries=places&callback=initAutocomplete"
        async defer></script>
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
