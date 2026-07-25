<div>
    {{-- Flash Messages --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title mb-0">
                <i class="bi bi-cash-coin me-1"></i> Pay Supplier Due
            </h3>
        </div>

        <div class="card-body">
            <div class="row g-4">
                {{-- LEFT: purchases with outstanding due --}}
                <div class="col-lg-7">
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search invoice or supplier..."
                               wire:model.live.debounce.400ms="search">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Supplier</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Paid</th>
                                    <th class="text-end">Due</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($purchases as $purchase)
                                    <tr wire:key="purchase-{{ $purchase->id }}"
                                        class="{{ $selectedPurchaseId === $purchase->id ? 'table-primary' : '' }}">
                                        <td class="fw-semibold">{{ $purchase->invoice_no }}</td>
                                        <td>{{ $purchase->supplier?->name ?? 'Walk-in / No Supplier' }}</td>
                                        <td class="text-end">৳{{ number_format($purchase->total_amount, 2) }}</td>
                                        <td class="text-end text-success">৳{{ number_format($purchase->paid_amount, 2) }}</td>
                                        <td class="text-end text-danger fw-bold">৳{{ number_format($purchase->due_amount, 2) }}</td>
                                        <td class="text-center">
                                            <button type="button" wire:click="selectPurchase({{ $purchase->id }})"
                                                    class="btn btn-sm {{ $selectedPurchaseId === $purchase->id ? 'btn-primary' : 'btn-outline-primary' }}">
                                                {{ $selectedPurchaseId === $purchase->id ? 'Selected' : 'Pay' }}
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No purchases with outstanding due.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $purchases->links() }}
                    </div>
                </div>

                {{-- RIGHT: payment form --}}
                <div class="col-lg-5">
                    <div class="card border shadow-sm">
                        <div class="card-body">
                            @if (!$selectedPurchaseId)
                                <div class="text-center text-muted py-5">
                                    <i class="bi bi-arrow-left-circle fs-1 d-block mb-2"></i>
                                    Select a purchase from the list to record a payment.
                                </div>
                            @else
                                <h6 class="fw-bold mb-3">
                                    Paying: {{ $selectedPurchase->invoice_no }}
                                    <button type="button" wire:click="clearSelection" class="btn btn-link btn-sm float-end p-0">
                                        Cancel
                                    </button>
                                </h6>

                                <div class="mb-3 p-2 rounded border bg-light-subtle">
                                    <div class="d-flex justify-content-between small">
                                        <span>Supplier:</span>
                                        <span class="fw-semibold">{{ $selectedPurchase->supplier?->name ?? 'Walk-in / No Supplier' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span>Total:</span>
                                        <span>৳{{ number_format($selectedPurchase->total_amount, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between small">
                                        <span>Already Paid:</span>
                                        <span class="text-success">৳{{ number_format($selectedPurchase->paid_amount, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold border-top mt-1 pt-1">
                                        <span>Outstanding Due:</span>
                                        <span class="text-danger">৳{{ number_format($selectedPurchase->due_amount, 2) }}</span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Payment Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text">৳</span>
                                        <input type="number" step="0.01" min="0.01"
                                               wire:model="amount"
                                               class="form-control @error('amount') is-invalid @enderror">
                                    </div>
                                    @error('amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Payment Date</label>
                                    <input type="date" wire:model="payment_date"
                                           class="form-control @error('payment_date') is-invalid @enderror">
                                    @error('payment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Payment Method</label>
                                    <select wire:model="method" class="form-select">
                                        <option value="cash">Cash</option>
                                        <option value="bank">Bank Transfer</option>
                                        <option value="mobile_banking">Mobile Banking (bKash/Nagad)</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Note (optional)</label>
                                    <textarea wire:model="note" rows="2" class="form-control"
                                              placeholder="Reference no, cheque no, etc."></textarea>
                                </div>

                                <button type="button" wire:click="save" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle me-1"></i> Record Payment
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
