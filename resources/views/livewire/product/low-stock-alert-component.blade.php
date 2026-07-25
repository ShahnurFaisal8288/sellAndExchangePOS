<div>
    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Low Stock Inventory Alerts
            </h4>
            <p class="text-muted small mb-0">Products reaching or below their minimum reorder thresholds.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input
                    type="text"
                    wire:model.live.debounce.400ms="search"
                    class="form-control border-start-0 ps-0 shadow-none"
                    placeholder="Search name, model, IMEI..."
                >
            </div>
        </div>
    </div>

    {{-- Alert Banner / Quick Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 bg-danger bg-opacity-10 rounded-3">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-danger fw-semibold small text-uppercase tracking-wide">Completely Out of Stock</span>
                        <h3 class="fw-bold mb-0 text-danger mt-1">{{ $totalOutStock }}</h3>
                    </div>
                    <div class="rounded-circle bg-danger text-white p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-x-circle-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 bg-warning bg-opacity-10 rounded-3">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-warning-emphasis fw-semibold small text-uppercase tracking-wide">Total Low Stock Items</span>
                        <h3 class="fw-bold mb-0 text-warning-emphasis mt-1">{{ $products->total() }}</h3>
                    </div>
                    <div class="rounded-circle bg-warning text-dark p-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-box-seam-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr class="text-muted small text-uppercase fw-semibold">
                            <th class="ps-4 py-3">Product Info</th>
                            <th class="py-3">Category</th>
                            <th class="text-center py-3">Current Stock</th>
                            <th class="text-center py-3">Alert Threshold</th>
                            <th class="text-center py-3">Status</th>
                            {{-- <th class="text-end pe-4 py-3">Action</th> --}}
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($products as $product)
                            @php
                                $stock = $product->available_stock ?? $product->stock_quantity;
                                $isOutOfStock = $stock <= 0;
                            @endphp
                            <tr wire:key="low-stock-{{ $product->id }}">
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark">{{ $product->name }}</div>
                                    @if($product->model)
                                        <div class="text-muted fs-7"><i class="bi bi-tag me-1"></i>{{ $product->model }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $product->category->name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold fs-6 {{ $isOutOfStock ? 'text-danger' : 'text-warning-emphasis' }}">
                                        {{ $stock }}
                                    </span>
                                </td>
                                <td class="text-center text-muted fw-semibold">
                                    {{ $product->min_stock_alert }}
                                </td>
                                <td class="text-center">
                                    @if($isOutOfStock)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            Out of Stock
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                            Low Stock
                                        </span>
                                    @endif
                                </td>
                                {{-- <td class="text-end pe-4">
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary shadow-sm rounded-2">
                                        <i class="bi bi-plus-lg me-1"></i>Restock
                                    </a>
                                </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="py-3">
                                        <i class="bi bi-check-circle-fill text-success fs-1 mb-2 d-block"></i>
                                        <h6 class="fw-bold text-dark">All Stock Levels Healthy</h6>
                                        <p class="text-muted small mb-0">No products are currently at or below their alert threshold.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($products->hasPages())
            <div class="card-footer bg-white border-top p-3">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
