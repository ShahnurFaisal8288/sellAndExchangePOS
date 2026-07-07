<div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-file-earmark-medical me-2"></i> {{ $editingId ? 'Modify' : 'New' }} Purchase Order
                    </h3>
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary btn-sm" wire:navigate>
                        Back to List
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Source Type</label>
                            <select wire:model.live="source_type" class="form-select">
                                <option value="supplier">Supplier Channel</option>
                                <option value="customer_trade_in">Customer Trade-In</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Supplier/Vendor</label>
                            <select wire:model="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                                <option value="">Select Vendor</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Invoice Reference</label>
                            <input type="text" wire:model="invoice_no" class="form-control @error('invoice_no') is-invalid @enderror">
                            @error('invoice_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Purchase Date</label>
                            <input type="date" wire:model="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror">
                            @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center my-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-list-nested me-1"></i> Line Items</h6>
                        <button type="button" wire:click="addItem" class="btn btn-dark btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Append Item Row
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45%;">Product Line Item</th>
                                    <th style="width: 15%;">Quantity</th>
                                    <th style="width: 20%;">Unit Cost</th>
                                    <th style="width: 15%;">Subtotal</th>
                                    <th style="width: 5%;" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                    <tr wire:key="item-row-{{ $index }}">
                                        <td>
                                            <select wire:model.live="items.{{ $index }}.product_id" class="form-select @error("items.{$index}.product_id") is-invalid @enderror">
                                                <option value="">Choose Catalog Product</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" min="1" wire:model.live.debounce.500ms="items.{{ $index }}.quantity" class="form-control text-center">
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" min="0" wire:model.live.debounce.500ms="items.{{ $index }}.unit_price" class="form-control">
                                            </div>
                                        </td>
                                        <td>
                                            <span class="form-control bg-light fw-bold text-end">${{ number_format($item['subtotal'] ?? 0, 2) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" wire:click="removeItem({{ $index }})" class="btn btn-outline-danger btn-sm" @if(count($items) <= 1) disabled @endif>
                                                <i class="bi bi-dash-circle"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @error('items') <div class="text-danger small d-block mb-3">{{ $message }}</div> @enderror

                    <div class="row justify-content-end mt-4">
                        <div class="col-md-4">
                            <div class="card p-3 bg-light border-0">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Gross Total:</span>
                                    <span class="fw-bold">${{ number_format($total_amount, 2) }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="mb-0">Amount Paid:</label>
                                    <div class="input-group" style="max-width: 150px运行;">
                                        <span class="input-group-text p-1 px-2">$</span>
                                        <input type="number" step="0.01" min="0" wire:model.live="paid_amount" class="form-control form-control-sm text-end fw-bold">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-2">
                                    <span class="fw-bold text-danger">Balance Due:</span>
                                    <span class="fw-bold text-danger">${{ number_format($due_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary" wire:navigate>Discard</a>
                    <button type="button" class="btn btn-primary" wire:click="save">Process Order</button>
                </div>
            </div>
        </div>
    </div>
</div>
