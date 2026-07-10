<div>
    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Sales Report</h3>
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
                    <label class="form-label">Payment Method</label>
                    <select wire:model.live="paymentMethod" class="form-select">
                        <option value="">All</option>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile_banking">Mobile Banking</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3 g-2">
                <div class="col-md-3">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Invoices</div>
                        <div class="fs-5 fw-bold">{{ $summary->total_invoices ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Total Sales</div>
                        <div class="fs-5 fw-bold">৳{{ number_format($summary->total_amount ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Discount Given</div>
                        <div class="fs-5 fw-bold">৳{{ number_format($summary->total_discount ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3">
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
                            <th>Customer</th>
                            <th>Cashier</th>
                            <th>Method</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                            <tr>
                                <td>{{ $sale->invoice_no }}</td>
                                <td>{{ $sale->sale_date }}</td>
                                <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                <td>{{ $sale->user->name }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td>
                                <td class="text-end">৳{{ number_format($sale->total_amount, 2) }}</td>
                                <td class="text-end">৳{{ number_format($sale->paid_amount, 2) }}</td>
                                <td class="text-end">৳{{ number_format($sale->due_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-4">No sales found for the selected filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $sales->links() }}
        </div>
    </div>
</div>
