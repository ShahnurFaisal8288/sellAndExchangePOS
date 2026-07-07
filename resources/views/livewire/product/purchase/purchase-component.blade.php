<div>
    <div class="app-content">
        <div class="container-fluid">
            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="bi bi-cart-check me-2"></i> All Purchases</h3>
                    <a href="{{ route('purchases.create') }}" class="btn btn-primary btn-sm" wire:navigate>
                        <i class="bi bi-plus-lg"></i> Record New Purchase
                    </a>
                </div>

                <div class="card-body">
                    <div class="mb-3" style="max-width: 320px;">
                        <input type="text" wire:model.live.debounce.300ms="search"
                               class="form-control" placeholder="Search Invoice or Supplier...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Invoice No</th>
                                    <th>Supplier</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Paid</th>
                                    <th>Due</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchases as $purchase)
                                    <tr wire:key="purchase-{{ $purchase->id }}">
                                        <td><strong>{{ $purchase->invoice_no }}</strong></td>
                                        <td>{{ $purchase->supplier->name ?? 'N/A' }} <small class="text-muted">({{ ucfirst(str_replace('_', ' ', $purchase->source_type)) }})</small></td>
                                        <td>{{ $purchase->purchase_date->format('Y-m-d') }}</td>
                                        <td>${{ number_format($purchase->total_amount, 2) }}</td>
                                        <td class="text-success">${{ number_format($purchase->paid_amount, 2) }}</td>
                                        <td class="{{ $purchase->due_amount > 0 ? 'text-danger' : 'text-muted' }}">
                                            ${{ number_format($purchase->due_amount, 2) }}
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn btn-sm btn-outline-primary" wire:navigate>
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button wire:click="delete({{ $purchase->id }})"
                                                    wire:confirm="Are you sure you want to delete this purchase order?"
                                                    class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No purchases cataloged.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
