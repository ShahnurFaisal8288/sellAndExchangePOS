<div>
    <div class="app-content py-4">
        <div class="container-fluid">
            <div class="card border-danger shadow-sm rounded-4 overflow-hidden bg-body">

                {{-- Card Header --}}
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center py-3 px-4">
                    <h3 class="card-title mb-0 fs-5 fw-bold">
                        <i class="bi bi-arrow-return-left me-2"></i> Process Purchase Return
                    </h3>
                    <a href="{{ route('purchases.index') }}" class="btn btn-light btn-sm px-3 rounded-pill text-dark fw-semibold" wire:navigate>
                        Back to List
                    </a>
                </div>

                {{-- Card Body --}}
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Supplier/Vendor</label>
                            <input type="text" value="{{ $supplier_name }}" class="form-control form-control-sm font-monospace text-muted" readonly disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Return Invoice Reference</label>
                            <input type="text" value="{{ $invoice_no }}" class="form-control form-control-sm font-monospace text-muted" readonly disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">Return Date</label>
                            <input type="date" wire:model="return_date" class="form-control form-control-sm @error('return_date') is-invalid @enderror">
                            @error('return_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Original Purchase Context Alert --}}
                    <div class="alert alert-secondary border-0 bg-body-secondary border-opacity-25 d-flex flex-wrap gap-5 py-3 px-4 rounded-3 mb-4">
                        <div>
                            <span class="text-muted small d-block">Original Total</span>
                            <strong class="font-monospace">৳{{ number_format($original_total, 2) }}</strong>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Already Paid</span>
                            <strong class="font-monospace">৳{{ number_format($original_paid, 2) }}</strong>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Currently Due</span>
                            <strong class="text-danger font-monospace">৳{{ number_format($original_due, 2) }}</strong>
                        </div>
                    </div>

                    <hr class="border-secondary opacity-10 my-4">

                    <h6 class="mb-3 fw-bold text-danger">
                        <i class="bi bi-list-nested me-1"></i> Items to Return <span class="text-muted fw-normal small">(Stock will be deducted)</span>
                    </h6>

                    @if(empty($items))
                        <div class="alert alert-info border-0 shadow-sm rounded-3">All items on this purchase have already been fully returned.</div>
                    @else
                        <div class="d-flex flex-column gap-3 mb-4">
                            @foreach($items as $index => $item)
                                <div class="card border shadow-sm rounded-3 bg-body-secondary border-opacity-25" wire:key="return-row-{{ $index }}">
                                    <div class="card-body p-4">
                                        <div class="row g-4 align-items-center">

                                            {{-- Product Info --}}
                                            <div class="col-md-3">
                                                <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-1">Product</span>
                                                <div class="fw-semibold text-body">{{ $item['product_name'] }}</div>
                                                @if(!empty($item['country_code']) || !empty($item['color']))
                                                    <div class="d-flex gap-1 mt-1">
                                                        @if(!empty($item['country_code']))
                                                            <span class="badge bg-secondary bg-opacity-25 text-body border px-2">{{ strtoupper($item['country_code']) }}</span>
                                                        @endif
                                                        @if(!empty($item['color']))
                                                            <span class="badge bg-secondary bg-opacity-25 text-body border px-2">{{ $item['color'] }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="text-muted fs-7 mt-1">Max returnable: {{ $item['max_returnable'] }}</div>
                                            </div>

                                            {{-- IMEI Selection --}}
                                            <div class="col-md-3 border-start border-end border-secondary border-opacity-10 px-md-3">
                                                <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-1">IMEI / Serial</span>
                                                @if(!empty($item['available_imeis']))
                                                    <div class="d-flex flex-wrap gap-1 mb-1">
                                                        @foreach($item['available_imeis'] as $imeiId => $imeiSerial)
                                                            <button type="button"
                                                                    wire:click="toggleImei({{ $index }}, {{ $imeiId }})"
                                                                    class="btn btn-sm font-monospace fs-7 {{ in_array($imeiId, $item['selected_imei_ids']) ? 'btn-danger shadow-sm' : 'btn-outline-secondary' }}">
                                                                {{ $imeiSerial }}
                                                            </button>
                                                        @endforeach
                                                    </div>
                                                    <div class="text-muted fs-7">Click to select units.</div>
                                                @else
                                                    <span class="text-muted fs-7">— (non-serialized)</span>
                                                @endif
                                            </div>

                                            {{-- Return Qty --}}
                                            <div class="col-md-2">
                                                <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-1">Return Qty</span>
                                                <input type="number" min="1" max="{{ $item['max_returnable'] }}"
                                                       wire:model.live.debounce.300ms="items.{{ $index }}.quantity"
                                                       class="form-control form-control-sm text-center fw-bold @error("items.{$index}.quantity") is-invalid @enderror"
                                                       @if(!empty($item['available_imeis'])) readonly @endif>
                                                @error("items.{$index}.quantity") <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            {{-- Unit Cost --}}
                                            <div class="col-md-2">
                                                <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-1">Unit Cost</span>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">৳</span>
                                                    <input type="text" value="{{ number_format($item['unit_price'], 2) }}" class="form-control font-monospace text-muted" readonly disabled>
                                                </div>
                                            </div>

                                            {{-- Subtotal --}}
                                            <div class="col-md-2 text-md-end">
                                                <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-1">Subtotal</span>
                                                <span class="fw-bold font-monospace text-body fs-6">৳{{ number_format($item['subtotal'] ?? 0, 2) }}</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @error('items') <div class="text-danger small d-block mb-3">{{ $message }}</div> @enderror

                    {{-- Summary Box --}}
                    <div class="row justify-content-end mt-4">
                        <div class="col-md-5 col-lg-4">
                            <div class="card border shadow-sm rounded-3 p-4 bg-body-secondary border-opacity-25">
                                <h6 class="text-uppercase text-muted fs-7 fw-bold mb-3 tracking-wide">
                                    <i class="bi bi-calculator me-1 text-danger"></i> Return Summary
                                </h6>
                                <div class="d-flex justify-content-between mb-2 small text-muted">
                                    <span>Total Return Value:</span>
                                    <span class="fw-bold font-monospace text-body">৳{{ number_format($total_amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small text-warning">
                                    <span>Due Cancelled:</span>
                                    <span class="fw-bold font-monospace">৳{{ number_format($this->dueCancelled, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between pt-2 border-top border-secondary border-opacity-10">
                                    <span class="fw-bold text-danger small">Cash Refund to You:</span>
                                    <span class="fw-bold text-danger font-monospace">৳{{ number_format($this->cashRefund, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Card Footer --}}
                <div class="card-footer bg-body border-top border-secondary border-opacity-10 py-3 px-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary px-4 rounded-pill shadow-sm" wire:navigate>Cancel</a>
                    <button type="button" class="btn btn-danger px-4 rounded-pill shadow-sm" wire:click="save" @if(empty($items)) disabled @endif>
                        <i class="bi bi-check-circle me-1"></i> Confirm Return & Deduct Stock
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
