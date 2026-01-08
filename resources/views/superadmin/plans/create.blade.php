<!DOCTYPE html>
<html lang="en" class="superadminlogin">

<head>
    @include('partials.superadminhead')
</head>

<body>

    <header id="header" class="bg-white border-b border-neutral-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-neutral-800 rounded-lg flex items-center justify-center mr-3">
                        <i class="text-white text-sm" data-fa-i2svg=""><svg class="svg-inline--fa fa-shield-halved" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="shield-halved" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                <path fill="currentColor" d="M256 0c4.6 0 9.2 1 13.4 2.9L457.7 82.8c22 9.3 38.4 31 38.3 57.2c-.5 99.2-41.3 280.7-213.6 363.2c-16.7 8-36.1 8-52.8 0C57.3 420.7 16.5 239.2 16 140c-.1-26.2 16.3-47.9 38.3-57.2L242.7 2.9C246.8 1 251.4 0 256 0zm0 66.8V444.8C394 378 431.1 230.1 432 141.4L256 66.8l0 0z"></path>
                            </svg></i>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <img src="https://api.dicebear.com/7.x/notionists/svg?scale=200&amp;seed=123" alt="Admin" class="w-8 h-8 rounded-full">
                    <span class="text-sm text-neutral-700">{{ Auth::user()->name }}</span>
                </div>
            </div>
        </div>
    </header>

    <div class="d-flex flex-column flex-md-row">
    @include('superadmin.partials.superadminsidebar')

        <div class="main-content flex-grow-1">
            <div class="">
                <div class="dashboard-content">
                    <div id="page-header" class="mb-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl text-neutral-900 mb-2">Subscription Plans</h2>
                                <p class="text-neutral-600">Create and manage subscription plans for your platform</p>
                            </div>
                        </div>
                    </div>

                    <div id="subscription-form" class="bg-white rounded-lg border border-neutral-200 p-6">
                        <div id="form-header" class="mb-6">
                            <h3 class="text-lg text-neutral-900 mb-1">Create New Subscription Plan</h3>
                            <p class="text-sm text-neutral-600">Define plan details, pricing, and features</p>
                        </div>

                        <form action="{{ route('superadmin.plans.store') }}" method="POST" class="space-y-6">
                            @csrf
                            @include('superadmin.plans.form')
                            <button type="button" class="px-4 py-2 border border-neutral-300 rounded-md text-neutral-700 hover:bg-neutral-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-neutral-900 text-white rounded-md hover:bg-neutral-800">
                                Create Plan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.superadminscripts')
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