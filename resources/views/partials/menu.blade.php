<li class="menu-item  {{ (request()->is('dashboard*')) ? 'active' : '' }}">
    <a href="{{ route('core.dashboard.index', ['route_subdomain' => request('route_subdomain')]) }}" class='menu-link'>
        <i class="fas fa-tachometer-alt-fast"></i>
        <span>Dashboard</span>
    </a>
</li>

@canany(['accounts.view'])
<li class="menu-item  {{ (request()->is('accounts*')) ? 'active' : '' }}">
    <a href="{{ route('core.account.index') }}" class='menu-link'>
        <i class="fas fa-building"></i>
        <span>Accounts</span>
    </a>
</li>
@endcan

@canany(['users.view'])
<li class="menu-item  {{ (request()->is('users*')) ? 'active' : '' }}">
    <a href="{{ route('core.user.index') }}" class='menu-link'>
        <i class="fas fa-users"></i>
        <span>Users</span>
    </a>
</li>
@endcan

<li class="menu-item  {{ (request()->is('customers*')) ? 'active' : '' }}">
    <a href="{{ route('core.customer.index') }}" class='menu-link'>
        <i class="fas fa-address-card"></i>
        <span>Customers</span>
    </a>
</li>

@canany(['vehicle.view'])
<li class="menu-item  {{ (request()->is('vehicle*')) ? 'active' : '' }}">
    <a href="{{ route('vehicle.index') }}" class='menu-link'>
        <i class="fas fa-truck"></i>
        <span>Vehicle</span>
    </a>
</li>
@endcan

@canany(['order.view'])
<li class="menu-item  {{ (request()->is('order*')) ? 'active' : '' }}">
    <a href="{{ route('order.index') }}" class='menu-link'>
        <i class="far fa-list-alt"></i>
        <span>Orders</span>
    </a>
</li>
@endcan


@canany(['roles.view'])
<li class="menu-item {{ (request()->is('roles*')) ? 'active' : '' }}">
    <a href="{{ route('core.role.index') }}" class='menu-link'>
        <i class="far fa-list-alt"></i>
        <span>Roles</span>
    </a>
</li>
@endcan