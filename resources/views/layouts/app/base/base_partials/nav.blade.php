<!--begin::Header-->
<nav class="app-header navbar navbar-expand bg-body border-bottom shadow-sm">
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
      <li class="nav-item d-none d-lg-block ms-2">
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
      </li>
      <!--end::Global Search-->
    </ul>
    <!--end::Start Navbar Links-->

    <!--begin::End Navbar Links-->
    <ul class="navbar-nav ms-auto align-items-center gap-1">
      <!--begin::Messages Dropdown Menu-->
      <li class="nav-item dropdown">
        <a
          class="nav-link rounded-circle d-flex align-items-center justify-content-center position-relative"
          style="width:38px;height:38px"
          data-bs-toggle="dropdown"
          href="#"
          aria-label="Messages: 3 unread"
        >
          <i class="bi bi-chat-text fs-6"></i>
          <span
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger"
            style="font-size:.6rem"
          >
            3
          </span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0 mt-2 p-0">
          <span class="dropdown-item-text fw-semibold small text-secondary px-3 pt-3 pb-2 d-block">
            Messages
          </span>
          <a href="#" class="dropdown-item px-3 py-2">
            <!--begin::Message-->
            <div class="d-flex">
              <div class="flex-shrink-0">
                <img
                  src="./assets/img/user1-128x128.jpg"
                  alt=""
                  class="img-size-50 rounded-circle me-3"
                />
              </div>
              <div class="flex-grow-1">
                <p class="dropdown-item-title mb-0">
                  Brad Diesel
                  <span class="float-end fs-7 text-danger"><i class="bi bi-star-fill"></i></span>
                </p>
                <p class="fs-7 mb-0 text-truncate">Call me whenever you can...</p>
                <p class="fs-7 text-secondary mb-0">
                  <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                </p>
              </div>
            </div>
            <!--end::Message-->
          </a>
          <div class="dropdown-divider m-0"></div>
          <a href="#" class="dropdown-item px-3 py-2">
            <!--begin::Message-->
            <div class="d-flex">
              <div class="flex-shrink-0">
                <img
                  src="./assets/img/user8-128x128.jpg"
                  alt=""
                  class="img-size-50 rounded-circle me-3"
                />
              </div>
              <div class="flex-grow-1">
                <p class="dropdown-item-title mb-0">
                  John Pierce
                  <span class="float-end fs-7 text-secondary"><i class="bi bi-star-fill"></i></span>
                </p>
                <p class="fs-7 mb-0 text-truncate">I got your message bro</p>
                <p class="fs-7 text-secondary mb-0">
                  <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                </p>
              </div>
            </div>
            <!--end::Message-->
          </a>
          <div class="dropdown-divider m-0"></div>
          <a href="#" class="dropdown-item px-3 py-2">
            <!--begin::Message-->
            <div class="d-flex">
              <div class="flex-shrink-0">
                <img
                  src="./assets/img/user3-128x128.jpg"
                  alt=""
                  class="img-size-50 rounded-circle me-3"
                />
              </div>
              <div class="flex-grow-1">
                <p class="dropdown-item-title mb-0">
                  Nora Silvester
                  <span class="float-end fs-7 text-warning"><i class="bi bi-star-fill"></i></span>
                </p>
                <p class="fs-7 mb-0 text-truncate">The subject goes here</p>
                <p class="fs-7 text-secondary mb-0">
                  <i class="bi bi-clock-fill me-1"></i> 4 Hours Ago
                </p>
              </div>
            </div>
            <!--end::Message-->
          </a>
          <div class="dropdown-divider m-0"></div>
          <a href="#" class="dropdown-item dropdown-footer text-center py-2 small fw-semibold">
            See all messages
          </a>
        </div>
      </li>
      <!--end::Messages Dropdown Menu-->

      <!--begin::Notifications Dropdown Menu-->
      <li class="nav-item dropdown">
        <a
          class="nav-link rounded-circle d-flex align-items-center justify-content-center position-relative"
          style="width:38px;height:38px"
          data-bs-toggle="dropdown"
          href="#"
          aria-label="Notifications: 15 unread"
        >
          <i class="bi bi-bell-fill fs-6"></i>
          <span
            class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-warning"
            style="font-size:.6rem"
          >
            15
          </span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0 mt-2 p-0">
          <span class="dropdown-item-text fw-semibold small text-secondary px-3 pt-3 pb-2 d-block">
            15 Notifications
          </span>
          <div class="dropdown-divider m-0"></div>
          <a href="#" class="dropdown-item px-3 py-2">
            <i class="bi bi-envelope me-2 text-primary"></i> 4 new messages
            <span class="float-end text-secondary fs-7">3 mins</span>
          </a>
          <div class="dropdown-divider m-0"></div>
          <a href="#" class="dropdown-item px-3 py-2">
            <i class="bi bi-people-fill me-2 text-success"></i> 8 friend requests
            <span class="float-end text-secondary fs-7">12 hours</span>
          </a>
          <div class="dropdown-divider m-0"></div>
          <a href="#" class="dropdown-item px-3 py-2">
            <i class="bi bi-file-earmark-fill me-2 text-warning"></i> 3 new reports
            <span class="float-end text-secondary fs-7">2 days</span>
          </a>
          <div class="dropdown-divider m-0"></div>
          <a href="#" class="dropdown-item dropdown-footer text-center py-2 small fw-semibold">
            See all notifications
          </a>
        </div>
      </li>
      <!--end::Notifications Dropdown Menu-->

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
      <li class="nav-item dropdown user-menu ms-1">
        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center py-1 px-2 rounded" data-bs-toggle="dropdown">
          <img
            src="./assets/img/user2-160x160.jpg"
            class="user-image rounded-circle shadow-sm border"
            style="width:32px;height:32px;object-fit:cover"
            alt="Alexander Pierce"
          />
          <span class="d-none d-md-inline ms-2 fw-medium">Alexander Pierce</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0 mt-2 p-0">
          <!--begin::User Image-->
          <li class="user-header text-bg-primary rounded-top">
            <img
              src="./assets/img/user2-160x160.jpg"
              class="rounded-circle shadow"
              alt="Alexander Pierce"
            />
            <p>
              Alexander Pierce - Web Developer
              <small>Member since Nov. 2023</small>
            </p>
          </li>
          <!--end::User Image-->
          <!--begin::Menu Body-->
          <li class="user-body">
            <!--begin::Row-->
            <div class="row text-center">
              <div class="col-4 border-end">
                <a href="#" class="d-block text-decoration-none">Followers</a>
              </div>
              <div class="col-4 border-end">
                <a href="#" class="d-block text-decoration-none">Sales</a>
              </div>
              <div class="col-4">
                <a href="#" class="d-block text-decoration-none">Friends</a>
              </div>
            </div>
            <!--end::Row-->
          </li>
          <!--end::Menu Body-->
          <!--begin::Menu Footer-->
          <li class="user-footer d-flex gap-2 p-2">
            <a href="#" class="btn btn-outline-secondary btn-sm flex-fill">Profile</a>
            <a href="#" class="btn btn-outline-danger btn-sm flex-fill">Sign out</a>
          </li>
          <!--end::Menu Footer-->
        </ul>
      </li>
      <!--end::User Menu Dropdown-->
    </ul>
    <!--end::End Navbar Links-->
  </div>
  <!--end::Container-->
</nav>
<!--end::Header-->
