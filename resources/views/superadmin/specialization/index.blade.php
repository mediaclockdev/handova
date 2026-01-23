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
                        <i class="text-white text-sm" data-fa-i2svg=""><svg class="svg-inline--fa fa-shield-halved"
                                aria-hidden="true" focusable="false" data-prefix="fas" data-icon="shield-halved"
                                role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                data-fa-i2svg="">
                                <path fill="currentColor"
                                    d="M256 0c4.6 0 9.2 1 13.4 2.9L457.7 82.8c22 9.3 38.4 31 38.3 57.2c-.5 99.2-41.3 280.7-213.6 363.2c-16.7 8-36.1 8-52.8 0C57.3 420.7 16.5 239.2 16 140c-.1-26.2 16.3-47.9 38.3-57.2L242.7 2.9C246.8 1 251.4 0 256 0zm0 66.8V444.8C394 378 431.1 230.1 432 141.4L256 66.8l0 0z">
                                </path>
                            </svg></i>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <img src="https://api.dicebear.com/7.x/notionists/svg?scale=200&amp;seed=123" alt="Admin"
                        class="w-8 h-8 rounded-full">
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
                        <div class="max-w-7xl mx-auto">
                            <div class="mb-8">
                                <h2 class="text-2xl text-neutral-900 mb-2">Service Specialization Management</h2>
                                <p class="text-neutral-600">View, filter, and manage all registered Service
                                    Specialization
                                </p>
                            </div>
                            <div id="search-filters" class="bg-white rounded-lg border border-neutral-200 p-6 mb-6">
                                <form method="GET" action="{{ route('superadmin.specialization.index') }}">
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                                        {{-- Search --}}
                                        <div class="md:col-span-2">
                                            <label class="block text-sm text-neutral-700 mb-2">Search Search
                                                Specialization..</label>
                                            <input type="text" name="search" value="{{ request('search') }}"
                                                placeholder="Search by specialization.."
                                                class="w-full px-4 py-2 border rounded-md" />
                                        </div>

                                        {{-- Status --}}
                                        <div>
                                            <label class="block text-sm text-neutral-700 mb-2">Status</label>
                                            <select name="status" class="w-full px-3 py-2 border rounded-md">
                                                <option value="">All Status</option>
                                                @foreach (['active', 'inactive'] as $status)
                                                    <option value="{{ $status }}" @selected(request('status') == $status)>
                                                        {{ ucfirst($status) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>


                                    <div class="flex justify-between items-center mt-4">
                                        <div class="flex space-x-2">
                                            <button
                                                class="px-4 py-2 bg-neutral-900 text-white rounded-md hover:bg-neutral-800">
                                                <i class="mr-2" data-fa-i2svg=""><svg class="svg-inline--fa fa-filter"
                                                        aria-hidden="true" focusable="false" data-prefix="fas"
                                                        data-icon="filter" role="img"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                                        data-fa-i2svg="">
                                                        <path fill="currentColor"
                                                            d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z">
                                                        </path>
                                                    </svg></i>Apply Filters
                                            </button>
                                            <a href="{{ route('superadmin.specialization.index') }}"
                                                class="px-4 py-2 border border-neutral-300 text-neutral-700 rounded-md hover:bg-neutral-50">
                                                <i class="mr-2" data-fa-i2svg=""><svg
                                                        class="svg-inline--fa fa-rotate-right" aria-hidden="true"
                                                        focusable="false" data-prefix="fas" data-icon="rotate-right"
                                                        role="img" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 512 512" data-fa-i2svg="">
                                                        <path fill="currentColor"
                                                            d="M463.5 224H472c13.3 0 24-10.7 24-24V72c0-9.7-5.8-18.5-14.8-22.2s-19.3-1.7-26.2 5.2L413.4 96.6c-87.6-86.5-228.7-86.2-315.8 1c-87.5 87.5-87.5 229.3 0 316.8s229.3 87.5 316.8 0c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0c-62.5 62.5-163.8 62.5-226.3 0s-62.5-163.8 0-226.3c62.2-62.2 162.7-62.5 225.3-1L327 183c-6.9 6.9-8.9 17.2-5.2 26.2s12.5 14.8 22.2 14.8H463.5z">
                                                        </path>
                                                    </svg></i>Reset
                                            </a>
                                            <a href="{{ route('superadmin.specialization.export', request()->query()) }}"
                                                class="px-4 py-2 border border-neutral-300 text-neutral-700 rounded-md hover:bg-neutral-50">
                                                <i class="mr-2" data-fa-i2svg=""><svg
                                                        class="svg-inline--fa fa-download" aria-hidden="true"
                                                        focusable="false" data-prefix="fas" data-icon="download"
                                                        role="img" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 512 512" data-fa-i2svg="">
                                                        <path fill="currentColor"
                                                            d="M288 32c0-17.7-14.3-32-32-32s-32 14.3-32 32V274.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3l128 128c12.5 12.5 32.8 12.5 45.3 0l128-128c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L288 274.7V32zM64 352c-35.3 0-64 28.7-64 64v32c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V416c0-35.3-28.7-64-64-64H346.5l-45.3 45.3c-25 25-65.5 25-90.5 0L165.5 352H64zm368 56a24 24 0 1 1 0 48 24 24 0 1 1 0-48z">
                                                        </path>
                                                    </svg></i>Export
                                            </a>
                                        </div>
                                </form>
                                <div class="px-0 py-2 bg-neutral-900 text-white rounded-md hover:bg-neutral-800">
                                    <a href="{{ route('superadmin.specialization.create') }}"
                                        class="px-4 py-2 bg-neutral-900 text-white rounded-md hover:bg-neutral-800">


                                        <i class="mr-2">
                                            <svg class="svg-inline--fa fa-plus" aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="plus" role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                <path fill="currentColor"
                                                    d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32V224H48c-17.7 0-32 14.3-32 32s14.3 32 32 32H192V432c0 17.7 14.3 32 32 32s32-14.3 32-32V288H400c17.7 0 32-14.3 32-32s-14.3-32-32-32H256V80z">
                                                </path>
                                            </svg>
                                        </i>
                                        Add Service Specialization
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="builders-table" class="bg-white rounded-lg border border-neutral-200 overflow-hidden mb-8">
                    <form id="bulk-action-form" method="POST"
                        action="{{ route('superadmin.specialization.bulkAction') }}">
                        @csrf
                        <input type="hidden" name="action" id="bulk-action-type">
                        <input type="hidden" name="user_ids" id="bulk-user-ids">
                    </form>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-neutral-50 border-b border-neutral-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs text-neutral-500 uppercase tracking-wider">
                                        <input type="checkbox" id="select-all" class="rounded border-neutral-300">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs text-neutral-500 uppercase tracking-wider">
                                        Service Specialization</th>
                                    <th class="px-6 py-3 text-left text-xs text-neutral-500 uppercase tracking-wider">
                                        Status</th>
                                    <th class="px-6 py-3 text-left text-xs text-neutral-500 uppercase tracking-wider">
                                        Joined</th>
                                    <th class="px-6 py-3 text-left text-xs text-neutral-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-neutral-200">
                                @foreach ($specializations as $specialization)
                                    <tr class="hover:bg-neutral-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" class="row-checkbox rounded border-neutral-300"
                                                value="{{ $specialization->id }}">
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="text-sm text-neutral-900">{{ $specialization->specialization }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="text-sm text-neutral-900">{{ ucFirst($specialization->status) }}</span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="text-sm text-neutral-900">{{ $specialization->created_at }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex space-x-2">

                                                {{-- Edit --}}
                                                <a href="{{ route('superadmin.specialization.edit', $specialization->id) }}"
                                                    class="text-neutral-700 hover:text-neutral-900">
                                                    <i data-fa-i2svg=""><svg class="svg-inline--fa fa-pen-to-square"
                                                            aria-hidden="true" focusable="false" data-prefix="fas"
                                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                                            data-icon="pen-to-square" role="img"
                                                            data-fa-i2svg="">
                                                            <path fill="currentColor"
                                                                d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z">
                                                            </path>
                                                        </svg></i>
                                                </a>

                                                {{-- Suspend --}}
                                                <form
                                                    action="{{ route('superadmin.specialization.suspend', $specialization->id) }}"
                                                    method="POST" class="suspend-form">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button type="button"
                                                        class="text-red-600 hover:text-red-800 suspend-btn">
                                                        <i data-fa-i2svg=""><svg class="svg-inline--fa fa-ban"
                                                                aria-hidden="true" focusable="false"
                                                                data-prefix="fas" data-icon="ban" role="img"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                viewBox="0 0 512 512" data-fa-i2svg="">
                                                                <path fill="currentColor"
                                                                    d="M367.2 412.5L99.5 144.8C77.1 176.1 64 214.5 64 256c0 106 86 192 192 192c41.5 0 79.9-13.1 111.2-35.5zm45.3-45.3C434.9 335.9 448 297.5 448 256c0-106-86-192-192-192c-41.5 0-79.9 13.1-111.2 35.5L412.5 367.2zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256z">
                                                                </path>
                                                            </svg></i>
                                                    </button>
                                                </form>

                                                <form
                                                    action="{{ route('superadmin.specialization.destroy', $specialization->id) }}"
                                                    method="POST" class="delete-form">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="button"
                                                        class="text-red-600 hover:text-red-800 transition delete-btn"
                                                        title="Delete">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
                                                            class="svg-inline--fa fa-trash">
                                                            <path
                                                                d="M135.2 17.7C140.9 7.4 151.7 0 163.8 0H284.2c12.1 0 22.9 7.4 28.6 17.7L328 32H432c8.8 0 16 7.2 16 16s-7.2 16-16 16H416l-21.2 339.1c-1.6 25.6-22.8 45-48.4 45H101.6c-25.6 0-46.8-19.4-48.4-45L32 64H16C7.2 64 0 56.8 0 48s7.2-16 16-16H120l15.2-14.3z" />
                                                        </svg>
                                                    </button>
                                                </form>


                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-6">
                            {{ $specializations->links() }}
                        </div>

                    </div>
                </div>
                <div id="bulk-actions" class="bg-white rounded-lg border border-neutral-200 p-4 mb-6">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-neutral-600">Bulk Actions:</span>
                            <button onclick="submitBulkAction('active')"
                                class="px-3 py-2 bg-neutral-600 text-white text-sm rounded-md hover:bg-neutral-700">
                                <i class="mr-2" data-fa-i2svg=""><svg class="svg-inline--fa fa-check"
                                        aria-hidden="true" focusable="false" data-prefix="fas" data-icon="check"
                                        role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"
                                        data-fa-i2svg="">
                                        <path fill="currentColor"
                                            d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z">
                                        </path>
                                    </svg></i>Activate Selected
                            </button>
                            <button onclick="submitBulkAction('inactive')"
                                class="px-3 py-2 bg-neutral-600 text-white text-sm rounded-md hover:bg-neutral-700">
                                <i class="mr-2" data-fa-i2svg=""><svg class="svg-inline--fa fa-ban"
                                        aria-hidden="true" focusable="false" data-prefix="fas" data-icon="ban"
                                        role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                        data-fa-i2svg="">
                                        <path fill="currentColor"
                                            d="M367.2 412.5L99.5 144.8C77.1 176.1 64 214.5 64 256c0 106 86 192 192 192c41.5 0 79.9-13.1 111.2-35.5zm45.3-45.3C434.9 335.9 448 297.5 448 256c0-106-86-192-192-192c-41.5 0-79.9 13.1-111.2 35.5L412.5 367.2zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256z">
                                        </path>
                                    </svg></i>Inactive Selected
                            </button>
                        </div>
                        <span class="text-sm text-neutral-500">0 selected</span>
                    </div>
                </div>
                <div id="stats-overview" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg border border-neutral-200 p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-neutral-100 rounded-lg flex items-center justify-center">
                                <i class="text-neutral-600 text-xl" data-fa-i2svg=""><svg
                                        class="svg-inline--fa fa-users" aria-hidden="true" focusable="false"
                                        data-prefix="fas" data-icon="users" role="img"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" data-fa-i2svg="">
                                        <path fill="currentColor"
                                            d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z">
                                        </path>
                                    </svg></i>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-neutral-500">Total Service Specialization</p>
                                <p class="text-2xl text-neutral-900">{{ $totalSpecializations }}</p>
                            </div>
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
