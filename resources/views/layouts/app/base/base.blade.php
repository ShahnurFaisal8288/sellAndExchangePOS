<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE v4 | Dashboard</title>

    <!--begin::Theme Init (prevents flash of incorrect theme on load, #6043)-->
    <script>
      (() => {
        'use strict';
        const STORAGE_KEY = 'lte-theme';
        let stored = null;
        try {
          stored = localStorage.getItem(STORAGE_KEY);
        } catch {
          // localStorage may be unavailable (private mode, sandboxed iframe).
        }
        const prefersDark = globalThis.matchMedia('(prefers-color-scheme: dark)').matches;
        // Mirror the resolution in _scripts.astro: explicit "dark"/"light" win,
        // otherwise ("auto" or unset) fall back to the OS preference.
        let resolved = 'light';
        if (stored === 'dark' || stored === 'light') {
          resolved = stored;
        } else if (prefersDark) {
          resolved = 'dark';
        }
        document.documentElement.setAttribute('data-bs-theme', resolved);
        document.documentElement.style.colorScheme = resolved;
      })();
    </script>
    <!--end::Theme Init-->

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE v4 | Dashboard" />
    <meta name="author" content="ColorlibHQ" />
    <meta
      name="description"
      content="AdminLTE is a free Bootstrap 5 admin dashboard template with almost 50 example pages, built with vanilla JS and designed with accessibility in mind."
    />
    <meta
      name="keywords"
      content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel"
    />
    <!--end::Primary Meta Tags-->

    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="{{ asset('assets/css/adminlte.css') }}" as="style" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
      media="print"
      onload="this.media = 'all'"
    />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('assets/css/adminlte.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->

    <!-- apexcharts -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
      integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0="
      crossorigin="anonymous"
    />

    <!-- jsvectormap -->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
      integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4="
      crossorigin="anonymous"
    />
    @livewireStyles
  </head>
  <!--end::Head-->
  <!--begin::Body-->

  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        @include('layouts.app.base.base_partials.nav')
        @include('layouts.app.base.base_partials.sidebar')
        <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            @yield('content') @if (isset($slot))
                        {{ $slot }}
            @endif
          </div>
        </div>
        </main>

        @include('layouts.app.base.base_partials.footer')
    </div>

    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('assets/js/adminlte.js') }}"></script>
    <!--end::Required Plugin(AdminLTE)-->

    <!--begin::App Init (re-runs after Livewire SPA navigation)-->
    <script>
      (() => {
        'use strict';

        const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
        const Default = {
          scrollbarTheme: 'os-theme-light',
          scrollbarAutoHide: 'leave',
          scrollbarClickScroll: true,
        };

        // Keep a reference to the current OverlayScrollbars instance so we
        // can destroy it before re-initializing (prevents double-binding
        // when this runs again on livewire:navigated).
        let osInstance = null;

        function initOverlayScrollbars() {
          const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
          const isMobile = window.innerWidth <= 992;

          if (osInstance) {
            try {
              osInstance.destroy();
            } catch {
              // instance may already be gone if the node was replaced
            }
            osInstance = null;
          }

          if (
            sidebarWrapper &&
            OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined &&
            !isMobile
          ) {
            osInstance = OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
              scrollbars: {
                theme: Default.scrollbarTheme,
                autoHide: Default.scrollbarAutoHide,
                clickScroll: Default.scrollbarClickScroll,
              },
            });
          }
        }

        // Re-initialize AdminLTE's own bindings (treeview toggle, sidebar
        // push-menu, etc.) in case they were only wired up on first load.
        // AdminLTE 4 typically exposes a Layout/Treeview module on window;
        // guard defensively since availability can vary by build.
        function reinitAdminLTE() {
          try {
            window.adminlte?.Layout?.getOrCreateInstance?.(document.body)?.init?.();
          } catch {
            // no-op: safe to ignore if this API isn't present in your build
          }
        }

        function initAll() {
          initOverlayScrollbars();
          reinitAdminLTE();
        }

        // First load
        document.addEventListener('DOMContentLoaded', initAll);

        // Livewire SPA-style navigation (wire:navigate) swaps the <body>
        // content without a full page reload, so DOMContentLoaded never
        // fires again. Without this, the sidebar dropdown / scrollbars
        // stop working after the first navigation. Harmless no-op if
        // wire:navigate isn't used anywhere in the app.
        document.addEventListener('livewire:navigated', initAll);
      })();
    </script>
    <!--end::App Init-->

    <!-- OPTIONAL SCRIPTS -->

    <!-- sortablejs -->
    <script
      src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
      crossorigin="anonymous"
    ></script>
    <script>
      (() => {
        'use strict';

        let sortableInstance = null;

        function initSortable() {
          const el = document.querySelector('.connectedSortable');
          if (!el) return;

          if (sortableInstance) {
            try {
              sortableInstance.destroy();
            } catch {
              // element may already be gone
            }
            sortableInstance = null;
          }

          sortableInstance = new Sortable(el, {
            group: 'shared',
            handle: '.card-header',
          });

          document
            .querySelectorAll('.connectedSortable .card-header')
            .forEach((cardHeader) => {
              cardHeader.style.cursor = 'move';
            });
        }

        document.addEventListener('DOMContentLoaded', initSortable);
        document.addEventListener('livewire:navigated', initSortable);
      })();
    </script>

    <!-- apexcharts -->
    <script
      src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
      integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8="
      crossorigin="anonymous"
    ></script>
    <!-- ChartJS -->
    <script>
      // NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
      // IT'S ALL JUST JUNK FOR DEMO
      // ++++++++++++++++++++++++++++++++++++++++++
      (() => {
        'use strict';

        let salesChartInstance = null;

        function initSalesChart() {
          const el = document.querySelector('#revenue-chart');
          if (!el) return;

          if (salesChartInstance) {
            try {
              salesChartInstance.destroy();
            } catch {
              // no-op
            }
            salesChartInstance = null;
          }

          const sales_chart_options = {
            series: [
              {
                name: 'Digital Goods',
                data: [28, 48, 40, 19, 86, 27, 90],
              },
              {
                name: 'Electronics',
                data: [65, 59, 80, 81, 56, 55, 40],
              },
            ],
            chart: {
              height: 300,
              type: 'area',
              toolbar: {
                show: false,
              },
            },
            legend: {
              show: false,
            },
            colors: ['#0d6efd', '#20c997'],
            dataLabels: {
              enabled: false,
            },
            stroke: {
              curve: 'smooth',
            },
            xaxis: {
              type: 'datetime',
              categories: [
                '2023-01-01',
                '2023-02-01',
                '2023-03-01',
                '2023-04-01',
                '2023-05-01',
                '2023-06-01',
                '2023-07-01',
              ],
            },
            tooltip: {
              x: {
                format: 'MMMM yyyy',
              },
            },
          };

          salesChartInstance = new ApexCharts(el, sales_chart_options);
          salesChartInstance.render();
        }

        document.addEventListener('DOMContentLoaded', initSalesChart);
        document.addEventListener('livewire:navigated', initSalesChart);
      })();
    </script>

    <!-- jsvectormap -->
    <script
      src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"
      integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y="
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"
      integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY="
      crossorigin="anonymous"
    ></script>


    <!--end::Script-->
    @livewireScripts
  </body>
  <!--end::Body-->
</html>
