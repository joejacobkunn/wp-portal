<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-block justify-content-between align-items-center">
                <div class="logo">
                    <a href="index.html"><img src="https://zuramai.github.io/mazer/demo/assets/images/logo/logo.svg" style="width: 100px" /></a>
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
                    <a href="{{ route('core.dashboard.index') }}" class='sidebar-link'>
                        <i class="fas fa-tachometer-alt-fast"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                @canany(['affiliates.view', 'affiliates.manage'])
                <li class="sidebar-item {{ (request()->is('affiliates*')) ? 'active' : '' }}">
                    <a href="{{ route('core.affiliate.index') }}" class='sidebar-link'>
                        <i class="fas fa-solid fa-circle-nodes"></i>
                        <span>Affiliates</span>
                    </a>
                </li>
                @endcan

                @canany(['users.view', 'users.manage'])
                <li class="sidebar-item {{ (request()->is('users*')) ? 'active' : '' }}">
                    <a href="{{ route('core.user.index') }}" class='sidebar-link'>
                    <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                @endcan


            </ul>
        </div>
    </div>
</div>