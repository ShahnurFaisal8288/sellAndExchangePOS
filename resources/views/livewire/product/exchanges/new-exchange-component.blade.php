<div class="app-content py-4">
    <div class="container-fluid">

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 shadow-sm border-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">

            {{-- Main Form Area --}}
            <div class="col-lg-8">

                {{-- 1. Customer Selection Card --}}
                <div class="card shadow-sm rounded-4 border-0 mb-4 bg-body">
                    <div class="card-header bg-body border-bottom border-secondary border-opacity-10 py-3 px-4">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-fill me-1"></i> Customer Information</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="toggleCustomer" wire:click="toggleNewCustomer" @if($is_new_customer) checked @endif>
                            <label class="form-check-label fw-semibold text-body" for="toggleCustomer">Create New Customer</label>
                        </div>

                        @if($is_new_customer)
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required text-body">Customer Name</label>
                                    <input type="text" wire:model="new_customer_name" class="form-control bg-body text-body border-secondary border-opacity-25 @error('new_customer_name') is-invalid @enderror" placeholder="John Doe">
                                    @error('new_customer_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required text-body">Customer Phone</label>
                                    <input type="text" wire:model="new_customer_phone" class="form-control bg-body text-body border-secondary border-opacity-25 @error('new_customer_phone') is-invalid @enderror" placeholder="017xxxxxxxx">
                                    @error('new_customer_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        @else
                            <div>
                                <label class="form-label text-body">Select Existing Customer (Optional)</label>
                                <select wire:model="customerId" class="form-select bg-body text-body border-secondary border-opacity-25">
                                    <option value="">-- Walk-in Customer --</option>
                                    @foreach($this->customers as $cust)
                                        <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->phone }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- 2. Trade-in / Old Product Card --}}
                <div class="card shadow-sm rounded-4 border-0 mb-4 bg-body">
                    <div class="card-header bg-body border-bottom border-secondary border-opacity-10 py-3 px-4">
                        <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-box-arrow-in-down me-1"></i> Trade-In Product (From Customer)</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required text-body">Old Product Name / Model</label>
                                <input type="text" wire:model="oldProductName" class="form-control bg-body text-body border-secondary border-opacity-25 @error('oldProductName') is-invalid @enderror" placeholder="e.g., iPhone 11 Pro 64GB">
                                @error('oldProductName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-body">IMEI / Serial Number (Optional)</label>
                                <input type="text" wire:model="oldProductImei" class="form-control bg-body text-body border-secondary border-opacity-25" placeholder="15-digit IMEI">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required text-body">Trade-In Return Value (৳)</label>
                                <input type="number" step="0.01" wire:model.live.debounce.300ms="oldProductReturnValue" class="form-control fw-bold text-danger bg-body border-secondary border-opacity-25 @error('oldProductReturnValue') is-invalid @enderror font-monospace" placeholder="0.00">
                                @error('oldProductReturnValue') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Outgoing Store Item Card --}}
                <div class="card shadow-sm rounded-4 border-0 mb-4 bg-body">
                    <div class="card-header bg-body border-bottom border-secondary border-opacity-10 py-3 px-4">
                        <h6 class="mb-0 fw-bold text-success"><i class="bi bi-box-arrow-up me-1"></i> Outgoing Product (To Customer)</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="position-relative mb-3" x-data="{ open: true }" @click.outside="open = false">
                            <label class="form-label fw-bold text-body">Search Store Product or Scan IMEI</label>
                            <div class="input-group">
                                <span class="input-group-text bg-body-secondary border-secondary border-opacity-25 text-muted"><i class="bi bi-search"></i></span>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="productSearch"
                                    @focus="open = true"
                                    @input="open = true"
                                    class="form-control bg-body text-body border-secondary border-opacity-25 @error('newProductId') is-invalid @enderror"
                                    placeholder="Type product name, model or scan IMEI..."
                                >
                            </div>
                            @error('newProductId') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                            {{-- Floating Search Results Dropdown --}}
                            @if($this->searchResults && $this->searchResults->isNotEmpty())
                                <div
                                    x-show="open"
                                    x-transition
                                    class="position-absolute start-0 end-0 top-100 mt-1 bg-body border border-secondary border-opacity-25 rounded-3 shadow-lg z-3 overflow-hidden"
                                    style="max-height: 350px; overflow-y: auto;"
                                >
                                    <div class="list-group list-group-flush">
                                        @foreach($this->searchResults as $prod)
                                            @if($prod->purchaseItemImeis->isNotEmpty())
                                                <div class="list-group-item bg-body-secondary text-uppercase fw-bold text-muted small py-2 px-3 border-bottom border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="bi bi-phone me-1"></i> {{ $prod->name }} {{ $prod->model ?? '' }}
                                                        @if($prod->country_code)
                                                            <span class="badge bg-secondary bg-opacity-25 text-body border ms-1">{{ strtoupper($prod->country_code) }}</span>
                                                        @endif
                                                        @if($prod->color)
                                                            <span class="badge bg-secondary bg-opacity-25 text-body border ms-1">{{ $prod->color }}</span>
                                                        @endif
                                                    </span>
                                                    <span class="text-success fw-bold font-monospace">৳{{ number_format($prod->sale_price, 2) }}</span>
                                                </div>

                                                @foreach($prod->purchaseItemImeis as $imei)
                                                    <button
                                                        type="button"
                                                        wire:click="selectNewProduct({{ $prod->id }}, {{ $imei->id }}, '{{ $imei->imei_serial }}')"
                                                        @click="open = false"
                                                        class="list-group-item list-group-item-action bg-body text-body ps-4 py-2 d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-10"
                                                    >
                                                        <div>
                                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 me-2"><i class="bi bi-barcode me-1"></i> IMEI</span>
                                                            <span class="fw-semibold font-monospace text-body">{{ $imei->imei_serial }}</span>
                                                        </div>
                                                        <span class="btn btn-sm btn-outline-primary py-0 px-2 fw-semibold">+ Select</span>
                                                    </button>
                                                @endforeach
                                            @else
                                                <button
                                                    type="button"
                                                    wire:click="selectNewProduct({{ $prod->id }})"
                                                    @click="open = false"
                                                    class="list-group-item list-group-item-action bg-body text-body d-flex justify-content-between align-items-center py-2 px-3 border-bottom border-secondary border-opacity-10"
                                                >
                                                    <div>
                                                        <span class="fw-semibold text-body">{{ $prod->name }} {{ $prod->model ?? '' }}</span>
                                                        @if($prod->country_code)
                                                            <span class="badge bg-secondary bg-opacity-25 text-body border ms-1">{{ strtoupper($prod->country_code) }}</span>
                                                        @endif
                                                        @if($prod->color)
                                                            <span class="badge bg-secondary bg-opacity-25 text-body border ms-1">{{ $prod->color }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="fw-bold text-success font-monospace">৳{{ number_format($prod->sale_price, 2) }}</div>
                                                        <small class="text-muted">In Stock: {{ $prod->stock_quantity }}</small>
                                                    </div>
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Selected Product Banner --}}
                        @if($newProductId)
                            <div class="alert alert-success bg-body-secondary border border-success border-opacity-25 d-flex justify-content-between align-items-center mb-0 text-body">
                                <div>
                                    <div class="fw-bold fs-6 text-body">{{ $selectedProductName }}</div>

                                    @if($selectedImeiNumber || $selectedCountryCode || $selectedColor)
                                        <div class="mt-1 d-flex flex-wrap gap-1">
                                            @if($selectedImeiNumber)
                                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 font-monospace"><i class="bi bi-barcode me-1"></i>IMEI: {{ $selectedImeiNumber }}</span>
                                            @endif
                                            @if($selectedCountryCode)
                                                <span class="badge bg-secondary bg-opacity-25 text-body border">{{ strtoupper($selectedCountryCode) }}</span>
                                            @endif
                                            @if($selectedColor)
                                                <span class="badge bg-secondary bg-opacity-25 text-body border">{{ $selectedColor }}</span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="text-muted small mt-1">
                                        Price: <strong class="text-success font-monospace">৳{{ number_format($newProductPrice, 2) }}</strong>
                                    </div>
                                </div>
                                <button type="button" wire:click="clearSelectedProduct" class="btn btn-outline-danger btn-sm rounded-circle">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted border border-secondary border-opacity-25 rounded-3 bg-body-secondary">
                                <i class="bi bi-box-arrow-up display-6 d-block text-secondary opacity-50 mb-2"></i>
                                No outgoing store product selected yet.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar Summary --}}
            <div class="col-lg-4">
                <div class="card shadow-sm rounded-4 border-0 bg-body sticky-top" style="top: 20px;">
                    <div class="card-header bg-body border-bottom border-secondary border-opacity-10 py-3 px-4">
                        <h6 class="mb-0 fw-bold text-body"><i class="bi bi-calculator me-1 text-primary"></i> Exchange Calculation</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2 text-muted small">
                            <span>New Product Price:</span>
                            <span class="fw-bold font-monospace text-body">৳{{ number_format($newProductPrice ?: 0, 2) }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3 text-danger small">
                            <span>Less Trade-In Value:</span>
                            <span class="fw-bold font-monospace">- ৳{{ number_format($oldProductReturnValue ?: 0, 2) }}</span>
                        </div>

                        <hr class="border-secondary opacity-10 my-3">

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold fs-6 text-body">
                                @if($additionalPayment >= 0)
                                    Customer Pays:
                                @else
                                    Refund to Customer:
                                @endif
                            </span>
                            <span class="fw-bolder fs-4 {{ $additionalPayment >= 0 ? 'text-success' : 'text-danger' }} font-monospace">
                                ৳{{ number_format(abs($additionalPayment), 2) }}
                            </span>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-body">Payment Method</label>
                            <select wire:model="paymentMethod" class="form-select bg-body text-body border-secondary border-opacity-25">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="mobile_banking">Mobile Banking (Bkash/Nagad)</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-body">Notes / Remarks</label>
                            <textarea wire:model="notes" class="form-control bg-body text-body border-secondary border-opacity-25" rows="2" placeholder="Optional notes..."></textarea>
                        </div>

                        <button
                            type="button"
                            wire:click="confirmExchange"
                            wire:loading.attr="disabled"
                            class="btn btn-primary w-100 py-3 fs-6 fw-bold rounded-pill shadow-sm"
                        >
                            <span wire:loading.remove wire:target="confirmExchange"><i class="bi bi-check-circle me-1"></i> Complete Exchange</span>
                            <span wire:loading wire:target="confirmExchange"><span class="spinner-border spinner-border-sm me-1"></span> Processing...</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
