    <div class="sidebar d-none d-md-flex flex-column p-4">
        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="{{ route('superadmin.dashboard') }}"
                    class="nav-link flex items-center px-3 py-2 text-sm text-neutral-600 bg-neutral-100 rounded-md {{ Route::is('superadmin.dashboard') ? 'active' : '' }}">
                    <i class="mr-3" data-fa-i2svg="">
                        <svg class="svg-inline--fa fa-credit-card" aria-hidden="true" focusable="false" data-prefix="fas"
                            data-icon="credit-card" role="img" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 576 512" data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M64 32C28.7 32 0 60.7 0 96v32H576V96c0-35.3-28.7-64-64-64H64zM576 224H0V416c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V224zM112 352h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16zm112 16c0-8.8 7.2-16 16-16H368c8.8 0 16 7.2 16 16s-7.2 16-16 16H240c-8.8 0-16-7.2-16-16z">
                            </path>
                        </svg>
                    </i> Dashboard
                </a>
            </li>

            {{-- <li class="nav-item mb-2">
                <a href="#"
                    class="nav-link flex items-center px-3 py-2 text-sm text-neutral-600 bg-neutral-100 rounded-md">
                    <i class="mr-3" data-fa-i2svg="">
                        <svg class="svg-inline--fa fa-chart-bar" aria-hidden="true" focusable="false" data-prefix="fas"
                            data-icon="chart-bar" role="img" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 512 512" data-fa-i2svg="">
                            <path fill="currentColor"
                                d="M32 32c17.7 0 32 14.3 32 32V400c0 8.8 7.2 16 16 16H480c17.7 0 32 14.3 32 32s-14.3 32-32 32H80c-44.2 0-80-35.8-80-80V64C0 46.3 14.3 32 32 32zm96 96c0-17.7 14.3-32 32-32l192 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-192 0c-17.7 0-32-14.3-32-32zm32 64H288c17.7 0 32 14.3 32 32s-14.3 32-32 32H160c-17.7 0-32-14.3-32-32s14.3-32 32-32zm0 96H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H160c-17.7 0-32-14.3-32-32s14.3-32 32-32z">
                            </path>
                        </svg>
                    </i> Analytics
                </a>
            </li> --}}

            <li class="nav-item mb-2">
                <a href="{{ route('superadmin.plans.index') }}"
                    class="nav-link flex items-center px-3 py-2 text-sm text-neutral-600 bg-neutral-100 rounded-md {{ Route::is('superadmin.plans.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card"></i> Subscription Plans
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="{{ route('superadmin.builders.index') }}"
                    class="nav-link flex items-center px-3 py-2 text-sm text-neutral-600 bg-neutral-100 rounded-md {{ Route::is('superadmin.builders.*') ? 'active' : '' }}">
                    <i class="bi bi-house-gear"></i> Builders
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="{{ route('superadmin.properties.index') }}"
                    class="nav-link flex items-center px-3 py-2 text-sm text-neutral-600 bg-neutral-100 rounded-md {{ Route::is('superadmin.properties.*') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i>Properties
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="{{ route('superadmin.owners.index') }}"
                    class="nav-link flex items-center px-3 py-2 text-sm text-neutral-600 bg-neutral-100 rounded-md {{ Route::is('superadmin.owners.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>House Owner
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="{{ route('superadmin.specialization.index') }}"
                    class="nav-link flex items-center px-3 py-2 text-sm text-neutral-600 bg-neutral-100 rounded-md {{ Route::is('superadmin.specialization.*') ? 'active' : '' }}">
                    <i class="bi bi-wrench"></i>Service Specialization
                </a>
            </li>

            <li class="nav-item mb-2">
                <a href="{{ route('superadmin.providers.index') }}"
                    class="nav-link flex items-center px-3 py-2 text-sm text-neutral-600 bg-neutral-100 rounded-md {{ Route::is('superadmin.providers.*') ? 'active' : '' }}">
                    <i class="bi bi-tools"></i>Service Provider
                </a>
            </li>
        </ul>
    </div>
