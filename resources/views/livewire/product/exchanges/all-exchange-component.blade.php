<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Trade-Ins & Exchanges</h4>
        <a href="{{ route('exchanges.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> New Exchange
        </a>
    </div>

    <select wire:model.live="typeFilter" class="form-select w-auto mb-3">
        <option value="">All Types</option>
        <option value="with_receipt">With Receipt</option>
        <option value="no_receipt">No Receipt</option>
        <option value="warranty">Warranty</option>
        <option value="trade_in">Trade-In</option>
    </select>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th><th>Type</th><th>Old Item</th><th>New Item</th>
                        <th class="text-end">Return Value</th><th class="text-end">New Price</th>
                        <th class="text-end">Diff</th><th>Date</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($exchanges as $ex)
                        <tr>
                            <td>{{ $ex->id }}</td>
                            <td><span class="badge text-bg-secondary">{{ str_replace('_', ' ', $ex->exchange_type) }}</span></td>
                            <td>{{ $ex->oldProduct->name ?? $ex->old_product_description ?? '-' }}</td>
                            <td>{{ $ex->newProduct->name ?? '-' }}</td>
                            <td class="text-end">৳{{ number_format($ex->old_product_return_value, 2) }}</td>
                            <td class="text-end">৳{{ number_format($ex->new_product_price, 2) }}</td>
                            <td class="text-end {{ $ex->additional_payment < 0 ? 'text-success' : '' }}">৳{{ number_format($ex->additional_payment, 2) }}</td>
                            <td>{{ $ex->exchange_date->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('exchanges.edit', $ex) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="{{ route('exchanges.show', $ex) }}" class="btn btn-sm btn-outline-secondary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center text-muted py-4">No exchanges yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($exchanges->hasPages())<div class="card-footer">{{ $exchanges->links() }}</div>@endif
    </div>
</div>
