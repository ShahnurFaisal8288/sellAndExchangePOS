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
                        <span class="text-muted">may need immediate restocking on this purchase.</span>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-body">

                {{-- Card Header --}}
                <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(135deg, #1e293b, #334155);">
                    <div class="d-flex flex-column align-items-start">
                        <h4 class="card-title fw-bold mb-1 lh-base text-white">
                            <i class="bi bi-file-earmark-text me-2"></i> {{ $editingId ? 'Modify' : 'New' }} Purchase Order
                        </h4>
                        <span class="text-white-50 small">Vendor details, unit-level serials, and costing.</span>
                    </div>
                    <a href="{{ route('purchases.index') }}" class="btn btn-light btn-sm px-3 rounded-pill shadow-sm" wire:navigate>
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                </div>

                <div class="card-body p-4">

                    {{-- Order Information --}}
                    <div class="card border shadow-sm rounded-3 p-4 mb-4 bg-body-secondary border-opacity-25">
                        <h6 class="text-uppercase text-muted fs-7 fw-bold mb-3">
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
                                {{-- Row header: line number + remove --}}
                                <div class="d-flex justify-content-between align-items-center px-4 pt-3">
                                    <span class="badge rounded-pill text-bg-dark px-3 py-2 fw-medium">
                                        <i class="bi bi-box-seam me-1"></i> Line {{ $index + 1 }}
                                    </span>
                                    <button type="button" wire:click="removeItem({{ $index }})"
                                            class="btn btn-outline-danger btn-sm rounded-circle p-1 lh-1" @if(count($items) <= 1) disabled @endif
                                            style="width: 30px; height: 30px;" title="Remove row">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                <div class="card-body p-4 pt-3">
                                    <div class="row g-4">

                                        {{-- Product Identity --}}
                                        <div class="col-lg-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-uppercase text-muted fs-7 fw-bold">Product</span>
                                                <button type="button" wire:click="toggleNewProduct({{ $index }})" class="btn btn-link btn-sm text-decoration-none p-0 small">
                                                    {{ $item['is_new_product'] ? 'Choose existing' : '+ New' }}
                                                </button>
                                            </div>

                                            @if($item['is_new_product'])
                                                <input type="text" wire:model="items.{{ $index }}.product_name" placeholder="Product name"
                                                       class="form-control form-control-sm @error("items.{$index}.product_name") is-invalid @enderror">
                                                @error("items.{$index}.product_name") <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            @else
                                                <select wire:model.live="items.{{ $index }}.product_id"
                                                        class="form-select form-select-sm @error("items.{$index}.product_id") is-invalid @enderror">
                                                    <option value="">Choose Catalog Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error("items.{$index}.product_id") <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            @endif

                                            <hr class="my-3 opacity-25">

                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-1">Qty</span>
                                                    <input type="number" min="1"
                                                           x-model.number="qty"
                                                           wire:model.live.debounce.500ms="items.{{ $index }}.quantity"
                                                           class="form-control form-control-sm text-center fw-bold">
                                                </div>
                                                <div class="col-6">
                                                    <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-1">Cost/Unit</span>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" step="0.01" min="0"
                                                               x-model.number="unitCost"
                                                               wire:model.live.debounce.500ms="items.{{ $index }}.unit_price"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <span class="text-uppercase text-muted fs-7 fw-bold d-block mb-1">Sale Price/Unit</span>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" step="0.01" min="0"
                                                               x-model.number="salePrice"
                                                               wire:model.live.debounce.500ms="items.{{ $index }}.sale_price"
                                                               class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary border-opacity-10 small">
                                                <span class="text-muted">Subtotal</span>
                                                <strong class="font-monospace">$<span x-text="purchaseSubtotal"></span></strong>
                                            </div>
                                        </div>

                                        {{-- Units: IMEI + Color + Country, one card per unit --}}
                                        <div class="col-lg-9 border-start border-secondary border-opacity-10 ps-lg-4">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="text-uppercase text-muted fs-7 fw-bold">
                                                    <i class="bi bi-upc-scan me-1"></i> Units ({{ count($item['imeis']) }})
                                                </span>
                                                <span class="text-muted fs-7">Auto-matches Quantity</span>
                                            </div>

                                            <div class="row g-2">
                                                @foreach($item['imeis'] as $imeiIndex => $imei)
                                                    <div class="col-md-6 col-xl-4">
                                                        <div class="border rounded-3 p-2 h-100 bg-body {{ !empty($imei['locked']) ? 'border-secondary' : 'border-opacity-25' }}">
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="badge text-bg-light border fs-7 px-2">Unit {{ $imeiIndex + 1 }}</span>
                                                                @if(!empty($imei['locked']))
                                                                    <span class="text-muted small" title="Already sold — locked">
                                                                        <i class="bi bi-lock-fill"></i> Sold
                                                                    </span>
                                                                @else
                                                                    <button type="button" wire:click="removeImeiField({{ $index }}, {{ $imeiIndex }})"
                                                                            class="btn btn-link btn-sm text-danger text-decoration-none p-0">
                                                                        <i class="bi bi-x-circle"></i>
                                                                    </button>
                                                                @endif
                                                            </div>

                                                            <input type="text" wire:model="items.{{ $index }}.imeis.{{ $imeiIndex }}.imei_serial"
                                                                   placeholder="IMEI / Serial" class="form-control form-control-sm font-monospace mb-1
                                                                   @error("items.{$index}.imeis.{$imeiIndex}.imei_serial") is-invalid @enderror"
                                                                   @if(!empty($imei['locked'])) disabled @endif>
                                                            @error("items.{$index}.imeis.{$imeiIndex}.imei_serial") <div class="invalid-feedback d-block fs-7">{{ $message }}</div> @enderror

                                                            <select wire:model="items.{{ $index }}.imeis.{{ $imeiIndex }}.color_attribute_id"
                                                                    class="form-select form-select-sm mb-1 @error("items.{$index}.imeis.{$imeiIndex}.color_attribute_id") is-invalid @enderror"
                                                                    @if(!empty($imei['locked'])) disabled @endif>
                                                                <option value="">Color...</option>
                                                                @foreach($this->colors as $color)
                                                                    <option value="{{ $color->id }}">{{ $color->label }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error("items.{$index}.imeis.{$imeiIndex}.color_attribute_id") <div class="invalid-feedback d-block fs-7">{{ $message }}</div> @enderror

                                                            <select wire:model="items.{{ $index }}.imeis.{{ $imeiIndex }}.country_attribute_id"
                                                                    class="form-select form-select-sm"
                                                                    @if(!empty($imei['locked'])) disabled @endif>
                                                                <option value="">Country (optional)</option>
                                                                @foreach($this->countries as $country)
                                                                    <option value="{{ $country->id }}">{{ $country->value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            @error("items.{$index}.imeis") <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @error('items') <div class="text-danger small d-block mb-3">{{ $message }}</div> @enderror

                    {{-- Financial Summary --}}
                    <div class="row justify-content-end">
                        <div class="col-md-5 col-lg-4">
                            <div class="card border-0 shadow-sm rounded-3 p-4 text-white"
                                 style="background: linear-gradient(135deg, #1e293b, #334155);">
                                <h6 class="text-uppercase text-white-50 fs-7 fw-bold mb-3">
                                    <i class="bi bi-calculator me-1"></i> Summary
                                </h6>
                                <div class="d-flex justify-content-between mb-2 small text-white-50">
                                    <span>Gross Total (Purchase):</span>
                                    <span class="fw-bold font-monospace text-white">${{ number_format($total_amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small text-white-50">
                                    <span>Expected Sale Value:</span>
                                    <span class="fw-bold font-monospace text-success">${{ number_format($total_sale_value ?? 0, 2) }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-3 pt-2 border-top border-light border-opacity-10">
                                    <label class="mb-0 small fw-semibold">Amount Paid:</label>
                                    <div class="input-group input-group-sm" style="max-width: 130px;">
                                        <span class="input-group-text bg-transparent text-white border-light border-opacity-25">$</span>
                                        <input type="number" step="0.01" min="0" wire:model.live="paid_amount"
                                               class="form-control text-end fw-bold font-monospace bg-transparent text-white border-light border-opacity-25">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between pt-2 border-top border-light border-opacity-10 mb-1">
                                    <span class="fw-bold small text-danger-emphasis">Balance Due:</span>
                                    <span class="fw-bold font-monospace text-warning">${{ number_format($due_amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between pt-1">
                                    <span class="fw-bold small text-white-50">Expected Profit:</span>
                                    <span class="fw-bold font-monospace text-success">${{ number_format(($total_sale_value ?? 0) - $total_amount, 2) }}</span>
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
