<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <label class="form-label">Exchange Type</label>
            <select wire:model.live="exchangeType" class="form-select">
                <option value="with_receipt">With Receipt (bought here, has invoice)</option>
                <option value="no_receipt">No Receipt (no traceable sale)</option>
                <option value="warranty">Warranty Replacement (same model, no price diff)</option>
                <option value="trade_in">Trade-In (item from outside this shop)</option>
            </select>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">Old / Traded-In Item</div>
                <div class="card-body">

                    @if($exchangeType === 'trade_in')
                        <p class="text-muted small">This item wasn't sold by this shop — create a new catalog row for it.
                        </p>
                        <div class="mb-2">
                            <label class="form-label">Category</label>
                            <select wire:model="newRowCategoryId" class="form-select">
                                <option value="">Select category (e.g. Used Mobile)</option>
                                @foreach($this->categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('newRowCategoryId') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Name</label>
                            <input wire:model="newRowName" class="form-control"
                                placeholder="e.g. Samsung Galaxy A50 (Used)">
                            @error('newRowName') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Model</label>
                            <input wire:model="newRowModel" class="form-control">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Specification / Condition Notes</label>
                            <textarea wire:model="newRowSpecification" class="form-control" rows="2"></textarea>
                        </div>
                    @elseif(in_array($exchangeType, ['with_receipt', 'warranty']))
                        <div class="mb-2">
                            <label class="form-label">Search Product Sold to This Customer</label>
                            <input wire:model.live.debounce.300ms="oldProductSearch" class="form-control"
                                placeholder="Name / IMEI...">
                            @if(count($this->oldProductResults) > 0)
                                <div class="list-group mt-1">
                                    @foreach($this->oldProductResults as $p)
                                        <button type="button" wire:click="selectOldProduct({{ $p->id }})"
                                            class="list-group-item list-group-item-action">
                                            {{ $p->name }} {{ $p->model }}
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                            @error('oldProductId') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <div class="mb-2">
                            <label class="form-label">Description (item isn't in catalog)</label>
                            <textarea wire:model="oldProductDescription" class="form-control" rows="2"
                                placeholder="Describe the item brought in..."></textarea>
                        </div>
                    @endif

                    <div class="mb-2">
                        <label class="form-label">Condition</label>
                        <select wire:model="condition" class="form-select">
                            <option value="resellable">Resellable</option>
                            <option value="damaged">Damaged (won't return to stock)</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Return Value / Credit (৳)</label>
                        <input type="number" step="0.01" min="0" wire:model.live="oldProductReturnValue"
                            class="form-control">
                        @error('oldProductReturnValue') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">New Item (Customer Takes Home)</div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label">Customer (optional — leave blank for walk-in)</label>
                        <select wire:model="customerId" class="form-select">
                            <option value="">Walk-in Customer</option>
                            @foreach($this->customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Search Product</label>
                        <input wire:model.live.debounce.300ms="newProductSearch" class="form-control"
                            placeholder="Name / IMEI...">
                        @if(count($this->newProductResults) > 0)
                            <div class="list-group mt-1">
                                @foreach($this->newProductResults as $p)
                                    <button type="button" wire:click="selectNewProduct({{ $p->id }})"
                                        class="list-group-item list-group-item-action d-flex justify-content-between">
                                        <span>{{ $p->name }} {{ $p->model }}</span>
                                        <span class="text-muted">Stock: {{ $p->stock_quantity }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @error('newProductId') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Price (৳)</label>
                        <input type="number" step="0.01" min="0" wire:model.live="newProductPrice" class="form-control">
                        @error('newProductPrice') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <hr>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>New Item Price</span>
                        <strong>৳{{ number_format((float) ($newProductPrice ?: 0), 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Old Item Credit</span>
                        <strong>৳{{ number_format((float) ($oldProductReturnValue ?: 0), 2) }}</strong>
                    </div>

                    <div class="d-flex justify-content-between fs-5">
                        <span>{{ (float) $additionalPayment >= 0 ? 'Customer Pays' : 'Refund to Customer' }}</span>
                        <strong class="{{ (float) $additionalPayment < 0 ? 'text-success' : '' }}">
                            ৳{{ number_format(abs((float) ($additionalPayment ?: 0)), 2) }}
                        </strong>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Notes</label>
                        <textarea wire:model="notes" class="form-control" rows="2"></textarea>
                    </div>

                    <button wire:click="confirmExchange" wire:loading.attr="disabled"
                        class="btn btn-primary w-100 mt-3">
                        <span wire:loading.remove wire:target="confirmExchange">Confirm Exchange</span>
                        <span wire:loading wire:target="confirmExchange">Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
