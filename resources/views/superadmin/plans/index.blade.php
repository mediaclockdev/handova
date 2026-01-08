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
                                <p class="text-neutral-600">Manage subscription plans for your platform</p>
                            </div>
                            <a href="{{ route('superadmin.plans.create')}}" class="text-decoration-none flex items-center px-4 py-2 bg-neutral-900 text-white rounded-md hover:bg-neutral-800">
                                <i class="mr-2" data-fa-i2svg="">
                                    <svg class="svg-inline--fa fa-plus" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="">
                                        <path fill="currentColor" d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z"></path>
                                    </svg>
                                </i>
                                Create Plan
                            </a>
                        </div>
                    </div>
                    <div id="existing-plans" class="mt-8 bg-white rounded-lg border border-neutral-200">
                        <div class="p-6 border-b border-neutral-200">
                            <h3 class="text-lg text-neutral-900">Existing Plans</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                @foreach($plans as $plan)
                                <div class="border border-neutral-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="text-md text-neutral-900">{{ $plan->plan_name }}</h4>
                                        <span class="px-2 py-1 bg-neutral-100 text-neutral-700 text-xs rounded">{{ ucfirst($plan->plan_type) }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <span class="text-2xl text-neutral-900">${{ $plan->plan_price }}</span>
                                        <span class="text-neutral-600">/{{ $plan->plan_type }}</span>
                                    </div>
                                    <ul class="space-y-1 text-sm text-neutral-600 mb-4">
                                        <li>{{ $plan->plan_allowed_listing }} Listings</li>
                                        <li>{{ $plan->plan_featured_properties }} Featured Properties</li>
                                        <li>{{ $plan->plan_photo_upload_limit }} Photos per listing</li>
                                    </ul>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('superadmin.plans.edit', $plan->id) }}"
                                            class="flex-1 px-3 py-1 border border-neutral-300 rounded text-sm hover:bg-neutral-50 plan_edit_button">
                                            Edit
                                        </a>
                                        <button class="flex-1 px-3 py-1 bg-neutral-900 text-white rounded text-sm hover:bg-neutral-800">View</button>
                                        <form action="{{ route('superadmin.plans.destroy', $plan->id) }}" method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button class="flex-1 px-3 py-1 border border-neutral-300 rounded text-sm hover:bg-neutral-50 plan_edit_button" onclick="return confirm('Delete this plan?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
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