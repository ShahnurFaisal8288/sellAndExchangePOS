<div>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h3 fw-bold mb-1">Profit & Loss Report</h1>
            <p class="text-muted small mb-0">Product-wise profit and loss, grouped by invoice.</p>
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
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Total Sale</span>
                        <h3 class="fw-bold mt-1 mb-0">৳{{ number_format($report['summary']['total_sales'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-primary bg-opacity-15 text-primary rounded-3">
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                </div>
                <p class="text-muted small mt-2 mb-0">Net Sales (After Discount): ৳{{ number_format($report['summary']['net_sales'], 2) }}</p>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Discount Given</span>
                        <h3 class="fw-bold text-warning mt-1 mb-0">৳{{ number_format($report['summary']['total_discount_given'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-warning bg-opacity-15 text-warning rounded-3">
                        <i class="bi bi-tags fs-4"></i>
                    </div>
                </div>
                <p class="text-muted small mt-2 mb-0">Total trade-in / discounts applied</p>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Total Profit</span>
                        <h3 class="fw-bold text-success mt-1 mb-0">৳{{ number_format($report['summary']['total_profit'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-success bg-opacity-15 text-success rounded-3">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                </div>
                <p class="text-muted small mt-2 mb-0">Positive earnings from operations</p>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Total Loss</span>
                        <h3 class="fw-bold text-danger mt-1 mb-0">৳{{ number_format($report['summary']['total_loss'], 2) }}</h3>
                    </div>
                    <div class="p-3 bg-danger bg-opacity-15 text-danger rounded-3">
                        <i class="bi bi-graph-down-arrow fs-4"></i>
                    </div>
                </div>
                <p class="text-muted small mt-2 mb-0">Deficit/Negative margins if any</p>
            </div>
        </div>
    </div>

    <!-- Product-wise Invoice Breakdown -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold text-dark mb-0 fs-6">Daily Profit & Loss (Invoice)</h5>
            <span class="text-muted small">
                @if($startDate && $endDate)
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                @else
                    All Time
                @endif
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th class="py-2 px-3">SN</th>
                            <th class="py-2 px-3">SKU / IMEI</th>
                            <th class="py-2 px-3">Item</th>
                            <th class="text-center py-2">Qty</th>
                            <th class="text-end py-2">Unit Price</th>
                            <th class="text-end py-2">Total</th>
                            <th class="text-end py-2">Cost</th>
                            <th class="text-end py-2 px-3">Profit / Loss</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($report['invoices'] as $invoice)
                            {{-- Invoice header row --}}
                            <tr class="table-light">
                                <td colspan="8" class="py-2 px-3">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center">
                                        <span class="fw-bold text-primary">
                                            <i class="bi bi-receipt me-1"></i>Inv: {{ $invoice['invoice_no'] }}
                                        </span>
                                        <span class="fw-semibold text-body">{{ $invoice['customer_name'] }}</span>
                                        <span class="text-muted small">Date: {{ \Carbon\Carbon::parse($invoice['sale_date'])->format('d M Y') }}</span>
                                    </div>
                                </td>
                            </tr>

                            @foreach($invoice['items'] as $i => $item)
                                <tr class="border-bottom border-secondary border-opacity-10">
                                    <td class="py-2 px-3 text-muted">{{ $i + 1 }}</td>
                                    <td class="py-2 px-3">
                                        <span class="font-monospace small">{{ $item['sku'] }}</span>
                                        @if($item['imei'])
                                            <div class="text-muted font-monospace" style="font-size: 11px;">{{ $item['imei'] }}</div>
                                        @endif
                                    </td>
                                    <td class="py-2 px-3">{{ $item['name'] }}</td>
                                    <td class="text-center py-2">{{ $item['qty'] }}</td>
                                    <td class="text-end py-2 font-monospace">৳{{ number_format($item['unit_price'], 2) }}</td>
                                    <td class="text-end py-2 font-monospace fw-semibold">৳{{ number_format($item['total'], 2) }}</td>
                                    <td class="text-end py-2 font-monospace text-muted">৳{{ number_format($item['cost'], 2) }}</td>
                                    <td class="text-end py-2 px-3 font-monospace fw-bold {{ $item['profit_loss'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        ৳{{ number_format($item['profit_loss'], 2) }}
                                    </td>
                                </tr>
                            @endforeach

                            {{-- Invoice subtotal row --}}
                            <tr class="bg-body-secondary bg-opacity-50 border-bottom border-secondary border-opacity-25">
                                <td colspan="5" class="text-end py-2 px-3 fw-bold text-muted small">-Total-</td>
                                <td class="text-end py-2 font-monospace fw-bold">৳{{ number_format($invoice['invoice_total'], 2) }}</td>
                                <td class="text-end py-2 font-monospace fw-bold text-muted">৳{{ number_format($invoice['invoice_cost'], 2) }}</td>
                                <td class="text-end py-2 px-3 font-monospace fw-bold {{ $invoice['invoice_profit_loss'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    ৳{{ number_format($invoice['invoice_profit_loss'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox display-6 d-block text-secondary opacity-50 mb-2"></i>
                                    No sales found for the selected date range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(!empty($report['invoices']))
                        <tfoot>
                            <tr class="table-dark">
                                <td colspan="5" class="text-end py-3 px-3 fw-bold">Grand Total</td>
                                <td class="text-end py-3 font-monospace fw-bold">৳{{ number_format($report['summary']['net_sales'], 2) }}</td>
                                <td class="text-end py-3 font-monospace fw-bold">৳{{ number_format($report['summary']['total_cost_of_goods'], 2) }}</td>
                                <td class="text-end py-3 px-3 font-monospace fw-bold {{ $report['summary']['net_profit_loss'] >= 0 ? 'text-success' : 'text-danger' }}">
                                    ৳{{ number_format($report['summary']['net_profit_loss'], 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
