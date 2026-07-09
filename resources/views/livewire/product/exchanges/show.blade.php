@extends('layouts.app.base.base')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Exchange #{{ $exchange->id }}</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('exchanges.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Exchanges
        </a>
        <a href="{{ route('exchanges.print', $exchange) }}" target="_blank" class="btn btn-sm btn-primary">
            <i class="bi bi-printer"></i> Print
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <span class="badge text-bg-secondary fs-6 mb-2">{{ ucwords(str_replace('_', ' ', $exchange->exchange_type)) }}</span>
                <div class="text-muted small">{{ $exchange->exchange_date->format('d M, Y') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">Old Item Credit</div>
                <div class="fs-5">৳{{ number_format($exchange->old_product_return_value, 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="text-muted small">{{ $exchange->additional_payment >= 0 ? 'Customer Paid' : 'Refunded to Customer' }}</div>
                <div class="fs-5 {{ $exchange->additional_payment < 0 ? 'text-success' : '' }}">
                    ৳{{ number_format(abs($exchange->additional_payment), 2) }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">Old / Traded-In Item</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted">Source</td>
                        <td>{{ $exchange->old_product_source === 'this_shop' ? 'Sold by this shop' : 'External / Trade-in' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Item</td>
                        <td>{{ $exchange->oldProduct->name ?? $exchange->old_product_description ?? '—' }} {{ $exchange->oldProduct->model ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Condition</td>
                        <td>
                            <span class="badge {{ $exchange->condition === 'resellable' ? 'text-bg-success' : 'text-bg-danger' }}">
                                {{ ucfirst($exchange->condition) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Return Value</td>
                        <td>৳{{ number_format($exchange->old_product_return_value, 2) }}</td>
                    </tr>
                    @if($exchange->purchase)
                        <tr>
                            <td class="text-muted">Linked Purchase</td>
                            <td>{{ $exchange->purchase->invoice_no }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">New Item (Customer Took Home)</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted">Item</td>
                        <td>{{ $exchange->newProduct->name ?? '—' }} {{ $exchange->newProduct->model ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Price</td>
                        <td>৳{{ number_format($exchange->new_product_price, 2) }}</td>
                    </tr>
                    @if($exchange->sale)
                        <tr>
                            <td class="text-muted">Customer</td>
                            <td>{{ $exchange->sale->customer->name ?? 'Walk-in' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Linked Sale</td>
                            <td><a href="{{ route('sales.show', $exchange->sale) }}">{{ $exchange->sale->invoice_no }}</a></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Served By</td>
                            <td>{{ $exchange->sale->user->name }}</td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

@if($exchange->notes)
    <div class="card mt-3">
        <div class="card-header">Notes</div>
        <div class="card-body">{{ $exchange->notes }}</div>
    </div>
@endif
@endsection
