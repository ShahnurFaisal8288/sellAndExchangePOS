<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand">
    <!--begin::Brand Link-->
    <a href="" class="brand-link d-flex align-items-center">
      <!--begin::Brand Mark (icon-based, no external logo file needed)-->
      <span
        class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white me-2"
        style="width:34px;height:34px;flex-shrink:0"
      >
        <i class="bi bi-shop fs-6"></i>
      </span>
      <!--end::Brand Mark-->
      <!--begin::Brand Text-->
      <span class="brand-text fw-semibold lh-1">
        Mobile<span class="text-primary">Exchange</span>
        <br />
        <small class="fw-normal opacity-75" style="font-size:.7rem">Point of Sale</small>
      </span>
      <!--end::Brand Text-->
    </a>
    <!--end::Brand Link-->
  </div>
  <!--end::Sidebar Brand-->

  <!--begin::Sidebar Wrapper-->
  <div class="sidebar-wrapper">
    <nav class="mt-2" aria-label="Main navigation">
      <!--begin::Sidebar Menu-->
      <ul
        class="nav sidebar-menu flex-column"
        data-lte-toggle="treeview"
        data-accordion="false"
        id="navigation"
      >
        <!--begin::MAIN-->
        <li class="nav-header">MAIN</li>
        <li class="nav-item">
          <a href="" class="nav-link">
            <i class="nav-icon bi bi-speedometer2"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <!--end::MAIN-->

        <!--begin::CATALOG-->
        <li class="nav-header">CATALOG</li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-box-seam"></i>
            <p>
              Products
              @if(($lowStockCount ?? 0) > 0)
                <span class="nav-badge badge text-bg-danger me-3">{{ $lowStockCount }}</span>
              @endif
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('products.index') }}" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>All Products</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="" class="nav-link">
                <i class="nav-icon bi bi-exclamation-circle text-warning"></i>
                <p>Low Stock Alerts</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('categories.index') }}" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Categories</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('brands.index') }}" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Brands</p>
              </a>
            </li>
          </ul>
        </li>
        <!--end::CATALOG-->

        <!--begin::PURCHASES-->
        <li class="nav-header">PURCHASES</li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-cart-plus"></i>
            <p>
              Purchases
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('purchases.index') }}" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>All Purchases</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('purchases.create') }}" class="nav-link">
                <i class="nav-icon bi bi-plus-circle"></i>
                <p>New Purchase</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('suppliers.index') }}" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Suppliers</p>
              </a>
            </li>
          </ul>
        </li>
        <!--end::PURCHASES-->

        <!--begin::SALES-->
        <li class="nav-header">SALES</li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-receipt-cutoff"></i>
            <p>
              Sales
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>All Sales</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="" class="nav-link">
                <i class="nav-icon bi bi-plus-circle"></i>
                <p>New Sale</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('customers.index') }}" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Customers</p>
              </a>
            </li>
          </ul>
        </li>
        <!--end::SALES-->

        <!--begin::EXCHANGES-->
        <li class="nav-header">EXCHANGES</li>
        <li class="nav-item">
          <a href="" class="nav-link">
            <i class="nav-icon bi bi-arrow-left-right"></i>
            <p>Trade-Ins &amp; Exchanges</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="" class="nav-link">
            <i class="nav-icon bi bi-plus-circle"></i>
            <p>New Exchange</p>
          </a>
        </li>
        <!--end::EXCHANGES-->

        <!--begin::REPORTS-->
        <li class="nav-header">REPORTS</li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon bi bi-graph-up-arrow"></i>
            <p>
              Reports
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Sales Report</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Purchase Report</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="" class="nav-link">
                <i class="nav-icon bi bi-circle"></i>
                <p>Inventory Report</p>
              </a>
            </li>
          </ul>
        </li>
        <!--end::REPORTS-->

        <!--begin::ADMINISTRATION-->
        {{-- @if(auth()->user()?->role === 'admin') --}}
          <li class="nav-header">ADMINISTRATION</li>
          <li class="nav-item">
            <a href="" class="nav-link">
              <i class="nav-icon bi bi-people"></i>
              <p>Staff &amp; Users</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="" class="nav-link">
              <i class="nav-icon bi bi-gear"></i>
              <p>Settings</p>
            </a>
          </li>
        {{-- @endif --}}
        <!--end::ADMINISTRATION-->
      </ul>
      <!--end::Sidebar Menu-->

      <!--begin::Quick New Sale CTA (bottom of sidebar)-->
      <div class="p-3 mt-3 border-top border-secondary border-opacity-25">
        <a
          href=""
          class="btn btn-sm btn-primary w-100 d-flex align-items-center justify-content-center gap-2"
        >
          <i class="bi bi-lightning-charge-fill" aria-hidden="true"></i>
          New Sale
        </a>
      </div>
      <!--end::Quick New Sale CTA-->
    </nav>
  </div>
  <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->
