<header class="mb-3 float-end">
  <a href="#" class="burger-btn d-block d-xl-none">
    <i class="bi bi-justify fs-3"></i>
  </a>

  <div class="dropdown auth-user-div pt-2">
      <button class="btn dropdown-toggle me-1" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div class="d-none d-lg-inline d-md-inline"><i class="fa fa-user me-1"></i> {{ auth()->user()->email }}</div>
          <div class="d-lg-none d-md-none"><i class="fa fa-ellipsis-vertical"></i> </div>
      </button>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="margin: 0px;">
          <div class="d-lg-none d-md-none text-truncate px-4"><small><i class="fa fa-user me-1"></i>  {{ auth()->user()->email }}</small></div>
          <a class="dropdown-item" href="{{ route('auth.login.logout') }}">Logout</a>
      </div>
  </div>
</header>