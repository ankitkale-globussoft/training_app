<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ">Learnit Platform</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none"><i class="bx bx-chevron-left bx-sm align-middle"></i></a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Analytics">Dashboard</div></a>
        </li>

        {{-- programs --}}
        <li class="menu-item {{ request()->routeIs('admin.program.*') ? 'active open' : '' }}">
            <a href="#" class="menu-link menu-toggle"><i class="menu-icon tf-icons bx bx-box"></i>
            <div data-i18n="Programs">Programs</div></a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.program.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.program.index') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="All Programs">All Programs</div></a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.program.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.program.create') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="All Programs">Add Program</div></a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Program Types">Program Types</div></a>
                </li>
            </ul>
        </li>

        {{-- Test --}}
        <li class="menu-item {{ request()->routeIs('admin.test.*') ? 'active open' : '' }}">
            <a href="#" class="menu-link menu-toggle"><i class="menu-icon tf-icons bx bx-box"></i>
            <div data-i18n="Tests">Tests</div></a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('admin.test.index') ? 'active' : '' }}">
                    <a href="{{ route('admin.test.index') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="All Tests">All Tests</div></a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.test.create') ? 'active' : '' }}">
                    <a href="{{ route('admin.test.create') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Create Test">Create Test</div></a>
                </li>
            </ul>
        </li>

        {{-- Payment --}}
        <li class="menu-item {{ request()->routeIs('admin.payments') ? 'active' : '' }}">
            <a href="{{ route('admin.payments') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Payments">Payments</div></a>
        </li>

        {{-- Trainers --}}
        <li class="menu-item {{ request()->routeIs('admin.trainers') ? 'active' : '' }}">
            <a href="{{ route('admin.trainers') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Trainers">Trainers</div></a>
        </li>
    </ul>
</aside>