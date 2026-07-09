@extends('layouts.app.base.base')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 no-print">
    <h4 class="mb-0">Invoice {{ $sale->invoice_no }}</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Sales
        </a>
        <livewire:product.sales.record-payment-component :sale="$sale" />
        <a href="{{ route('sales.print', $sale) }}" target="_blank" class="btn btn-sm btn-primary">
            <i class="bi bi-printer"></i> Print
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body" id="invoice-content">

        <div class="d-flex justify-content-between mb-4">
            <div>
                <h5 class="mb-0">MobileExchange</h5>
                <small class="text-muted">Point of Sale — Electronics &amp; Mobile Device Retail</small>
            </div>
            <div class="text-end">
                <h5 class="mb-0">Invoice #{{ $sale->invoice_no }}</h5>
                <small class="text-muted">{{ $sale->sale_date->format('d M, Y') }}</small>
            </div>
        </div>

        <hr>

        <div class="row mb-4">
            <div class="col-md-6">
                <strong>Billed To</strong><br>
                @if($sale->customer)
                    {{ $sale->customer->name }}<br>
                    {{ $sale->customer->phone }}<br>
                    @if($sale->customer->address){{ $sale->customer->address }}<br>@endif
                @else
                    Walk-in Customer
                @endif
            </div>
            <div class="col-md-6 text-md-end">
                <strong>Served By</strong><br>
                {{ $sale->user->name }}<br>
                <strong>Payment Method</strong><br>
                <span class="badge text-bg-secondary">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            {{ $item->product->name ?? 'Deleted Product' }} {{ $item->product->model ?? '' }}
                            @if($item->product?->imei_serial)
                                <br><small class="text-muted">IMEI: {{ $item->product->imei_serial }}</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">৳{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-end">৳{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="row">
            <div class="col-md-6">
                @if($sale->notes ?? false)
                    <strong>Notes</strong>
                    <p class="text-muted">{{ $sale->notes }}</p>
                @endif
            </div>
            <div class="col-md-6">
                <table class="table table-sm mb-0">
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-end">৳{{ number_format($sale->total_amount + $sale->discount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td class="text-end">- ৳{{ number_format($sale->discount, 2) }}</td>
                    </tr>
                    <tr class="fw-bold fs-5">
                        <td>Total</td>
                        <td class="text-end">৳{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Paid</td>
                        <td class="text-end">৳{{ number_format($sale->paid_amount, 2) }}</td>
                    </tr>
                    <tr class="{{ $sale->due_amount > 0 ? 'text-danger fw-bold' : '' }}">
                        <td>Due</td>
                        <td class="text-end">৳{{ number_format($sale->due_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <hr class="mt-4">
        <p class="text-center text-muted small mb-0">Thank you for your business!</p>
    </div>
</div>
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('payment-recorded', () => {
            const modalEl = document.getElementById('recordPaymentModal');
            bootstrap.Modal.getInstance(modalEl)?.hide();
            location.reload(); // simplest way to refresh the invoice totals + payment history below
        });
    });
</script>
@endsection



