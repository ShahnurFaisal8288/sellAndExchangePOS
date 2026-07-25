<div>
    <div class="app-content py-4">
        <div class="container-fluid">

            {{-- Low Stock Alert --}}
            @if($this->lowStockProducts->isNotEmpty())
                <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4 rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-4 text-warning me-3"></i>
                    <div>
                        <strong>Low Stock Notice:</strong>
                        <span>{{ $this->lowStockProducts->pluck('name')->implode(', ') }}</span>
                        <span>may need immediate restocking on this purchase.</span>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-body">
                {{-- Card Header --}}
                <div class="card-header bg-body border-bottom border-secondary border-opacity-10 py-3 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title fw-bold mb-0">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i> {{ $editingId ? 'Modify' : 'New' }} Purchase Order
                        </h4>
                        <p class="text-muted small mb-0">Manage vendor details, line items, serial numbers, and costs.</p>
                    </div>
                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" wire:navigate>
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                </div>

                <div class="card-body p-4">

                    {{-- Header Info Section --}}
                    <div class="card border shadow-sm rounded-3 p-4 mb-4 bg-body-secondary border-opacity-25">
                        <h6 class="text-uppercase text-muted fs-7 fw-bold mb-3 tracking-wide">
                            <i class="bi bi-info-circle me-1 text-primary"></i> Order Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0 fw-semibold small">Supplier / Vendor</label>
                                    <button type="button" wire:click="toggleNewSupplier" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold">
                                        {{ $is_new_supplier ? 'Choose existing' : '+ New Supplier' }}
                                    </button>
                                </div>

                                @if($is_new_supplier)
                                    <input type="text" wire:model="new_supplier_name" placeholder="Supplier name"
                                           class="form-control form-control-sm mb-2 @error('new_supplier_name') is-invalid @enderror">
                                    @error('new_supplier_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                                    <input type="text" wire:model="new_supplier_phone" placeholder="Phone, e.g. +880 1712-345678"
                                           class="form-control form-control-sm">
                                @else
                                    <select wire:model="supplier_id" class="form-select form-select-sm @error('supplier_id') is-invalid @enderror">
                                        <option value="">Select Vendor</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Invoice Reference</label>
                                <input type="text" value="{{ $invoice_no }}" class="form-control form-control-sm font-monospace text-muted" readonly disabled>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Purchase Date</label>
                                <input type="date" wire:model="purchase_date" class="form-control form-control-sm @error('purchase_date') is-invalid @enderror">
                                @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Line Items Header --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-list-columns-reverse me-1 text-primary"></i> Line Items
                        </h5>
                        <button type="button" wire:click="addItem" class="btn btn-dark btn-sm px-3 rounded-pill shadow-sm">
                            <i class="bi bi-plus-lg me-1"></i> Append Item Row
                        </button>
                    </div>

                    {{-- Line Item Cards --}}
                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach($items as $index => $item)
                            <div class="card border shadow-sm rounded-3 bg-body-secondary border-opacity-25" wire:key="item-row-{{ $index }}"
                                 x-data="{
                                     qty: {{ (float) ($item['quantity'] ?? 0) }},
                                     unitCost: {{ (float) ($item['unit_price'] ?? 0) }},
                                     salePrice: {{ (float) ($item['sale_price'] ?? 0) }},
                                     get purchaseSubtotal() { return (this.qty * this.unitCost).toFixed(2); },
                                     get saleSubtotal() { return (this.qty * this.salePrice).toFixed(2); }
                                 }"
                            >
                                <div class="card-body p-4">
                                    <div class="row g-4 align-items-center">

                                        {{-- Product Identity --}}
                                        <div class="col-lg-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-uppercase text-muted fs-7 fw-bold">Product</span>
                                                <button type="button" wire:click="toggleNewProduct({{ $index }})" class="btn btn-link btn-sm text-decoration-none p-0 small">
                                                    {{ $item['is_new_product'] ? 'Choose existing' : '+ New Product' }}
                                                </button>
                                            </div>

                                            @if($item['is_new_product'])
                                                <input type="text" wire:model="items.{{ $index }}.product_name" placeholder="Product name"
                                                       class="form-control form-control-sm mb-2 @error("items.{$index}.product_name") is-invalid @enderror">
                                                @error("items.{$index}.product_name") <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <input type="text" wire:model="items.{{ $index }}.country_code" placeholder="Country (US, JP)"
                                                               maxlength="10" class="form-control form-control-sm text-uppercase">
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="text" wire:model="items.{{ $index }}.color" placeholder="Color"
                                                               class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                            @else
                                                <select wire:model.live="items.{{ $index }}.product_id"
                                                        class="form-select form-select-sm mb-2 @error("items.{$index}.product_id") is-invalid @enderror">
                                                    <option value="">Choose Catalog Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error("items.{$index}.product_id") <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

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
                                            @endif
                                        </div>

                                        {{-- IMEI / Serial Section --}}
                                        <div class="col-lg-3 border-start border-end border-secondary border-opacity-10 px-lg-3">
                                            <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-2">IMEI / Serials</span>
                                            <div class="d-flex flex-column gap-1 mb-2">
                                                @foreach($item['imeis'] as $imeiIndex => $imei)
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" wire:model="items.{{ $index }}.imeis.{{ $imeiIndex }}.imei_serial"
                                                               placeholder="Unit {{ $imeiIndex + 1 }}" class="form-control font-monospace"
                                                               @if(!empty($imei['locked'])) disabled @endif>
                                                        @if(!empty($imei['locked']))
                                                            <span class="input-group-text text-muted" title="Already sold or returned — locked">
                                                                <i class="bi bi-lock-fill"></i>
                                                            </span>
                                                        @else
                                                            <button type="button" wire:click="removeImeiField({{ $index }}, {{ $imeiIndex }})"
                                                                    class="btn btn-outline-danger">&times;</button>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" wire:click="addImeiField({{ $index }})" class="btn btn-outline-secondary btn-sm w-100 rounded-pill fs-7">
                                                <i class="bi bi-plus"></i> Add IMEI
                                            </button>
                                            @error("items.{$index}.imeis") <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>

                                        {{-- Quantity + Pricing + Remove --}}
                                        <div class="col-lg-5">
                                            <div class="row g-2 mb-3">
                                                <div class="col-4">
                                                    <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-1">Qty</span>
                                                    <input type="number" min="1"
                                                           x-model.number="qty"
                                                           wire:model.live.debounce.500ms="items.{{ $index }}.quantity"
                                                           class="form-control form-control-sm text-center fw-bold">
                                                </div>
                                                <div class="col-4">
                                                    <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-1">Cost (Unit)</span>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" step="0.01" min="0"
                                                               x-model.number="unitCost"
                                                               wire:model.live.debounce.500ms="items.{{ $index }}.unit_price"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-1">Sale (Unit)</span>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" step="0.01" min="0"
                                                               x-model.number="salePrice"
                                                               wire:model.live.debounce.500ms="items.{{ $index }}.sale_price"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-10">
                                                <div class="small text-muted">
                                                    Subtotal: <strong class="font-monospace" x-text="'$' + purchaseSubtotal"></strong>
                                                </div>
                                                <button type="button" wire:click="removeItem({{ $index }})"
                                                        class="btn btn-outline-danger btn-sm rounded-circle p-1 lh-1" @if(count($items) <= 1) disabled @endif
                                                        style="width: 30px; height: 30px;" title="Remove row">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('items') <div class="text-danger small d-block mb-3">{{ $message }}</div> @enderror

                    {{-- Financial Summary Section --}}
                    <div class="row justify-content-end">
                        <div class="col-md-5 col-lg-4">
                            <div class="card border shadow-sm rounded-3 p-4 bg-body-secondary border-opacity-25">
                                <h6 class="text-uppercase text-muted fs-7 fw-bold mb-3 tracking-wide">
                                    <i class="bi bi-calculator me-1 text-primary"></i> Summary
                                </h6>
                                <div class="d-flex justify-content-between mb-2 small text-muted">
                                    <span>Gross Total (Purchase):</span>
                                    <span class="fw-bold font-monospace">${{ number_format($total_amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small text-success">
                                    <span>Expected Sale Value:</span>
                                    <span class="fw-bold font-monospace">${{ number_format($total_sale_value ?? 0, 2) }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3 pt-2 border-top border-secondary border-opacity-10">
                                    <label class="mb-0 small fw-semibold">Amount Paid:</label>
                                    <div class="input-group input-group-sm" style="max-width: 130px;">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" wire:model.live="paid_amount"
                                               class="form-control text-end fw-bold font-monospace">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between pt-2 border-top border-secondary border-opacity-10 mb-1">
                                    <span class="fw-bold text-danger small">Balance Due:</span>
                                    <span class="fw-bold text-danger font-monospace">${{ number_format($due_amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between pt-1">
                                    <span class="fw-bold text-primary small">Expected Profit:</span>
                                    <span class="fw-bold text-primary font-monospace">${{ number_format(($total_sale_value ?? 0) - $total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Card Footer --}}
                <div class="card-footer bg-body border-top border-secondary border-opacity-10 py-3 px-4 d-flex justify-content-end gap-2">
                    <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary px-4 rounded-pill shadow-sm" wire:navigate>Discard</a>
                    <button type="button" class="btn btn-primary px-4 rounded-pill shadow-sm" wire:click="save">
                        <i class="bi bi-check-circle me-1"></i> Process Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
