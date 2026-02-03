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
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl text-neutral-900 mb-2">Welcome to {{ Auth::user()->name }}</h2>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('superadmin.logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                    class="btn btn-danger">
                                    Logout
                                </a>

                                <form id="logout-form" action="{{ route('superadmin.logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>

                        <div id="stats-overview" class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-4 mb-8">
                            <div class="bg-white rounded-lg border border-neutral-200 p-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-neutral-100 rounded-lg flex items-center justify-center">
                                        <i class="text-neutral-600 text-xl" data-fa-i2svg=""><svg
                                                class="svg-inline--fa fa-users" aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="users" role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"
                                                data-fa-i2svg="">
                                                <path fill="currentColor"
                                                    d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z">
                                                </path>
                                            </svg></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-neutral-500">Total Builders</p>
                                        <p class="text-2xl text-neutral-900">{{ $totalUsers }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg border border-neutral-200 p-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-neutral-100 rounded-lg flex items-center justify-center">
                                        <i class="text-neutral-600 text-xl" data-fa-i2svg=""><svg
                                                class="svg-inline--fa fa-circle-check" aria-hidden="true"
                                                focusable="false" data-prefix="fas" data-icon="circle-check"
                                                role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                                data-fa-i2svg="">
                                                <path fill="currentColor"
                                                    d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z">
                                                </path>
                                            </svg></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-neutral-500">Active Builders</p>
                                        <p class="text-2xl text-neutral-900">{{ $activeUsers }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg border border-neutral-200 p-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-neutral-100 rounded-lg flex items-center justify-center">
                                        <i class="text-neutral-600 text-xl" data-fa-i2svg=""><svg
                                                class="svg-inline--fa fa-clock" aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="clock" role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                                data-fa-i2svg="">
                                                <path fill="currentColor"
                                                    d="M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z">
                                                </path>
                                            </svg></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-neutral-500">Pending Builders</p>
                                        <p class="text-2xl text-neutral-900">{{ $pendingUsers }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg border border-neutral-200 p-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-neutral-100 rounded-lg flex items-center justify-center">
                                        <i class="text-neutral-600 text-xl" data-fa-i2svg=""><svg
                                                class="svg-inline--fa fa-ban" aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="ban" role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                                data-fa-i2svg="">
                                                <path fill="currentColor"
                                                    d="M367.2 412.5L99.5 144.8C77.1 176.1 64 214.5 64 256c0 106 86 192 192 192c41.5 0 79.9-13.1 111.2-35.5zm45.3-45.3C434.9 335.9 448 297.5 448 256c0-106-86-192-192-192c-41.5 0-79.9 13.1-111.2 35.5L412.5 367.2zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256z">
                                                </path>
                                            </svg></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-neutral-500">Suspended Builders</p>
                                        <p class="text-2xl text-neutral-900">{{ $blockedUsers }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="stats-overview" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                            <div class="bg-white rounded-lg border border-neutral-200 p-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-neutral-100 rounded-lg flex items-center justify-center">
                                        <i class="text-neutral-600 text-xl" data-fa-i2svg=""><svg
                                                class="svg-inline--fa fa-users" aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="users" role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"
                                                data-fa-i2svg="">
                                                <path fill="currentColor"
                                                    d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192h42.7c15.9 0 31 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96c-.2 0-.4 0-.7 0H21.3C9.6 320 0 310.4 0 298.7zM405.3 320c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3c13.6-6.3 28.7-9.7 44.6-9.7h42.7C592.2 192 640 239.8 640 298.7c0 11.8-9.6 21.3-21.3 21.3H405.3zM224 224a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352H378.7C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7H154.7c-14.7 0-26.7-11.9-26.7-26.7z">
                                                </path>
                                            </svg></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-neutral-500">Total Properties</p>
                                        <p class="text-2xl text-neutral-900">{{ $totalProperties }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg border border-neutral-200 p-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-neutral-100 rounded-lg flex items-center justify-center">
                                        <i class="text-neutral-600 text-xl" data-fa-i2svg=""><svg
                                                class="svg-inline--fa fa-circle-check" aria-hidden="true"
                                                focusable="false" data-prefix="fas" data-icon="circle-check"
                                                role="img" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 512 512" data-fa-i2svg="">
                                                <path fill="currentColor"
                                                    d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z">
                                                </path>
                                            </svg></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-neutral-500">Available Properties</p>
                                        <p class="text-2xl text-neutral-900">{{ $availableProperties }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg border border-neutral-200 p-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-neutral-100 rounded-lg flex items-center justify-center">
                                        <i class="text-neutral-600 text-xl" data-fa-i2svg=""><svg
                                                class="svg-inline--fa fa-clock" aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="clock" role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                                data-fa-i2svg="">
                                                <path fill="currentColor"
                                                    d="M256 0a256 256 0 1 1 0 512A256 256 0 1 1 256 0zM232 120V256c0 8 4 15.5 10.7 20l96 64c11 7.4 25.9 4.4 33.3-6.7s4.4-25.9-6.7-33.3L280 243.2V120c0-13.3-10.7-24-24-24s-24 10.7-24 24z">
                                                </path>
                                            </svg></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-neutral-500">Pending Properties</p>
                                        <p class="text-2xl text-neutral-900">{{ $pendingProperties }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white rounded-lg border border-neutral-200 p-6">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-neutral-100 rounded-lg flex items-center justify-center">
                                        <i class="text-neutral-600 text-xl" data-fa-i2svg=""><svg
                                                class="svg-inline--fa fa-ban" aria-hidden="true" focusable="false"
                                                data-prefix="fas" data-icon="ban" role="img"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                                                data-fa-i2svg="">
                                                <path fill="currentColor"
                                                    d="M367.2 412.5L99.5 144.8C77.1 176.1 64 214.5 64 256c0 106 86 192 192 192c41.5 0 79.9-13.1 111.2-35.5zm45.3-45.3C434.9 335.9 448 297.5 448 256c0-106-86-192-192-192c-41.5 0-79.9 13.1-111.2 35.5L412.5 367.2zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256z">
                                                </path>
                                            </svg></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-neutral-500">Sold Properties</p>
                                        <p class="text-2xl text-neutral-900">{{ $soldProperties }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4 mb-4">

                            <!-- User Status -->
                            <div class="col-lg-4">
                                <div class="card p-4 h-100">
                                    <h6 class="fw-semibold text-muted mb-3">User Status</h6>
                                    <canvas id="userStatusChart"></canvas>
                                </div>
                            </div>

                            <!-- Property Status -->
                            <div class="col-lg-4">
                                <div class="card p-4 h-100">
                                    <h6 class="fw-semibold text-muted mb-3">Property Status</h6>
                                    <canvas id="propertyStatusChart"></canvas>
                                </div>
                            </div>

                            <!-- Overview -->
                            <div class="col-lg-4">
                                <div class="card p-4 h-100">
                                    <h6 class="fw-semibold text-muted mb-3">Platform Overview</h6>
                                    <canvas id="overviewChart"></canvas>
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
    Chart.defaults.font.family = "Inter, system-ui, -apple-system";
    Chart.defaults.color = "#6c757d";

    /* ---------------- USER STATUS (BAR) ---------------- */
    new Chart(document.getElementById('userStatusChart'), {
        type: 'bar',
        data: {
            labels: ['Active', 'Pending', 'Blocked'],
            datasets: [{
                data: [
                    {{ $activeUsers }},
                    {{ $pendingUsers }},
                    {{ $blockedUsers }}
                ],
                backgroundColor: ['#198754', '#ffc107', '#adb5bd'],
                borderRadius: 6
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    /* ---------------- PROPERTY STATUS (DOUGHNUT) ---------------- */
    new Chart(document.getElementById('propertyStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Available', 'Pending', 'Sold'],
            datasets: [{
                data: [
                    {{ $availableProperties }},
                    {{ $pendingProperties }},
                    {{ $soldProperties }}
                ],
                backgroundColor: ['#0d6efd', '#ffc107', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            }
        }
    });

    /* ---------------- OVERVIEW (LINE) ---------------- */
    new Chart(document.getElementById('overviewChart'), {
        type: 'line',
        data: {
            labels: ['Users', 'Properties'],
            datasets: [{
                label: 'Total',
                data: [
                    {{ $totalUsers }},
                    {{ $totalProperties }}
                ],
                borderColor: '#f89d31',
                backgroundColor: 'rgba(248,157,49,0.15)',
                tension: 0.4,
                fill: true,
                pointRadius: 5
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>


</html>
