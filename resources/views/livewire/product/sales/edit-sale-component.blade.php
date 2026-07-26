<div class="app-content py-4">
    <div class="container-fluid">

        {{-- Success Notification --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 shadow-sm border-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0 text-body">
                <i class="bi bi-pencil-square me-1 text-warning"></i> Edit Sale
                <span class="text-muted fw-normal">#{{ $sale->invoice_no }}</span>
            </h5>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="bi bi-arrow-left me-1"></i> Back to Sales
            </a>
        </div>

        <div class="row g-4">

            {{-- Left Column: Product Search & Cart Table --}}
            <div class="col-lg-7">

                {{-- Product/IMEI Search Input Card --}}
                <div class="card mb-4 shadow-sm rounded-4 border-0 bg-body position-relative" x-data="{ open: true }" @click.outside="open = false">
                    <div class="card-body p-4">
                        <label class="form-label fw-bold text-body">Search Product or IMEI</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-secondary border-secondary border-opacity-25 text-muted"><i class="bi bi-search"></i></span>
                            <input
                                type="text"
                                wire:model.live.debounce.300ms="productSearch"
                                @focus="open = true"
                                @input="open = true"
                                class="form-control bg-body text-body border-secondary border-opacity-25"
                                placeholder="Type product name, model or scan IMEI number..."
                            >
                        </div>

                        @if(count($this->searchResults) > 0)
                            <div
                                x-show="open"
                                x-transition
                                class="position-absolute start-0 end-0 top-100 mt-1 mx-4 bg-body border border-secondary border-opacity-25 rounded-3 shadow-lg z-3 overflow-hidden"
                                style="max-height: 350px; overflow-y: auto;"
                            >
                                <div class="list-group list-group-flush">
                                    @foreach($this->searchResults as $product)
                                        @if($product->purchaseItemImeis->isNotEmpty())
                                            <div class="list-group-item bg-body-secondary text-uppercase fw-bold text-muted small py-2 px-3 border-bottom border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i class="bi bi-phone me-1"></i> {{ $product->name }} {{ $product->model }}
                                                    @if($product->country_code)
                                                        <span class="badge bg-secondary bg-opacity-25 text-body border ms-1">{{ strtoupper($product->country_code) }}</span>
                                                    @endif
                                                    @if($product->color)
                                                        <span class="badge bg-secondary bg-opacity-25 text-body border ms-1">{{ $product->color }}</span>
                                                    @endif
                                                </span>
                                                <span class="text-success fw-bold font-monospace">৳{{ number_format($product->sale_price, 2) }}</span>
                                            </div>

                                            @foreach($product->purchaseItemImeis as $imei)
                                                <button
                                                    type="button"
                                                    wire:click="addToCart({{ $product->id }}, '{{ $imei->imei_serial }}', {{ $imei->id }})"
                                                    @click="open = false"
                                                    class="list-group-item list-group-item-action bg-body text-body ps-4 py-2 d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-10"
                                                >
                                                    <div>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 me-2"><i class="bi bi-barcode me-1"></i> IMEI</span>
                                                        <span class="fw-semibold font-monospace text-body">{{ $imei->imei_serial }}</span>
                                                    </div>
                                                    <span class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold">+ Add to Cart</span>
                                                </button>
                                            @endforeach
                                        @else
                                            <button
                                                type="button"
                                                wire:click="addToCart({{ $product->id }})"
                                                @click="open = false"
                                                class="list-group-item list-group-item-action bg-body text-body d-flex justify-content-between align-items-center py-2 px-3 border-bottom border-secondary border-opacity-10"
                                            >
                                                <div>
                                                    <span class="fw-semibold text-body">{{ $product->name }} {{ $product->model }}</span>
                                                    @if($product->country_code)
                                                        <span class="badge bg-secondary bg-opacity-25 text-body border ms-1">{{ strtoupper($product->country_code) }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-end">
                                                    <div class="fw-bold text-success font-monospace">৳{{ number_format($product->sale_price, 2) }}</div>
                                                    <small class="text-muted">In Stock: {{ $product->stock_quantity }}</small>
                                                </div>
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Cart Table Card --}}
                <div class="card shadow-sm rounded-4 border-0 bg-body overflow-hidden">
                    <div class="card-header bg-body border-bottom border-secondary border-opacity-10 d-flex justify-content-between align-items-center py-3 px-4">
                        <h6 class="mb-0 fw-bold text-body"><i class="bi bi-cart3 me-1 text-primary"></i> Cart Items</h6>
                        <span class="badge bg-primary rounded-pill px-3 py-1">{{ count($cart) }} Items</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light bg-body-secondary text-muted small text-uppercase">
                                    <tr>
                                        <th class="py-3 px-4">Item & IMEI Details</th>
                                        <th class="text-center py-3" style="width:120px">Qty</th>
                                        <th class="text-end py-3">Unit Price</th>
                                        <th class="text-end py-3">Subtotal</th>
                                        <th class="text-center py-3" style="width:50px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cart as $cartKey => $item)
                                        <tr wire:key="cart-item-{{ $cartKey }}" class="border-bottom border-secondary border-opacity-10">
                                            <td class="py-3 px-4">
                                                <div class="fw-semibold text-body">{{ $item['name'] }}</div>

                                                @if(!empty($item['imei']))
                                                    <div class="mt-1">
                                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 font-monospace">
                                                            <i class="bi bi-barcode me-1"></i>IMEI: {{ $item['imei'] }}
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">Standard Product (No IMEI)</span>
                                                @endif

                                                <div class="mt-1">
                                                    @if(!empty($item['country_code']))
                                                        <span class="badge bg-secondary bg-opacity-25 text-body border" style="font-size: 10px;">{{ strtoupper($item['country_code']) }}</span>
                                                    @endif
                                                    @if(!empty($item['color']))
                                                        <span class="badge bg-secondary bg-opacity-25 text-body border" style="font-size: 10px;">{{ $item['color'] }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center py-3">
                                                <input
                                                    type="number"
                                                    min="1"
                                                    wire:change="updateQty('{{ $cartKey }}', $event.target.value)"
                                                    value="{{ $item['qty'] }}"
                                                    class="form-control form-control-sm text-center fw-bold bg-body text-body border-secondary border-opacity-25"
                                                    @if(!empty($item['imei_id'])) readonly @endif
                                                >
                                            </td>
                                            <td class="text-end py-3 font-monospace">৳{{ number_format($item['price'], 2) }}</td>
                                            <td class="text-end py-3 fw-bold font-monospace text-body">৳{{ number_format($item['price'] * $item['qty'], 2) }}</td>
                                            <td class="text-center py-3 px-3">
                                                <button wire:click="removeFromCart('{{ $cartKey }}')" class="btn btn-sm btn-outline-danger border-0 rounded-circle">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">
                                                <i class="bi bi-basket display-6 d-block text-secondary opacity-50 mb-2"></i>
                                                Cart is empty. Scan an IMEI or search product above.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Right Column: Customer & Checkout Panel --}}
            <div class="col-lg-5">
                <div class="card shadow-sm rounded-4 border-0 bg-body">
                    <div class="card-header bg-body border-bottom border-secondary border-opacity-10 py-3 px-4">
                        <h6 class="mb-0 fw-bold text-body"><i class="bi bi-receipt me-1 text-success"></i> Checkout Summary</h6>
                    </div>
                    <div class="card-body p-4">

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label mb-0 fw-bold text-body">Customer</label>
                                <button type="button" wire:click="toggleNewCustomer" class="btn btn-link btn-sm p-0 text-decoration-none fw-semibold">
                                    {{ $is_new_customer ? 'Choose Existing' : '+ New Customer' }}
                                </button>
                            </div>

                            @if($is_new_customer)
                                <div class="border border-secondary border-opacity-25 rounded-3 p-3 bg-body-secondary">
                                    <input
                                        type="text"
                                        wire:model="new_customer_name"
                                        placeholder="Customer Name *"
                                        class="form-control form-control-sm mb-2 bg-body text-body border-secondary border-opacity-25 @error('new_customer_name') is-invalid @enderror"
                                    >
                                    @error('new_customer_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                                    <input
                                        type="text"
                                        wire:model="new_customer_phone"
                                        placeholder="Phone Number *"
                                        class="form-control form-control-sm bg-body text-body border-secondary border-opacity-25 @error('new_customer_phone') is-invalid @enderror"
                                    >
                                    @error('new_customer_phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            @else
                                <select wire:model="customerId" class="form-select bg-body text-body border-secondary border-opacity-25">
                                    <option value="">Walk-in Customer (Guest)</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-body">Discount (৳)</label>
                            <input type="number" min="0" step="0.01" x-on:focus="$event.target.select()" wire:model.live.debounce.800ms="discount" class="form-control bg-body text-body border-secondary border-opacity-25">
                            @error('discount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-body">Payment Method</label>
                            <select wire:model="paymentMethod" class="form-select bg-body text-body border-secondary border-opacity-25">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="mobile_banking">Mobile Banking (bKash/Nagad/Rocket)</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-body">Paid Amount (৳)</label>
                            <input type="number" min="0" step="0.01" x-on:focus="$event.target.select()" wire:model.live.debounce.800ms="paidAmount" class="form-control fw-bold bg-body text-body border-secondary border-opacity-25 font-monospace">
                        </div>

                        <hr class="border-secondary opacity-10 my-4">

                        <div class="d-flex justify-content-between mb-2 text-muted small">
                            <span>Subtotal:</span>
                            <strong class="font-monospace text-body">৳{{ number_format($this->subtotal, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-muted small">
                            <span>Discount:</span>
                            <span class="font-monospace">- ৳{{ number_format((float) $discount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 fs-5 pt-2 border-top border-secondary border-opacity-10">
                            <span class="fw-bold text-body">Total Amount:</span>
                            <span class="fw-bold text-primary font-monospace">৳{{ number_format($this->total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-danger mb-4">
                            <span class="small fw-semibold">Due Balance:</span>
                            <strong class="fs-6 font-monospace">৳{{ number_format($this->due, 2) }}</strong>
                        </div>

                        @error('cart') <div class="text-danger small mb-3">{{ $message }}</div> @enderror

                        <button
                            wire:click="confirmUpdate"
                            wire:loading.attr="disabled"
                            class="btn btn-warning w-100 py-3 fs-6 fw-bold rounded-pill shadow-sm text-white"
                            @disabled(count($cart) === 0)
                        >
                            <span wire:loading.remove wire:target="confirmUpdate">
                                <i class="bi bi-arrow-repeat me-1"></i> Update Sale
                            </span>
                            <span wire:loading wire:target="confirmUpdate">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving Changes...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
