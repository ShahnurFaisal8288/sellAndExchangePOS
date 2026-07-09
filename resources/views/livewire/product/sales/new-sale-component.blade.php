<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
        <!-- Left: Product search + Cart -->
        <div class="col-lg-7">
            <div class="card mb-3">
                <div class="card-body">
                    <label class="form-label">Search Product (name / model / IMEI)</label>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="productSearch"
                        class="form-control"
                        placeholder="Type to search..."
                    >

                    @if(count($this->searchResults) > 0)
                        <div class="list-group mt-2">
                            @foreach($this->searchResults as $product)
                                <button
                                    type="button"
                                    wire:click="addToCart({{ $product->id }})"
                                    class="list-group-item list-group-item-action d-flex justify-content-between"
                                >
                                    <span>{{ $product->name }} {{ $product->model }}</span>
                                    <span class="text-muted">Stock: {{ $product->stock_quantity }} | ৳{{ number_format($product->sale_price, 2) }}</span>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">Cart</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th class="text-center" style="width:120px">Qty</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cart as $productId => $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-center">
                                        <input
                                            type="number"
                                            min="1"
                                            max="{{ $item['stock'] }}"
                                            wire:change="updateQty({{ $productId }}, $event.target.value)"
                                            value="{{ $item['qty'] }}"
                                            class="form-control form-control-sm text-center"
                                        >
                                    </td>
                                    <td class="text-end">৳{{ number_format($item['price'], 2) }}</td>
                                    <td class="text-end">৳{{ number_format($item['price'] * $item['qty'], 2) }}</td>
                                    <td>
                                        <button wire:click="removeFromCart({{ $productId }})" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Cart is empty. Search and add products above.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Checkout panel -->
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">Checkout</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Customer (optional — leave blank for walk-in)</label>
                        <select wire:model="customerId" class="form-select">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Discount (৳)</label>
                        <input type="number" step="0.01" min="0" wire:model.live="discount" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select wire:model="paymentMethod" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile_banking">Mobile Banking</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Paid Amount (৳)</label>
                        <input type="number" wire:model.live="paidAmount" class="form-control">
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between"><span>Subtotal</span><strong>৳{{ number_format($this->subtotal, 2) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Discount</span><strong>- ৳{{ number_format($discount, 2) }}</strong></div>
                    <div class="d-flex justify-content-between fs-5"><span>Total</span><strong>৳{{ number_format($this->total, 2) }}</strong></div>
                    <div class="d-flex justify-content-between text-danger"><span>Due</span><strong>৳{{ number_format($this->due, 2) }}</strong></div>

                    @error('cart') <div class="text-danger small mt-2">{{ $message }}</div> @enderror

                    <button
                        wire:click="confirmSale"
                        wire:loading.attr="disabled"
                        class="btn btn-primary w-100 mt-3"
                        @disabled(count($cart) === 0)
                    >
                        <span wire:loading.remove wire:target="confirmSale">Confirm Sale</span>
                        <span wire:loading wire:target="confirmSale">Processing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
