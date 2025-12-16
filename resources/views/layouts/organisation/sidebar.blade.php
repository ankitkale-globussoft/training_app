<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ">Learnit Platform</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none"><i class="bx bx-chevron-left bx-sm align-middle"></i></a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('org.home') ? 'active' : '' }}">
            <a href="{{ route('org.home') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Analytics">Dashboard</div></a>
        </li>

        <li class="menu-item {{ request()->routeIs('org.programs.*') ? 'active open' : '' }}">
            <a href="#" class="menu-link menu-toggle"><i class="menu-icon tf-icons bx bx-box"></i>
            <div data-i18n="Trainings">Programs</div></a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('org.programs.index') ? 'active' : '' }}">
                    <a href="{{ route('org.programs.index') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="All Programs">All Programs</div></a>
                </li>
                <li class="menu-item {{ request()->routeIs('org.programs.view.requested') ? 'active' : '' }}">
                    <a href="{{ route('org.programs.view.requested') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="My Programs">Requested Programs</div></a>
                </li>
            </ul>
        </li>
    </ul>
</aside>