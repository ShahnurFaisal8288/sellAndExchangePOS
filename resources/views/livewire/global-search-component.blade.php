<div class="position-relative" x-data="{ open: false }" @click.outside="open = false">
    <div class="input-group input-group-sm">
        <span class="input-group-text bg-transparent border-end-0">
            <i class="bi bi-search text-secondary"></i>
        </span>
        <input type="search" wire:model.live.debounce.300ms="query" @focus="open = true" @input="open = true"
            class="form-control border-start-0 ps-0" placeholder="Search products, customers, invoices…"
            aria-label="Search" style="min-width:320px" />
    </div>

    @if($this->results !== null)
        <div x-show="open" x-transition
            class="position-absolute start-0 end-0 top-100 mt-1 bg-body border border-secondary border-opacity-25 rounded-3 shadow-lg z-3 overflow-hidden"
            style="max-height: 420px; overflow-y: auto; min-width: 340px;">
            @if(empty($this->results))
                <div class="p-3 text-muted small text-center">No results found.</div>
            @else
                <div class="list-group list-group-flush">

                    @if($this->results['purchases']->isNotEmpty())
                        <div class="list-group-item bg-body-secondary text-uppercase fw-bold text-muted small py-2 px-3">Purchase
                            Invoices</div>
                        {{-- Purchases --}}
                        @foreach($this->results['purchases'] as $purchase)
                            <a href="{{ route('purchases.invoice', $purchase->id) }}" target="_blank"
                                class="list-group-item list-group-item-action py-2 px-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold small">{{ $purchase->invoice_no }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $purchase->supplier?->name ?? 'No Supplier' }}
                                    </div>
                                </div>
                                <span
                                    class="badge bg-secondary bg-opacity-25 text-body border">৳{{ number_format($purchase->total_amount, 2) }}</span>
                            </a>
                        @endforeach
                    @endif

                    @if($this->results['sales']->isNotEmpty())
                        <div class="list-group-item bg-body-secondary text-uppercase fw-bold text-muted small py-2 px-3">Sale
                            Invoices</div>
                        {{-- Sales — sales.show exists, this one was already correct --}}
                        @foreach($this->results['sales'] as $sale)
                            <a href="{{ route('sales.show', $sale->id) }}" wire:navigate
                                class="list-group-item list-group-item-action py-2 px-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-semibold small">{{ $sale->invoice_no }}</div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $sale->customer?->name ?? 'Walk-in Customer' }}
                                    </div>
                                </div>
                                <span
                                    class="badge bg-secondary bg-opacity-25 text-body border">৳{{ number_format($sale->total_amount, 2) }}</span>
                            </a>
                        @endforeach
                    @endif

                    @if($this->results['customers']->isNotEmpty())
                        <div class="list-group-item bg-body-secondary text-uppercase fw-bold text-muted small py-2 px-3">Customers
                        </div>
                        {{-- Customers — fixed to customers.index --}}
                        @foreach($this->results['customers'] as $customer)
                            <a href="{{ route('customers.index', ['search' => $customer->name]) }}" wire:navigate
                                class="list-group-item list-group-item-action py-2 px-3">
                                <div class="fw-semibold small">{{ $customer->name }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $customer->phone }}</div>
                            </a>
                        @endforeach
                    @endif

                    @if($this->results['products']->isNotEmpty())
                        <div class="list-group-item bg-body-secondary text-uppercase fw-bold text-muted small py-2 px-3">Products
                        </div>
                       {{-- Products — fixed to products.edit --}}
@foreach($this->results['products'] as $product)
    <a href="{{ route('products.edit', $product->id) }}" wire:navigate
       class="list-group-item list-group-item-action py-2 px-3 d-flex justify-content-between align-items-center">
        <div class="fw-semibold small">{{ $product->name }} {{ $product->model }}</div>
        <span class="badge bg-secondary bg-opacity-25 text-body border">Stock: {{ $product->stock_quantity }}</span>
    </a>
@endforeach
                    @endif

                </div>
            @endif
        </div>
    @endif
</div>
