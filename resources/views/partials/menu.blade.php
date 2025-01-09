<li class="menu-item  {{ request()->is('dashboard*') ? 'active' : '' }}">
    <a href="{{ route('core.dashboard.index', ['route_subdomain' => request('route_subdomain')]) }}" wire:navigate
        class='menu-link'>
        <i class="fas fa-tachometer-alt-fast"></i>
        <span>Dashboard</span>
    </a>
</li>

@canany(['accounts.view'])
    <li class="menu-item  {{ request()->is('accounts*') ? 'active' : '' }}">
        <a href="{{ route('core.account.index') }}" wire:navigate class='menu-link'>
            <i class="fas fa-building"></i>
            <span>Accounts</span>
        </a>
    </li>
@endcan

@canany(['users.view'])
    <li class="menu-item  {{ request()->is('users*') ? 'active' : '' }}">
        <a href="{{ route('core.user.index') }}" wire:navigate class='menu-link'>
            <i class="fas fa-users"></i>
            <span>Users</span>
        </a>
    </li>
@endcan

@canany(['roles.view'])
    <li class="menu-item {{ request()->is('roles*') ? 'active' : '' }}">
        <a href="{{ route('core.role.index') }}" wire:navigate class='menu-link'>
            <i class="fas fa-shield-alt"></i>
            <span>Roles</span>
        </a>
    </li>
@endcan

