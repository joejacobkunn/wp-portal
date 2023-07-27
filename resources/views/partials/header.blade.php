<header class="mb-4">
    <div class="header-top">
        <div class="container">
            <div class="logo">
                <a href="{{ route('core.dashboard.index') }}"><img src="/assets/images/logo.png" alt="Logo"></a>
            </div>
            <div class="header-top-right">

                <div class="dropdown">
                    <a href="#" id="topbarUserDropdown" class="user-dropdown d-flex align-items-center dropend dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar bg-primary">
                            <span class="avatar-content">{{ auth()->user()->abbreviation }}</span>
                        </div>

                        <div class="text">
                            <h6 class="user-dropdown-name">{{ auth()->user()->name }}</h6>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="topbarUserDropdown">
                        <li><a class="dropdown-item" href="javascript:void(0);">{{ auth()->user()->email }}</a></li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="{{ route('auth.login.logout') }}">Logout</a></li>
                    </ul>
                </div>

                <!-- Burger button responsive -->
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </div>
        </div>
    </div>
    <nav class="main-navbar">
        <div class="container">
            <ul>
                @include('partials.menu')
            </ul>
        </div>
    </nav>
</header>
