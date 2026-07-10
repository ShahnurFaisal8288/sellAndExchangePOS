<div>
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title mb-0">Purchase Report</h3>
        </div>
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Source</label>
                    <select wire:model.live="sourceType" class="form-select">
                        <option value="">All</option>
                        <option value="supplier">Supplier</option>
                        <option value="customer_trade_in">Customer Trade-In</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3 g-2">
                <div class="col-md-4">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Invoices</div>
                        <div class="fs-5 fw-bold">{{ $summary->total_invoices ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Total Purchases</div>
                        <div class="fs-5 fw-bold">৳{{ number_format($summary->total_amount ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Due</div>
                        <div class="fs-5 fw-bold text-danger">৳{{ number_format($summary->total_due ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Source</th>
                            <th>Supplier</th>
                            <th>Recorded By</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->invoice_no }}</td>
                                <td>{{ $purchase->purchase_date }}</td>
                                <td>
                                    @if($purchase->source_type === 'customer_trade_in')
                                        <span class="badge text-bg-info">Trade-In</span>
                                    @else
                                        <span class="badge text-bg-secondary">Supplier</span>
                                    @endif
                                </td>
                                <td>{{ $purchase->supplier->name ?? '—' }}</td>
                                <td>{{ $purchase->user->name }}</td>
                                <td class="text-end">৳{{ number_format($purchase->total_amount, 2) }}</td>
                                <td class="text-end">৳{{ number_format($purchase->paid_amount, 2) }}</td>
                                <td class="text-end">৳{{ number_format($purchase->due_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No purchases found for the selected filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $purchases->links() }}
        </div>
    </div>
</div>
