<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Low Stock Alerts</h4>
        <input
            type="text"
            wire:model.live.debounce.400ms="search"
            class="form-control w-25"
            placeholder="Search product, model, IMEI..."
        >
    </div>

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Model</th>
                        <th>Category</th>
                        <th class="text-center">Stock</th>
                        <th class="text-center">Alert Level</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->model }}</td>
                            <td>{{ $product->category->name ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge text-bg-danger">{{ $product->stock_quantity }}</span>
                            </td>
                            <td class="text-center">{{ $product->min_stock_alert }}</td>
                            <td class="text-end">
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                    Restock
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                No products are currently below their stock alert level.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="card-footer">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
