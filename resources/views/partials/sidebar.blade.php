<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-block justify-content-between align-items-center">
                <div class="logo">
                    <a href="index.html"><img src="/assets/images/logo.png" /></a>
                </div>
                <div class="sidebar-toggler  x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <li class="sidebar-item {{ (request()->is('dashboard*')) ? 'active' : '' }}">
                    <a href="{{ route('core.dashboard.index', ['route_subdomain' => request('route_subdomain')]) }}"
                        class='sidebar-link'>
                        <i class="fas fa-tachometer-alt-fast"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @canany(['accounts.view'])
                <li class="sidebar-item {{ (request()->is('accounts*')) ? 'active' : '' }}">
                    <a href="{{ route('core.account.index') }}" class='sidebar-link'>
                        <i class="fas fa-building"></i>
                        <span>Accounts</span>
                    </a>
                </li>
                @endcan

                @canany(['users.view'])
                <li class="sidebar-item {{ (request()->is('users*')) ? 'active' : '' }}">
                    <a href="{{ route('core.user.index') }}" class='sidebar-link'>
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                @endcan

                @unless(request()->route_subdomain == 'admin')

                @if(auth()->user()->account->hasModule('customers'))

                <li class="sidebar-item {{ (request()->is('customers*')) ? 'active' : '' }}">
                    <a href="{{ route('core.customer.index') }}" class='sidebar-link'>
                        <i class="fas fa-address-card"></i>
                        <span>Customers</span>
                    </a>
                </li>

                @endif

                @if(auth()->user()->account->hasModule('vehicles'))

                @canany(['vehicle.view'])
                <li class="sidebar-item {{ (request()->is('vehicle*')) ? 'active' : '' }}">
                    <a href="{{ route('vehicle.index') }}" class='sidebar-link'>
                        <i class="fas fa-truck"></i>
                        <span>Vehicle</span>
                    </a>
                </li>
                @endcan

                @endif

                @if(auth()->user()->account->hasModule('orders'))

                @canany(['order.view'])
                <li class="sidebar-item {{ (request()->is('order*')) ? 'active' : '' }}">
                    <a href="{{ route('order.index') }}" class='sidebar-link'>
                        <i class="far fa-list-alt"></i>
                        <span>Orders</span>
                    </a>
                </li>
                @endcan

                @endif

                @endif

                @canany(['roles.view'])
                <li class="sidebar-item {{ (request()->is('roles*')) ? 'active' : '' }}">
                    <a href="{{ route('core.role.index') }}" class='sidebar-link'>
                        <i class="far fa-list-alt"></i>
                        <span>Roles</span>
                    </a>
                </li>
                @endcan



            </ul>
        </div>
    </div>
</div>