@unless (request()->route_subdomain == 'admin')

    @if (auth()->user()->account->hasModule('customers'))
        @canany(['customers.view'])
            <li class="menu-item  {{ request()->is('customers*') ? 'active' : '' }}">
                <a href="{{ route('core.customer.index') }}" wire:navigate class='menu-link'>
                    <i class="fas fa-address-card"></i>
                    <span>Customers</span>
                </a>
            </li>
        @endcan

    @endif

    @if (auth()->user()->account->hasModule('reporting'))
        @canany(['reporting.view'])
            <li class="menu-item {{ request()->is('reporting*') ? 'active' : '' }}">
                <a href="{{ route('reporting.index') }}" wire:navigate class='menu-link'>
                    <i class="fa-solid fa-chart-line"></i>
                    <span>Reporting</span>
                </a>
            </li>
        @endcan

    @endif

    @if (auth()->user()->account->hasModule('vehicles'))

        @canany(['vehicle.view'])
            <li class="menu-item  {{ request()->is('vehicle*') ? 'active' : '' }}">
                <a href="{{ route('vehicle.index') }}" wire:navigate class='menu-link'>
                    <i class="fas fa-truck"></i>
                    <span>Vehicle</span>
                </a>
            </li>
        @endcan

    @endif

    @if (auth()->user()->account->hasModule('equipment'))
        <li class="menu-item {{ request()->is('equipment*') ? 'active' : '' }}  has-sub">
            <a href="#" class="menu-link">
                <span><i class="far fa-list-alt"></i> Equipment</span>
            </a>
            <div class="submenu ">
                <div class="submenu-group-wrapper">
                    <ul class="submenu-group">
                        @canany(['equipment.unavailable.view'])
                            <li class="submenu-item  ">
                                <a href="{{ route('equipment.unavailable.index') }}" wire:navigate
                                    class="submenu-link">Unavailable Units</a>
                            </li>
                        @endcan
                        @canany(['equipment.warranty.view', 'equipment.warranty.manage'])
                            <li class="submenu-item  ">
                                <a href="{{ route('equipment.warranty.index') }}" wire:navigate class="submenu-link">Warranty
                                    Registration</a>
                            </li>
                        @endcan
                        @canany(['equipment.floor-model-inventory.view', 'equipment.floor-model-inventory.manage'])
                            <li class="submenu-item  ">
                                <a href="{{ route('equipment.floor-model-inventory.index') }}" wire:navigate
                                    class="submenu-link">Floor Model Inventory</a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </div>
        </li>
    @endif
    @if (auth()->user()->account->hasModule('sales-rep-override'))
        @can('customer.sales-rep-override.view')
            <li class="menu-item  {{ request()->is('sales-rep-override*') ? 'active' : '' }}">
                <a href="{{ route('sales-rep-override.index') }}" wire:navigate class='menu-link'>
                    <i class="fas fa-universal-access"></i>
                    <span>Sales Rep Override</span>
                </a>
            </li>
        @endcan
    @endif

    @if (auth()->user()->account->hasModule('marketing'))
        <li class="menu-item {{ request()->is('marketing*') ? 'active' : '' }}  has-sub">
            <a href="#" class="menu-link">
                <span><i class="far fa-list-alt"></i> Marketing</span>
            </a>
            <div class="submenu ">
                <div class="submenu-group-wrapper">
                    <ul class="submenu-group">
                        @canany(['marketing.sms-view', 'marketing.sms-manage'])
                            <li class="submenu-item">
                                <a href="{{ route('marketing.sms-marketing.index') }}" wire:navigate
                                    class="submenu-link">Kenect Blast</a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </div>
        </li>
    @endif
    @if (auth()->user()->account->hasModule('orders'))

        @canany(['order.view'])
            <li class="menu-item  {{ request()->is('orders*') ? 'active' : '' }}">
                <a href="{{ route('order.index') }}" wire:navigate class='menu-link'>
                    <i class="fas fa-boxes"></i>
                    <span>Orders</span>
                </a>
            </li>
        @endcan

    @endif

    @if (auth()->user()->account->hasModule('pos'))

        @canany(['terminals.view', 'transactions.view'])
            <li class="menu-item  {{ request()->is('pos*') ? 'active' : '' }}">
                <a href="{{ route('pos.index') }}" wire:navigate class='menu-link'>
                    <i class="fas fa-cash-register"></i>
                    <span>POS</span>
                </a>
            </li>
        @endcan

    @endif

    @if (auth()->user()->account->hasModule('products'))

        @canany(['products.view'])
            <li class="menu-item  {{ request()->is('products*') ? 'active' : '' }}">
                <a href="{{ route('products.index') }}" wire:navigate class='menu-link'>
                    <i class="fas fa-list-alt"></i>
                    <span>Products</span>
                </a>
            </li>
        @endcan

    @endif

    @if (auth()->user()->account->hasModule('scheduler'))

        <li class="menu-item {{ request()->is('scheduler*') ? 'active' : '' }}  has-sub">
            <a href="#" class="menu-link">
                <span><i class="far fa-calendar-check"></i> Scheduler</span>
            </a>
            <div class="submenu ">
                <div class="submenu-group-wrapper">
                    <ul class="submenu-group">
                        @canany(['scheduler.schedule.view'])
                            <li class="submenu-item  ">
                                <a href="{{ route('schedule.index') }}" wire:navigate class="submenu-link">Schedule</a>
                            </li>
                        @endcan
                        @can('scheduler.serice-area.view')
                            <li class="submenu-item  {{ request()->is('service-area*') ? 'active' : '' }}">
                                <a href="{{ route('service-area.index') }}" wire:navigate>
                                    <span>Service Area</span>
                                </a>
                            </li>
                        @endcan
                        @canany(['scheduler.truck.view'])
                            <li class="submenu-item  ">
                                <a href="{{ route('scheduler.truck.index') }}" wire:navigate class="submenu-link">Truck</a>
                            </li>
                        @endcan
                        @canany(['scheduler.schedule.view'])
                            <li class="submenu-item  ">
                                <a href="{{ route('equipment.floor-model-inventory.index') }}" wire:navigate
                                    class="submenu-link">Drivers</a>
                            </li>
                        @endcan

                        <li class="submenu-item">
                            <a href="" wire:navigate>
                                <span>Surveys</span>
                            </a>
                        </li>
                        @canany(['scheduler.template.view'])
                            <li class="submenu-item">
                                <a href="{{ route('schedule.email-template.index') }}" wire:navigate>
                                    <span>Templates</span>
                                </a>
                            </li>
                        @endcan
                        @canany(['scheduler.shift.view'])
                            <li class="submenu-item">
                                <a href="{{ route('schedule.shift.index') }}" wire:navigate>
                                    <span>Shifts</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </div>
            </div>
        </li>


    @endif



    @endif
