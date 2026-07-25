<!--begin::Header-->
<nav class="app-header navbar navbar-expand bg-body border-bottom shadow-sm" wire:ignore>
  <!--begin::Container-->
  <div class="container-fluid px-3">
    <!--begin::Start Navbar Links-->
    <ul class="navbar-nav align-items-center">
      <li class="nav-item">
        <a
          class="nav-link rounded-circle d-flex align-items-center justify-content-center"
          style="width:38px;height:38px"
          data-lte-toggle="sidebar"
          href="#"
          role="button"
          aria-label="Toggle sidebar"
        >
          <i class="bi bi-list fs-5"></i>
        </a>
      </li>

      <!--begin::Global Search-->
      {{-- <li class="nav-item d-none d-lg-block ms-2">
        <form class="d-flex" role="search" onsubmit="return false;">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-transparent border-end-0">
              <i class="bi bi-search text-secondary"></i>
            </span>
            <input
              type="search"
              class="form-control border-start-0 ps-0"
              placeholder="Search products, customers, invoices…"
              aria-label="Search"
              style="min-width:320px"
            />
          </div>
        </form>
      </li> --}}
      <!--end::Global Search-->
    </ul>
    <!--end::Start Navbar Links-->

    <!--begin::End Navbar Links-->
    <ul class="navbar-nav ms-auto align-items-center gap-1">
      <!--begin::Messages Dropdown Menu-->

      <!--end::Messages Dropdown Menu-->


      <!--begin::Fullscreen Toggle-->
      <li class="nav-item d-none d-md-block">
        <a
          class="nav-link rounded-circle d-flex align-items-center justify-content-center"
          style="width:38px;height:38px"
          href="#"
          data-lte-toggle="fullscreen"
          aria-label="Toggle fullscreen"
        >
          <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
          <i data-lte-icon="minimize" class="bi bi-fullscreen-exit d-none"></i>
        </a>
      </li>
      <!--end::Fullscreen Toggle-->

      <!--begin::Color Mode Toggle-->
      <li class="nav-item dropdown">
        <a
          class="nav-link rounded-circle d-flex align-items-center justify-content-center"
          style="width:38px;height:38px"
          href="#"
          id="bd-theme"
          aria-label="Toggle color scheme"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          <i class="bi bi-sun-fill" data-lte-theme-icon="light"></i>
          <i class="bi bi-moon-fill d-none" data-lte-theme-icon="dark"></i>
          <i class="bi bi-circle-half d-none" data-lte-theme-icon="auto"></i>
        </a>
        <ul
          class="dropdown-menu dropdown-menu-end shadow border-0 mt-2"
          aria-labelledby="bd-theme"
          style="--bs-dropdown-min-width: 9rem"
        >
          <li>
            <button
              type="button"
              class="dropdown-item d-flex align-items-center"
              data-bs-theme-value="light"
              aria-pressed="false"
            >
              <i class="bi bi-sun-fill me-2"></i>
              Light
              <i class="bi bi-check-lg ms-auto d-none"></i>
            </button>
          </li>
          <li>
            <button
              type="button"
              class="dropdown-item d-flex align-items-center"
              data-bs-theme-value="dark"
              aria-pressed="false"
            >
              <i class="bi bi-moon-fill me-2"></i>
              Dark
              <i class="bi bi-check-lg ms-auto d-none"></i>
            </button>
          </li>
          <li>
            <button
              type="button"
              class="dropdown-item d-flex align-items-center active"
              data-bs-theme-value="auto"
              aria-pressed="true"
            >
              <i class="bi bi-circle-half me-2"></i>
              Auto
              <i class="bi bi-check-lg ms-auto d-none"></i>
            </button>
          </li>
        </ul>
      </li>
      <!--end::Color Mode Toggle-->

      <!--begin::User Menu Dropdown-->
      <!--begin::User Menu Dropdown-->
<li class="nav-item dropdown user-menu ms-1">
  <a href="#" class="nav-link dropdown-toggle d-flex align-items-center py-1 px-2 rounded" data-bs-toggle="dropdown">
    <span class="d-none d-md-inline ms-2 fw-medium">{{ Auth::user()->name }}</span>
  </a>
  <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0 mt-2 p-0">

    <!--begin::Menu Header-->
    <li class="p-3 border-bottom">
      <div class="d-flex align-items-center gap-2">
        <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold"
             style="width:40px;height:40px">
          {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div>
          <div class="fw-semibold">{{ Auth::user()->name }}</div>
          <div class="text-muted small">{{ Auth::user()->email }}</div>
        </div>
      </div>
    </li>
    <!--end::Menu Header-->

    <!--begin::Menu Footer-->
    <li class="user-footer d-flex gap-2 p-2">
      <form method="POST" action="{{ route('logout') }}" class="flex-fill">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2" data-test="logout-button">
          <i class="bi bi-box-arrow-right"></i>
          {{ __('Log out') }}
        </button>
      </form>
    </li>
    <!--end::Menu Footer-->
  </ul>
</li>
<!--end::User Menu Dropdown-->
      <!--end::User Menu Dropdown-->
    </ul>
    <!--end::End Navbar Links-->
  </div>
  <!--end::Container-->
</nav>
<!--end::Header-->
