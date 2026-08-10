
<div>
    <div class="app-content py-4">
        <div class="container-fluid">

            {{-- Page Header --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-body">

                <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #1e293b, #334155);">

                    <div class="d-flex flex-column align-items-start">
                        <h4 class="card-title fw-bold mb-1 lh-base text-white">
                            <i class="bi bi-cart-check me-2"></i>
                            Purchase Management
                        </h4>

                        <span class="text-white-50 small">
                            Manage purchase transactions, suppliers, payments, and stock records.
                        </span>
                    </div>

                    <a href="{{ route('purchases.create') }}"
                       class="btn btn-light btn-sm px-3 rounded-pill shadow-sm"
                       wire:navigate>
                        <i class="bi bi-plus-lg me-1"></i>
                        New Purchase
                    </a>
                </div>

                <div class="card-body p-4">

                    {{-- Flash Message --}}
                    @if (session()->has('message'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 d-flex align-items-center mb-4"
                             role="alert">
                            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                            <span>{{ session('message') }}</span>
                        </div>
                    @endif

                    {{-- Search & Summary --}}
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">

                        <div class="position-relative" style="max-width: 360px; width: 100%;">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>

                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   class="form-control ps-5 rounded-pill"
                                   placeholder="Search invoice or supplier...">
                        </div>

                        <div class="text-muted small">
                            <i class="bi bi-receipt me-1"></i>
                            {{ $purchases->total() }} purchase{{ $purchases->total() === 1 ? '' : 's' }}
                        </div>
                    </div>

                    {{-- Purchase Table --}}
                    <div class="card border shadow-sm rounded-3 overflow-hidden border-opacity-25">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3">Invoice</th>
                                        <th class="py-3">Supplier</th>
                                        <th class="py-3">Purchase Date</th>
                                        <th class="py-3 text-end">Total</th>
                                        <th class="py-3 text-end">Paid</th>
                                        <th class="py-3 text-end">Due</th>
                                        <th class="px-4 py-3 text-end">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($purchases as $purchase)
                                        <tr wire:key="purchase-{{ $purchase->id }}">

                                            {{-- Invoice --}}
                                            <td class="px-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge text-bg-dark rounded-pill px-3 py-2">
                                                        <i class="bi bi-receipt me-1"></i>
                                                        {{ $purchase->invoice_no }}
                                                    </span>
                                                </div>
                                            </td>

                                            {{-- Supplier --}}
                                            <td>
                                                <div class="fw-semibold">
                                                    {{ $purchase->supplier?->name ?? 'N/A' }}
                                                </div>

                                                <div class="text-muted small">
                                                    {{ ucfirst(str_replace('_', ' ', $purchase->source_type ?? '')) }}
                                                </div>
                                            </td>

                                            {{-- Date --}}
                                            <td>
                                                <span class="text-muted">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    {{ $purchase->purchase_date?->format('Y-m-d') ?? 'N/A' }}
                                                </span>
                                            </td>

                                            {{-- Total --}}
                                            <td class="text-end">
                                                <span class="fw-semibold font-monospace">
                                                    ${{ number_format($purchase->total_amount, 2) }}
                                                </span>
                                            </td>

                                            {{-- Paid --}}
                                            <td class="text-end">
                                                <span class="fw-semibold font-monospace text-success">
                                                    ${{ number_format($purchase->paid_amount, 2) }}
                                                </span>
                                            </td>

                                            {{-- Due --}}
                                            <td class="text-end">
                                                <span class="fw-semibold font-monospace
                                                    {{ $purchase->due_amount > 0 ? 'text-danger' : 'text-muted' }}">
                                                    ${{ number_format($purchase->due_amount, 2) }}
                                                </span>
                                            </td>

                                            {{-- Actions --}}
                                            <td class="px-4 text-end">
                                                <div class="d-inline-flex align-items-center gap-1">

                                                    {{-- Return Purchase --}}
                                                    @if ($purchase->source_type !== 'supplier_return')
                                                        <a href="{{ route('purchase_return', $purchase->id) }}"
                                                           class="btn btn-sm btn-outline-warning rounded-circle"
                                                           title="Return Purchase"
                                                           wire:navigate>
                                                            <i class="bi bi-arrow-return-left"></i>
                                                        </a>
                                                    @endif

                                                    {{-- Edit Purchase --}}
                                                    <a href="{{ route('purchases.edit', $purchase->id) }}"
                                                       class="btn btn-sm btn-outline-primary rounded-circle"
                                                       title="Edit Purchase"
                                                       wire:navigate>
                                                        <i class="bi bi-pencil"></i>
                                                    </a>

                                                    {{-- Delete Purchase --}}
                                                    <button type="button"
                                                            wire:click="delete({{ $purchase->id }})"
                                                            wire:confirm="Are you sure you want to delete this purchase? This will also reverse the associated stock and remove its IMEI records."
                                                            class="btn btn-sm btn-outline-danger rounded-circle"
                                                            title="Delete Purchase">
                                                        <i class="bi bi-trash"></i>
                                                    </button>

                                                </div>
                                            </td>

                                        </tr>
                                    @empty

                                        <tr>
                                            <td colspan="7" class="text-center py-5">

                                                <div class="text-muted">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>

                                                    <h6 class="fw-semibold mb-1">
                                                        No Purchases Found
                                                    </h6>

                                                    <p class="small mb-3">
                                                        No purchase records match your current search.
                                                    </p>

                                                    @if (filled($search))
                                                        <button type="button"
                                                                wire:click="$set('search', '')"
                                                                class="btn btn-sm btn-outline-secondary rounded-pill">
                                                            <i class="bi bi-x-circle me-1"></i>
                                                            Clear Search
                                                        </button>
                                                    @else
                                                        <a href="{{ route('purchases.create') }}"
                                                           class="btn btn-sm btn-primary rounded-pill"
                                                           wire:navigate>
                                                            <i class="bi bi-plus-lg me-1"></i>
                                                            Record New Purchase
                                                        </a>
                                                    @endif
                                                </div>

                                            </td>
                                        </tr>

                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    @if ($purchases->hasPages())
                        <div class="d-flex justify-content-end mt-4">
                            {{ $purchases->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

