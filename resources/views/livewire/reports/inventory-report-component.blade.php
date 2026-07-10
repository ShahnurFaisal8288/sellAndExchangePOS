<div>
    <div class="card mb-3">
        <div class="card-header">
            <h3 class="card-title mb-0">Inventory Report</h3>
        </div>
        <div class="card-body">
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <input type="text" wire:model.live.debounce.400ms="search" class="form-control" placeholder="Search name, model, IMEI...">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="categoryId" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select wire:model.live="brandId" class="form-select">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-center">
                    <div class="form-check">
                        <input type="checkbox" wire:model.live="lowStockOnly" class="form-check-input" id="lowStockOnly">
                        <label class="form-check-label" for="lowStockOnly">Low stock only</label>
                    </div>
                </div>
            </div>

            <div class="row mb-3 g-2">
                <div class="col-md-4">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Products</div>
                        <div class="fs-5 fw-bold">{{ $summary->total_products ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Stock Value (Cost)</div>
                        <div class="fs-5 fw-bold">৳{{ number_format($summary->stock_value_cost ?? 0, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-2 text-center">
                        <div class="text-muted small">Stock Value (Retail)</div>
                        <div class="fs-5 fw-bold">৳{{ number_format($summary->stock_value_retail ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>IMEI/Serial</th>
                            <th class="text-end">Stock</th>
                            <th class="text-end">Cost</th>
                            <th class="text-end">Sale Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            <tr class="{{ $product->stock_quantity <= $product->min_stock_alert ? 'table-warning' : '' }}">
                                <td>{{ $product->name }} <span class="text-muted small">{{ $product->model }}</span></td>
                                <td>{{ $product->category->name ?? '—' }}</td>
                                <td>{{ $product->brand->name ?? '—' }}</td>
                                <td>{{ $product->imei_serial ?? '—' }}</td>
                                <td class="text-end">{{ $product->stock_quantity }}</td>
                                <td class="text-end">৳{{ number_format($product->purchase_price, 2) }}</td>
                                <td class="text-end">৳{{ number_format($product->sale_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">No products match these filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $products->links() }}
        </div>
    </div>
</div>
