<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ">Learnit Platform</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none"><i class="bx bx-chevron-left bx-sm align-middle"></i></a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item active">
            <a href="#" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Dashboard">Dashboard</div></a>
        </li>
        <li class="menu-item">
            <a href="#" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Programs">Programs</div></a>
        </li>

        {{-- Trainings --}}
        <li class="menu-item">{{-- 'active open' if want to show the active state of dropdown --}}
            <a href="#" class="menu-link menu-toggle"><i class="menu-icon tf-icons bx bx-box"></i>
            <div data-i18n="Trainings">Trainings</div></a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="#" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Upcomming">Upcomming</div></a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Ongoing">Ongoing</div></a>
                </li>
            </ul>
        </li>

        {{-- Tests --}}
        <li class="menu-item">{{-- 'active open' if want to show the active state of dropdown --}}
            <a href="#" class="menu-link menu-toggle"><i class="menu-icon tf-icons bx bx-box"></i>
            <div data-i18n="Tests">Tests</div></a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="#" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Create Test">Create Test</div></a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Add Question">Add Question</div></a>
                </li>
            </ul>
        </li>
    </ul>
</aside>