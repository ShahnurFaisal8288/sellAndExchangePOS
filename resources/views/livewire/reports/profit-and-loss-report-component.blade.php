<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Profit & Loss Report</h1>
            <p class="text-muted small mb-0">Track total sales, discounts given, profits, and losses.</p>
        </div>

        <!-- Filter Controls -->
        <div class="d-flex flex-wrap align-items-center gap-2">
            <select wire:model.live="filterType" class="form-select form-select-sm w-auto">
                <option value="today">Today</option>
                <option value="this_week">This Week</option>
                <option value="this_month">This Month</option>
                <option value="this_year">This Year</option>
                <option value="all">All Time</option>
                <option value="custom">Custom Range</option>
            </select>

            @if($filterType === 'custom')
                <input type="date" wire:model.live="startDate" class="form-control form-control-sm w-auto">
                <span class="text-muted small">to</span>
                <input type="date" wire:model.live="endDate" class="form-control form-control-sm w-auto">
            @endif
        </div>
    </div>

    <!-- Summary Metric Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Sales Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Total Sale</span>
                        <h3 class="fw-bold mt-1 mb-0">${{ number_format($report['total_sales'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-15 text-primary rounded-3">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                </div>
                <p class="text-muted small mt-2 mb-0">Net Sales (After Discount): ${{ number_format($report['net_sales'], 2) }}</p>
            </div>
        </div>

        <!-- Discount Given Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Discount Given</span>
                        <h3 class="fw-bold text-warning mt-1 mb-0">${{ number_format($report['total_discount_given'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-15 text-warning rounded-3">
                        <i class="bi bi-tags fs-4"></i>
                    </div>
                </div>
                <p class="text-muted small mt-2 mb-0">Total trade-in / discounts applied</p>
            </div>
        </div>

        <!-- Total Profit Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Total Profit</span>
                        <h3 class="fw-bold text-success mt-1 mb-0">${{ number_format($report['total_profit'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-success bg-opacity-15 text-success rounded-3">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                </div>
                <p class="text-muted small mt-2 mb-0">Positive earnings from operations</p>
            </div>
        </div>

        <!-- Total Loss Card -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Total Loss</span>
                        <h3 class="fw-bold text-danger mt-1 mb-0">${{ number_format($report['total_loss'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-danger bg-opacity-15 text-danger rounded-3">
                        <i class="bi bi-graph-down-arrow fs-4"></i>
                    </div>
                </div>
                <p class="text-muted small mt-2 mb-0">Deficit/Negative margins if any</p>
            </div>
        </div>
    </div>

    <!-- Detailed Financial Breakdown Box -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light py-3">
            <h5 class="card-title fw-bold text-dark mb-0 fs-6">Financial Statement Breakdown</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-column gap-3">
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <span class="text-secondary small">Gross Sales Revenue</span>
                    <span class="fw-semibold">${{ number_format($report['total_sales'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <span class="text-secondary small">Total Discount Given</span>
                    <span class="fw-semibold text-warning">-${{ number_format($report['total_discount_given'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <span class="text-secondary small">Net Sales</span>
                    <span class="fw-semibold">${{ number_format($report['net_sales'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                    <span class="text-secondary small">Cost of Goods Sold (COGS)</span>
                    <span class="fw-semibold text-danger">-${{ number_format($report['total_cost_of_goods'], 2) }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3">
                    <span class="fw-bold text-dark">Net Balance (Profit / Loss)</span>
                    <span class="fw-bold fs-5 {{ $report['net_profit_loss'] >= 0 ? 'text-success' : 'text-danger' }}">
                        ${{ number_format($report['net_profit_loss'], 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
