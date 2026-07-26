<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">All Sales</h4>
        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> New Sale
        </a>
    </div>

    <!-- Search & Filter Card -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body row g-2">
            <div class="col-md-4">
                <input wire:model.live.debounce.400ms="search" class="form-control"
                    placeholder="Search product name, IMEI, invoice, or customer...">
            </div>
            <div class="col-md-3">
                <input type="date" wire:model.live="dateFrom" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="date" wire:model.live="dateTo" class="form-control">
            </div>
        </div>
    </div>

    <!-- Sales Data Table Card -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Products & IMEIs Sold</th>
                            <th>Cashier</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due</th>
                            <th>Date</th>
                            <th class="text-center" style="width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td class="fw-bold font-monospace text-primary">{{ $sale->invoice_no }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $sale->customer->name ?? 'Walk-in Customer' }}</div>
                                    @if(isset($sale->customer->phone))
                                        <small class="text-muted d-block">{{ $sale->customer->phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    <!-- List of sold products and their attached IMEIs -->
                                    <div class="d-flex flex-column gap-1">
                                        @forelse($sale->items as $item)
                                            <div class="py-1">
                                                <span class="fw-semibold">
                                                    {{ $item->product->name ?? 'Product' }} {{ $item->product->model ?? '' }}
                                                </span>
                                                <span class="badge bg-light text-dark border ms-1">Qty:
                                                    {{ $item->quantity }}</span>

                                                <!-- Display sold IMEI serial if attached -->
                                                @if(!empty($item->imei_serial))
                                                    <div class="mt-1">
                                                        <span class="badge bg-info text-dark font-monospace"
                                                            style="font-size: 11px;">
                                                            <i class="bi bi-barcode me-1"></i>IMEI: {{ $item->imei_serial }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @empty
                                            <span class="text-muted small">No items linked</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>{{ $sale->user->name ?? 'N/A' }}</td>
                                <td class="text-end fw-bold">৳{{ number_format($sale->total_amount, 2) }}</td>
                                <td class="text-end text-success">৳{{ number_format($sale->paid_amount, 2) }}</td>
                                <td class="text-end {{ $sale->due_amount > 0 ? 'text-danger fw-bold' : 'text-muted' }}">
                                    ৳{{ number_format($sale->due_amount, 2) }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M, Y') }}</td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-secondary"
                                            title="View Details">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                        <a href="{{ route('sales.edit', $sale) }}" class="btn btn-outline-warning"
                                            title="Edit Sale">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <a href="{{ route('sales.print', $sale) }}" target="_blank"
                                            class="btn btn-outline-primary" title="Print Invoice">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="bi bi-receipt display-6 d-block text-secondary mb-2"></i>
                                    No sales records found matching your query.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($sales->hasPages())
            <div class="card-footer bg-light">{{ $sales->links() }}</div>
        @endif
    </div>
</div>
