<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Exchange #{{ $exchange->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #212529; margin: 0; padding: 24px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .muted { color: #6c757d; font-size: 13px; }
        hr { border: none; border-top: 1px solid #dee2e6; margin: 16px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        td, th { border: 1px solid #dee2e6; padding: 8px 10px; font-size: 14px; }
        th { background: #f8f9fa; text-align: left; width: 35%; }
        .two-col { display: flex; gap: 20px; }
        .two-col > div { flex: 1; }
        .summary td { border: none; padding: 4px 0; }
        .total-row td { font-weight: bold; font-size: 16px; border-top: 2px solid #212529; }
        .print-bar { text-align: right; margin-bottom: 16px; }
        .print-bar button { background: #0d6efd; color: #fff; border: none; padding: 6px 16px; border-radius: 4px; cursor: pointer; }
        @media print { .print-bar { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="print-bar"><button onclick="window.print()">Print</button></div>

    <div class="header">
        <div>
            <h2>MobileExchange</h2>
            <div class="muted">Point of Sale — Electronics &amp; Mobile Device Retail</div>
        </div>
        <div style="text-align:right">
            <h3 style="margin:0">Exchange #{{ $exchange->id }}</h3>
            <div class="muted">{{ $exchange->exchange_date->format('d M, Y') }}</div>
            <div class="muted">{{ ucwords(str_replace('_', ' ', $exchange->exchange_type)) }}</div>
        </div>
    </div>

    <hr>

    <div class="two-col">
        <div>
            <table>
                <tr><th colspan="2">Old / Traded-In Item</th></tr>
                <tr><td>Item</td><td>{{ $exchange->oldProduct->name ?? $exchange->old_product_description ?? '—' }}</td></tr>
                <tr><td>Condition</td><td>{{ ucfirst($exchange->condition) }}</td></tr>
                <tr><td>Return Value</td><td>৳{{ number_format($exchange->old_product_return_value, 2) }}</td></tr>
            </table>
        </div>
        <div>
            <table>
                <tr><th colspan="2">New Item</th></tr>
                <tr><td>Item</td><td>{{ $exchange->newProduct->name ?? '—' }}</td></tr>
                <tr><td>Price</td><td>৳{{ number_format($exchange->new_product_price, 2) }}</td></tr>
                <tr><td>Customer</td><td>{{ $exchange->sale->customer->name ?? 'Walk-in' }}</td></tr>
            </table>
        </div>
    </div>

    <table class="summary" style="width:300px; margin-left:auto">
        <tr><td>New Item Price</td><td style="text-align:right">৳{{ number_format($exchange->new_product_price, 2) }}</td></tr>
        <tr><td>Old Item Credit</td><td style="text-align:right">- ৳{{ number_format($exchange->old_product_return_value, 2) }}</td></tr>
        <tr class="total-row">
            <td>{{ $exchange->additional_payment >= 0 ? 'Customer Paid' : 'Refunded' }}</td>
            <td style="text-align:right">৳{{ number_format(abs($exchange->additional_payment), 2) }}</td>
        </tr>
    </table>

    @if($exchange->notes)
        <p><strong>Notes:</strong> {{ $exchange->notes }}</p>
    @endif

    <hr>
    <p style="text-align:center; color:#6c757d; font-size:13px">Thank you for your business!</p>
</body>
</html>
