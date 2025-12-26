<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ">Learnit Platform</span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none"><i
                class="bx bx-chevron-left bx-sm align-middle"></i></a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <li class="menu-item {{ request()->routeIs('trainer.dashboard') ? 'active' : '' }}">
            <a href="{{ route('trainer.dashboard') }}" class="menu-link"><i
                    class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        @if(Auth::guard('trainer_web')->user()->verified === 'verified')
            {{-- Programs --}}
            <li class="menu-item {{ request()->routeIs('trainer.programs.*') ? 'active open' : '' }}">
                <a href="#" class="menu-link menu-toggle"><i class="menu-icon tf-icons bx bx-box"></i>
                    <div data-i18n="Trainings">Programs</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('trainer.programs.browse') ? 'active' : '' }}">
                        <a href="{{ route('trainer.programs.browse') }}" class="menu-link"><i
                                class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="All Programs">All Programs</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('trainer.programs.index') ? 'active' : '' }}">
                        <a href="{{ route('trainer.programs.index') }}" class="menu-link"><i
                                class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="My Programs">My Programs</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Trainings --}}
            <li class="menu-item {{ request()->routeIs('trainer.trainings.*') ? 'active open' : '' }}">
                <a href="#" class="menu-link menu-toggle"><i class="menu-icon tf-icons bx bx-box"></i>
                    <div data-i18n="Trainings">Trainings</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('trainer.trainings.open') ? 'active' : '' }}">
                        <a href="{{ route('trainer.trainings.open') }}" class="menu-link"><i
                                class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Upcomming">Open Requests</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('trainer.trainings.upcomming') ? 'active' : '' }}">
                        <a href="{{ route('trainer.trainings.upcomming') }}" class="menu-link"><i
                                class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Upcomming">Upcomming</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('trainer.trainings.assigned') ? 'active' : '' }}">
                        <a href="{{ route('trainer.trainings.assigned') }}" class="menu-link"><i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Ongoing">Assigned</div>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Content Manager --}}
            <li class="menu-item {{ request()->routeIs('trainer.content-manager') ? 'active' : '' }}">
                <a href="{{ route('trainer.content-manager') }}" class="menu-link"><i
                        class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Content Manager">Content Manager</div>
                </a>
            </li>

            {{-- Payments --}}
            <li class="menu-item {{ request()->routeIs('trainer.payments.*') ? 'active open' : '' }}">
                <a href="#" class="menu-link menu-toggle"><i class="menu-icon tf-icons bx bx-box"></i>
                    <div data-i18n="Trainings">Payments</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('trainer.payments.view') ? 'active' : '' }}">
                        <a href="{{ route('trainer.payments.view') }}" class="menu-link"><i
                                class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Payments">Payments</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('trainer.payments.request') ? 'active' : '' }}">
                        <a href="{{ route('trainer.payments.request') }}" class="menu-link"><i
                                class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Request Payment">Request Payment</div>
                        </a>
                    </li> 
                    <li class="menu-item {{ request()->routeIs('trainer.payments.account-details') ? 'active' : '' }}">
                        <a href="{{ route('trainer.payments.account-details') }}" class="menu-link"><i
                                class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Account Details">Account Details</div>
                        </a>
                    </li>
                </ul>
            </li>
        @endif
    </ul>
</aside>