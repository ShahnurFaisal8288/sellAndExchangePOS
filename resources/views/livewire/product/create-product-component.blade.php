<div>
    <div class="card card-primary card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="bi bi-plus-lg me-1"></i> Add Product
            </h3>
            <a href="{{ route('products.index') }}" wire:navigate class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Products
            </a>
        </div>

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                    <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Country Code</label>
                    <input type="text" wire:model="country_code" maxlength="10" placeholder="e.g. US, JP, CN"
                        class="form-control text-uppercase @error('country_code') is-invalid @enderror">
                    @error('country_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Color</label>
                    <input type="text" wire:model="color" placeholder="e.g. Black, Midnight Blue"
                        class="form-control @error('color') is-invalid @enderror">
                    @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Purchase Price <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" min="0" wire:model="purchase_price"
                            class="form-control @error('purchase_price') is-invalid @enderror">
                    </div>
                    @error('purchase_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sale Price <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" min="0" wire:model="sale_price"
                            class="form-control @error('sale_price') is-invalid @enderror">
                    </div>
                    @error('sale_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                {{-- <div class="col-md-4">
                    <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                    <input type="number" min="0" wire:model="stock_quantity"
                        class="form-control @error('stock_quantity') is-invalid @enderror">
                    @error('stock_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div> --}}
                <div class="col-md-4">
                    <label class="form-label">Min Stock Alert <span class="text-danger">*</span></label>
                    <input type="number" min="0" wire:model="min_stock_alert"
                        class="form-control @error('min_stock_alert') is-invalid @enderror">
                    @error('min_stock_alert') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('products.index') }}" wire:navigate class="btn btn-secondary">Cancel</a>
            <button type="button" wire:click="save" class="btn btn-primary" wire:loading.attr="disabled"
                wire:target="save">
                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
                Save Product
            </button>
        </div>
    </div>
</div>
