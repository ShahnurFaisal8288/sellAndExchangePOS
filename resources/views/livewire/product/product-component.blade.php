<div>
    {{-- Flash message --}}
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
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3 class="card-title mb-0">
                <i class="bi bi-box-seam me-1"></i> Products
            </h3>
            <a type="button" class="btn btn-primary btn-sm" href="{{ route('products.create') }}">
                <i class="bi bi-plus-lg me-1"></i> Add Product
            </a>
        </div>

        <div class="card-body">
            {{-- Filters --}}
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Search by name, model, or IMEI/serial..."
                        wire:model.live.debounce.400ms="search">
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="categoryFilter">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model.live="brandFilter">
                        <option value="">All Brands</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="lowStockOnly"
                            wire:model.live="lowStockOnly">
                        <label class="form-check-label" for="lowStockOnly">
                            Low stock only
                        </label>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Name / Model</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>IMEI/Serial</th>
                            <th class="text-end">Purchase Price</th>
                            <th class="text-end">Sale Price</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr wire:key="product-{{ $product->id }}">
                                <td>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    @if ($product->model)
                                        <div class="text-muted small">{{ $product->model }}</div>
                                    @endif
                                </td>
                                <td>{{ $product->category->name ?? '—' }}</td>
                                <td>{{ $product->brand->name ?? '—' }}</td>
                                <td>{{ $product->imei_serial ?: '—' }}</td>
                                <td class="text-end">{{ number_format($product->purchase_price, 2) }}</td>
                                <td class="text-end">{{ number_format($product->sale_price, 2) }}</td>
                                <td class="text-center">
                                    <span
                                        class="badge {{ $product->is_low_stock ? 'text-bg-danger' : 'text-bg-secondary' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                    @if ($product->is_low_stock)
                                        <i class="bi bi-exclamation-triangle-fill text-danger ms-1" title="Low stock"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge {{ $product->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('products.edit', $product->id) }}" wire:navigate
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                        wire:click="delete({{ $product->id }})"
                                        wire:confirm="Are you sure you want to delete this product?">
                                        <i class="bi bi-trash"></i>
                                    </button>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    {{-- Create / Edit Modal --}}
    <div class="modal fade @if ($showModal) show d-block @endif" tabindex="-1"
        style="@if ($showModal) background: rgba(0,0,0,0.5); @endif" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form wire:submit="save">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $editingId ? 'Edit Product' : 'Add Product' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror"
                                    wire:model="category_id">
                                    <option value="">Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Brand <span class="text-danger">*</span></label>
                                <select class="form-select @error('brand_id') is-invalid @enderror"
                                    wire:model="brand_id">
                                    <option value="">Select brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model="name">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Model</label>
                                <input type="text" class="form-control @error('model') is-invalid @enderror"
                                    wire:model="model">
                                @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Specification</label>
                                <textarea class="form-control @error('specification') is-invalid @enderror" rows="2"
                                    placeholder="RAM, storage, color, condition, etc."
                                    wire:model="specification"></textarea>
                                @error('specification') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">IMEI / Serial</label>
                                <input type="text" class="form-control @error('imei_serial') is-invalid @enderror"
                                    wire:model="imei_serial">
                                @error('imei_serial') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">Leave blank for items without one (e.g. computer parts).</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Purchase Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0"
                                        class="form-control @error('purchase_price') is-invalid @enderror"
                                        wire:model="purchase_price">
                                </div>
                                @error('purchase_price') <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Sale Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" min="0"
                                        class="form-control @error('sale_price') is-invalid @enderror"
                                        wire:model="sale_price">
                                </div>
                                @error('sale_price') <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                <input type="number" min="0"
                                    class="form-control @error('stock_quantity') is-invalid @enderror"
                                    wire:model="stock_quantity">
                                @error('stock_quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Min Stock Alert <span class="text-danger">*</span></label>
                                <input type="number" min="0"
                                    class="form-control @error('min_stock_alert') is-invalid @enderror"
                                    wire:model="min_stock_alert">
                                @error('min_stock_alert') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancel</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
                            {{ $editingId ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade @if ($deletingId) show d-block @endif" tabindex="-1"
        style="@if ($deletingId) background: rgba(0,0,0,0.5); @endif" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Product</h5>
                    <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product? This cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Cancel</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
