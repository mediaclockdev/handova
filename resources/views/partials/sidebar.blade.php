        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <a href="{{ url('/') }}"><img src="{{ asset('images/handova.svg') }}" alt="Handova Logo"
                        class="logo"></a>
            </div>

            <nav class="nav flex-column">
                <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i> Dashboard
                </a>
                <a class="nav-link {{ Route::is('admin.properties.*') ? 'active' : '' }}"
                    href="{{ route('admin.properties.index') }}">
                    <i class="bi bi-house-door"></i> Properties
                </a>
                <a class="nav-link {{ Route::is('admin.house_owners.*') ? 'active' : '' }}"
                    href="{{ route('admin.house_owners.index') }}">
                    <i class="bi bi-people"></i> House owners
                </a>
                <a class="nav-link {{ Route::is('admin.issue_report.*') ? 'active' : '' }}"
                    href="{{ route('admin.issue_report.index') }}">
                    <i class="bi bi-exclamation-circle"></i> Issue Reporting
                </a>
                <a class="nav-link {{ Route::is('admin.service_provider.*') ? 'active' : '' }}"
                    href="{{ route('admin.service_provider.index') }}">
                    <i class="bi bi-tools"></i> Service provider
                </a>
                <a class="nav-link {{ Route::is('admin.report_analytics.*') ? 'active' : '' }}"
                    href="{{ route('admin.report_analytics.index') }}">
                    <i class="bi bi-bar-chart"></i> Report And Analytics
                </a>
                {{-- <a class="nav-link" href="#">
                    <i class="bi bi-search"></i> Audit trail
                </a>
                <a class="nav-link" href="#">
                    <i class="bi bi-person-badge"></i> Admin
                </a> --}}
                <a class="nav-link {{ Route::is('admin.house_plans.*') ? 'active' : '' }}"
                    href="{{ route('admin.house_plans.index') }}">
                    <i class="bi bi-geo-alt"></i> House plans
                </a>
                <a class="nav-link {{ Route::is('admin.appliances.*') ? 'active' : '' }}"
                    href="{{ route('admin.appliances.index') }}">
                    <i class="bi bi-plug"></i> Appliances
                </a>
                <a class="nav-link {{ Route::is('admin.compliance_certificates.*') ? 'active' : '' }}"
                    href="{{ route('admin.compliance_certificates.index') }}">
                    <i class="bi bi-patch-check"></i> Compliance certificates
                </a>
                <a class="nav-link {{ Route::is('admin.page_content.*') ? 'active' : '' }}"
                    href="{{ route('admin.page_content.index') }}">
                    <i class="bi bi-patch-check"></i> Page Content
                </a>
            </nav>

            <hr>

            <nav class="nav flex-column mt-auto">
                <a class="nav-link {{ Route::is('admin.help.*') ? 'active' : '' }}"
                    href="{{ route('admin.help.index') }}">
                    <i class="bi bi-question-circle"></i> Help
                </a>
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a class="nav-link" href="#" id="logout-btn">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </form>
            </nav>
        </div>

        <div class="sidebar-backdrop" id="sidebar-backdrop"></div>
