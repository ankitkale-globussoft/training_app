<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ">{{ config('app.name') }}</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none"><i
                class="bx bx-chevron-left bx-sm align-middle"></i></a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('student.home') ? 'active' : '' }}">
            <a href="{{ route('student.home') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <!-- Tests Menu -->
        <li class="menu-item {{ request()->routeIs('student.tests.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Tests">Tests</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('student.tests.available') ? 'active' : '' }}">
                    <a href="{{ route('student.tests.available') }}" class="menu-link">
                        <div data-i18n="Available Tests">Available Tests</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('student.tests.attempted') ? 'active' : '' }}">
                    <a href="{{ route('student.tests.attempted') }}" class="menu-link">
                        <div data-i18n="Attempted Tests">Attempted Tests</div>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Certificates -->
        <li class="menu-item {{ request()->routeIs('student.certificates.*') ? 'active' : '' }}">
            <a href="{{ route('student.certificates.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-award"></i>
                <div data-i18n="Certificates">Certificates</div>
            </a>
        </li>
    </ul>
</aside>