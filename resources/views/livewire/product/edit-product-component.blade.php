<div>
    <div class="card card-primary card-outline">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="bi bi-pencil-square me-1"></i> Edit Product
            </h3>

            <a href="{{ route('products.index') }}" wire:navigate class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Products
            </a>
        </div>

        <form wire:submit="update">
            <div class="card-body">
                <div class="row g-3">

                    {{-- Category --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Category <span class="text-danger">*</span>
                        </label>

                        <select
                            class="form-select @error('category_id') is-invalid @enderror"
                            wire:model="category_id"
                        >
                            <option value="">Select Category</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('category_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Brand --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Brand
                        </label>

                        <select
                            class="form-select"
                            wire:model="brand_id"
                        >
                            <option value="">Select Brand</option>

                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('brand_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div class="col-md-8">
                        <label class="form-label">
                            Product Name <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            wire:model="name"
                        >

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Model --}}
                    <div class="col-md-4">
                        <label class="form-label">
                            Model
                        </label>

                        <input
                            type="text"
                            class="form-control @error('model') is-invalid @enderror"
                            wire:model="model"
                        >

                        @error('model')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Specification --}}
                    <div class="col-12">
                        <label class="form-label">
                            Specification
                        </label>

                        <textarea
                            rows="3"
                            class="form-control @error('specification') is-invalid @enderror"
                            wire:model="specification"
                            placeholder="RAM, Storage, Processor, Color, Condition etc."
                        ></textarea>

                        @error('specification')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- IMEI --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            IMEI / Serial
                        </label>

                        <input
                            type="text"
                            class="form-control @error('imei_serial') is-invalid @enderror"
                            wire:model="imei_serial"
                        >

                        @error('imei_serial')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <div class="form-text">
                            Leave blank for products without an IMEI or serial number.
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Status <span class="text-danger">*</span>
                        </label>

                        <select
                            class="form-select @error('status') is-invalid @enderror"
                            wire:model="status"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Purchase Price --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Purchase Price <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">৳</span>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control @error('purchase_price') is-invalid @enderror"
                                wire:model="purchase_price"
                            >
                        </div>

                        @error('purchase_price')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Sale Price --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Sale Price <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">৳</span>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control @error('sale_price') is-invalid @enderror"
                                wire:model="sale_price"
                            >
                        </div>

                        @error('sale_price')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Stock Quantity --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Stock Quantity <span class="text-danger">*</span>
                        </label>

                        <input
                            type="number"
                            min="0"
                            class="form-control @error('stock_quantity') is-invalid @enderror"
                            wire:model="stock_quantity"
                        >

                        @error('stock_quantity')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Minimum Stock Alert --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Minimum Stock Alert <span class="text-danger">*</span>
                        </label>

                        <input
                            type="number"
                            min="0"
                            class="form-control @error('min_stock_alert') is-invalid @enderror"
                            wire:model="min_stock_alert"
                        >

                        @error('min_stock_alert')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-end gap-2">

                <a
                    href="{{ route('products.index') }}"
                    wire:navigate
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                    wire:loading.attr="disabled"
                    wire:target="update"
                >
                    <span
                        wire:loading
                        wire:target="update"
                        class="spinner-border spinner-border-sm me-1"
                    ></span>

                    <i class="bi bi-check-circle me-1" wire:loading.remove wire:target="update"></i>

                    Update Product
                </button>

            </div>
        </form>
    </div>
</div>
