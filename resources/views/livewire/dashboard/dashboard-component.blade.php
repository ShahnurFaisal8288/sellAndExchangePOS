<div>
<div>

    {{-- Page header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">Dashboard</h4>
            <small class="text-muted">{{ now()->format('l, d M Y') }}</small>
        </div>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> New Sale
        </a>
    </div>

    {{-- Stat cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Today's Sales</div>
                        <div class="fs-5 fw-bold">৳{{ number_format($todaySummary->revenue, 2) }}</div>
                        <div class="text-muted small">{{ $todaySummary->invoice_count }} invoice(s)</div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Collected Today</div>
                        <div class="fs-5 fw-bold">৳{{ number_format($todaySummary->collected, 2) }}</div>
                        <div class="text-danger small">Due: ৳{{ number_format($todaySummary->due, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="bi bi-calendar3 fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">This Month</div>
                        <div class="fs-5 fw-bold">৳{{ number_format($monthRevenue, 2) }}</div>
                        <div class="text-muted small">Total revenue</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Stock Value</div>
                        <div class="fs-5 fw-bold">৳{{ number_format($stockValue, 2) }}</div>
                        <div class="text-muted small">{{ $totalProducts }} active products</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Due / risk row --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Receivable (customers owe)</div>
                    <div class="fs-5 fw-bold text-danger">৳{{ number_format($totalReceivableDue, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Payable (owed to suppliers)</div>
                    <div class="fs-5 fw-bold text-danger">৳{{ number_format($totalPayableDue, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Low Stock Items</div>
                    <div class="fs-5 fw-bold {{ $lowStockCount > 0 ? 'text-warning' : '' }}">{{ $lowStockCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Out of Stock</div>
                    <div class="fs-5 fw-bold {{ $outOfStockCount > 0 ? 'text-danger' : '' }}">{{ $outOfStockCount }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Sales trend chart --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header  fw-semibold">Sales — Last 7 Days</div>
                <div class="card-body" wire:ignore>
                    <canvas id="salesTrendChart" height="110"></canvas>
                </div>
            </div>
        </div>

        {{-- Top products --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header fw-semibold">Top Selling Products (This Month)</div>
                <div class="card-body p-0">
                    @forelse($topProducts as $product)
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">{{ $product->name }} {{ $product->model }}</div>
                                <div class="text-muted small">{{ $product->qty_sold }} sold</div>
                            </div>
                            <div class="text-end fw-semibold">৳{{ number_format($product->revenue, 2) }}</div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">No sales recorded this month yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        {{-- Recent sales --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header  d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Recent Sales</span>
                    <a href="{{ route('sales.index') }}" class="small">View all</a>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $sale->invoice_no }}</div>
                                        <div class="text-muted small">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }} · {{ $sale->cashier_name ?? '—' }}</div>
                                    </td>
                                    <td>{{ $sale->customer_name ?? 'Walk-in' }}</td>
                                    <td class="text-end">৳{{ number_format($sale->total_amount, 2) }}</td>
                                    <td class="text-end">
                                        @if($sale->due_amount > 0)
                                            <span class="badge bg-danger-subtle text-danger">৳{{ number_format($sale->due_amount, 2) }}</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success">Paid</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-4">No sales yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Low stock alerts --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Low Stock Alerts</span>
                    <a href="{{ route('products.index') }}" class="small">View all</a>
                </div>
                <div class="card-body p-0">
                    @forelse($lowStockProducts as $product)
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                            <div>
                                <div class="fw-semibold">{{ $product->name }} {{ $product->model }}</div>
                                <div class="text-muted small">Alert level: {{ $product->min_stock_alert }}</div>
                            </div>
                            <span class="badge {{ $product->stock_quantity == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">
                                {{ $product->stock_quantity }} left
                            </span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-check-circle text-success fs-4 d-block mb-1"></i>
                            All stock levels look healthy.
                        </div>
                    @endforelse
                </div>
                @if($exchangesThisWeek > 0)
                    <div class="card-footer text-muted small">
                        <i class="bi bi-arrow-left-right"></i> {{ $exchangesThisWeek }} exchange(s) processed this week
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Chart script lives inside the component root on purpose — this avoids
         depending on the layout having @stack('scripts'), which is a common
         reason this chart renders blank (the script never loads at all). --}}
    <script>
        (function () {
            function renderSalesTrendChart() {
                const ctx = document.getElementById('salesTrendChart');
                if (!ctx) return;

                // If Livewire re-renders this component, destroy the old chart
                // instance first so we don't stack duplicate charts on the canvas.
                if (ctx._chartInstance) {
                    ctx._chartInstance.destroy();
                }

                ctx._chartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($salesTrend->pluck('label')),
                        datasets: [{
                            label: 'Sales (৳)',
                            data: @json($salesTrend->pluck('total')),
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13,110,253,0.1)',
                            tension: 0.35,
                            fill: true,
                            pointRadius: 3,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { callback: (v) => '৳' + v } }
                        }
                    }
                });
            }

            if (typeof Chart === 'undefined') {
                // Chart.js not loaded yet on this page — load it once, then render.
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js';
                script.onload = renderSalesTrendChart;
                document.head.appendChild(script);
            } else {
                renderSalesTrendChart();
            }
        })();
    </script>

</div><div>

</div>

</div>
