<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">All Sales</h4>
        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> New Sale
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body row g-2">
            <div class="col-md-4">
                <input wire:model.live.debounce.400ms="search" class="form-control" placeholder="Search invoice no...">
            </div>
            <div class="col-md-3">
                <input type="date" wire:model.live="dateFrom" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="date" wire:model.live="dateTo" class="form-control">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Cashier</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Paid</th>
                        <th class="text-end">Due</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->invoice_no }}</td>
                            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                            <td>{{ $sale->user->name }}</td>
                            <td class="text-end">৳{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="text-end">৳{{ number_format($sale->paid_amount, 2) }}</td>
                            <td class="text-end {{ $sale->due_amount > 0 ? 'text-danger' : '' }}">
                                ৳{{ number_format($sale->due_amount, 2) }}</td>
                            <td>{{ $sale->sale_date }}</td>
                            <td><a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                            <td>
                                <a href="{{ route('sales.print', $sale) }}" target="_blank"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-printer"></i> Print
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No sales yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($sales->hasPages())
            <div class="card-footer">{{ $sales->links() }}</div>
        @endif
    </div>
</div>
