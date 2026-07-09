
@php
    // Define which route patterns belong to which section.
    // Add/remove patterns here as you add more routes — everything else
    // (menu-open + active classes) is derived automatically below.
    $sections = [
        'products'   => ['products.*', 'categories.*', 'brands.*'],
        'purchases'  => ['purchases.*', 'suppliers.*'],
        'sales'      => ['sales.*', 'customers.*'],
        'exchanges'  => ['exchanges.*'],
        'reports'    => ['reports.*'],
        'admin'      => ['staff.*', 'users.*', 'settings.*'],
    ];

    $activeSection = null;
    foreach ($sections as $key => $patterns) {
        if (request()->routeIs($patterns)) {
            $activeSection = $key;
            break;
        }
    }
@endphp

<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <!--begin::Sidebar Brand-->
  <div class="sidebar-brand">
    <a href="{{ route('dashboard') }}" class="brand-link d-flex align-items-center">
      <span
        class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white me-2"
        style="width:34px;height:34px;flex-shrink:0"
      >
        <i class="bi bi-shop fs-6"></i>
      </span>
      <span class="brand-text fw-semibold lh-1">
        Mobile<span class="text-primary">Exchange</span>
        <br />
        <small class="fw-normal opacity-75" style="font-size:.7rem">Point of Sale</small>
      </span>
    </a>
  </div>
  <!--end::Sidebar Brand-->

  <!--begin::Sidebar Wrapper-->
  <div class="sidebar-wrapper">
    <nav class="mt-2" aria-label="Main navigation">
      <ul
        class="nav sidebar-menu flex-column"
        data-lte-toggle="treeview"
        data-accordion="false"
        id="navigation"
      >
        <!--begin::MAIN-->
        <li class="nav-header">MAIN</li>
        <li class="nav-item">
          <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="nav-icon bi bi-speedometer2"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <!--end::MAIN-->

        <!--begin::CATALOG-->
        <li class="nav-header">CATALOG</li>
        <li class="nav-item {{ $activeSection === 'products' ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ $activeSection === 'products' ? 'active' : '' }}">
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
              <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.index') ? 'active' : '' }}">
                <i class="nav-icon bi bi-circle"></i>
                <p>All Products</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('products.low-stock') }}" class="nav-link">
                <i class="nav-icon bi bi-exclamation-circle text-warning"></i>
                <p>Low Stock Alerts</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="nav-icon bi bi-circle"></i>
                <p>Categories</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('brands.index') }}" class="nav-link {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                <i class="nav-icon bi bi-circle"></i>
                <p>Brands</p>
              </a>
            </li>
          </ul>
        </li>
        <!--end::CATALOG-->

        <!--begin::PURCHASES-->
        <li class="nav-header">PURCHASES</li>
        <li class="nav-item {{ $activeSection === 'purchases' ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ $activeSection === 'purchases' ? 'active' : '' }}">
            <i class="nav-icon bi bi-cart-plus"></i>
            <p>
              Purchases
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.index') ? 'active' : '' }}">
                <i class="nav-icon bi bi-circle"></i>
                <p>All Purchases</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('purchases.create') }}" class="nav-link {{ request()->routeIs('purchases.create') ? 'active' : '' }}">
                <i class="nav-icon bi bi-plus-circle"></i>
                <p>New Purchase</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="nav-icon bi bi-circle"></i>
                <p>Suppliers</p>
              </a>
            </li>
          </ul>
        </li>
        <!--end::PURCHASES-->

        <!--begin::SALES-->
        <li class="nav-header">SALES</li>
        <li class="nav-item {{ $activeSection === 'sales' ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ $activeSection === 'sales' ? 'active' : '' }}">
            <i class="nav-icon bi bi-receipt-cutoff"></i>
            <p>
              Sales
              <i class="nav-arrow bi bi-chevron-right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{ route('sales.index') }}" class="nav-link ">
                <i class="nav-icon bi bi-circle"></i>
                <p>All Sales</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('sales.create') }}" class="nav-link">
                <i class="nav-icon bi bi-plus-circle"></i>
                <p>New Sale</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
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
          <a href="{{ route('exchanges.index') }}" class="nav-link">
            <i class="nav-icon bi bi-arrow-left-right"></i>
            <p>Trade-Ins &amp; Exchanges</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('exchanges.create') }}" class="nav-link">
            <i class="nav-icon bi bi-plus-circle"></i>
            <p>New Exchange</p>
          </a>
        </li>
        <!--end::EXCHANGES-->

        <!--begin::REPORTS-->
        <li class="nav-header">REPORTS</li>
        <li class="nav-item {{ $activeSection === 'reports' ? 'menu-open' : '' }}">
          <a href="#" class="nav-link {{ $activeSection === 'reports' ? 'active' : '' }}">
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
        {{-- @if(auth()->user()?->role === 'admin')
          <li class="nav-header">ADMINISTRATION</li>
          <li class="nav-item">
            <a href="{{ route('staff.index') }}" class="nav-link {{ request()->routeIs('staff.*') ? 'active' : '' }}">
              <i class="nav-icon bi bi-people"></i>
              <p>Staff &amp; Users</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
              <i class="nav-icon bi bi-gear"></i>
              <p>Settings</p>
            </a>
          </li>
        @endif --}}
        <!--end::ADMINISTRATION-->
      </ul>
      <!--end::Sidebar Menu-->

      <!--begin::Quick New Sale CTA (bottom of sidebar)-->
      <div class="p-3 mt-3 border-top border-secondary border-opacity-25">
        <a
          href="{{ route('sales.create') }}"
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
