<div>
    @if($sale->due_amount > 0)
        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
            <i class="bi bi-cash-coin"></i> Record Payment
        </button>

        <div class="modal fade" id="recordPaymentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Record Payment — {{ $sale->invoice_no }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Current Due: <strong class="text-danger">৳{{ number_format($sale->due_amount, 2) }}</strong></p>

                        <div class="mb-3">
                            <label class="form-label">Amount (৳)</label>
                            <input type="number" step="0.01" min="0.01" wire:model="amount" class="form-control">
                            @error('amount') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select wire:model="paymentMethod" class="form-select">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="mobile_banking">Mobile Banking</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes (optional)</label>
                            <input type="text" wire:model="notes" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button
                            type="button"
                            wire:click="save"
                            wire:loading.attr="disabled"
                            class="btn btn-primary"
                        >
                            Save Payment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <span class="badge text-bg-success">Fully Paid</span>
    @endif
</div>
