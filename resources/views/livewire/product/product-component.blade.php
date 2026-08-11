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
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h3 class="card-title mb-0">
                <i class="bi bi-box-seam me-1"></i> Products
            </h3>
            <a href="{{ route('products.create') }}" wire:navigate class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add Product
            </a>
        </div>

        <div class="card-body">
            {{-- Cleaned Filters: Search & Low Stock --}}
            <div class="row g-2 mb-3 align-items-center">
                <div class="col-md-8 col-lg-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search by name, model, or IMEI/serial..."
                            wire:model.live.debounce.400ms="search">
                    </div>
                </div>
                <div class="col-md-4 col-lg-3">
                    <div class="form-check form-switch m-0 ps-5">
                        <input class="form-check-input" type="checkbox" role="switch" id="lowStockOnly"
                            wire:model.live="lowStockOnly">
                        <label class="form-check-label fw-semibold" for="lowStockOnly">
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
                            <th>IMEI / Serial</th>
                            <th class="text-end">Purchase Price</th>
                            <th class="text-end">Sale Price</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            @php
                                // Check stock dynamically from unsold IMEIs if present, fallback to stock_quantity column
                                $currentStock = $product->total_imei_count > 0
        ? $product->available_stock
        : $product->stock_quantity;

    $isLowStock = $currentStock <= $product->min_stock_alert;
                            @endphp
                            <tr wire:key="product-{{ $product->id }}">
                                <td>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    @if ($product->model)
                                        <div class="text-muted small">{{ $product->model }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if ($product->purchaseItemImeis->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($product->purchaseItemImeis->take(3) as $imei)
                                                <span
                                                    class="badge text-bg-light border font-monospace">{{ $imei->imei_serial }}</span>
                                            @endforeach
                                            @if ($product->purchaseItemImeis->count() > 3)
                                                <span
                                                    class="badge text-bg-secondary">+{{ $product->purchaseItemImeis->count() - 3 }}
                                                    more</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">৳{{ number_format($product->purchase_price, 2) }}</td>
                                <td class="text-end">৳{{ number_format($product->sale_price, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $isLowStock ? 'text-bg-danger' : 'text-bg-secondary' }}">
                                        {{ $currentStock }}
                                    </span>
                                    @if ($isLowStock)
                                        <i class="bi bi-exclamation-triangle-fill text-danger ms-1" title="Low stock alert"></i>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge {{ $product->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('products.edit', $product) }}" wire:navigate
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No products matching your criteria were found.
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
</div>
