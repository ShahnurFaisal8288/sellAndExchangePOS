<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $sale->invoice_no }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #212529;
            margin: 0;
            padding: 24px;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .invoice-header h2 { margin: 0; }
        .muted { color: #6c757d; font-size: 13px; }
        hr { border: none; border-top: 1px solid #dee2e6; margin: 16px 0; }
        .bill-row { display: flex; justify-content: space-between; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #dee2e6; padding: 8px 10px; font-size: 14px; }
        th { background: #f8f9fa; text-align: left; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .summary { width: 300px; margin-left: auto; }
        .summary td { border: none; padding: 4px 10px; }
        .summary .total-row td { font-weight: bold; font-size: 16px; border-top: 2px solid #212529; }
        .due-row td { color: #dc3545; font-weight: bold; }
        .footer-note { text-align: center; color: #6c757d; margin-top: 30px; font-size: 13px; }
        .badge {
            background: #6c757d; color: #fff; padding: 3px 8px;
            border-radius: 4px; font-size: 12px; display: inline-block;
        }
        .print-bar { text-align: right; margin-bottom: 16px; }
        .print-bar button {
            background: #0d6efd; color: #fff; border: none;
            padding: 6px 16px; border-radius: 4px; cursor: pointer; font-size: 14px;
        }

        @media print {
            .print-bar { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="print-bar">
        <button onclick="window.print()">Print</button>
    </div>

    <div class="invoice-header">
        <div>
            <h2>MobileExchange</h2>
            <div class="muted">Point of Sale — Electronics &amp; Mobile Device Retail</div>
        </div>
        <div style="text-align:right">
            <h3 style="margin:0">Invoice #{{ $sale->invoice_no }}</h3>
            <div class="muted">{{ $sale->sale_date->format('d M, Y') }}</div>
        </div>
    </div>

    <hr>

    <div class="bill-row">
        <div>
            <strong>Billed To</strong><br>
            @if($sale->customer)
                {{ $sale->customer->name }}<br>
                {{ $sale->customer->phone }}<br>
                @if($sale->customer->address){{ $sale->customer->address }}<br>@endif
            @else
                Walk-in Customer
            @endif
        </div>
        <div style="text-align:right">
            <strong>Served By</strong><br>
            {{ $sale->user->name }}<br>
            <strong>Payment Method</strong><br>
            <span class="badge">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</span>
        </div>
    </div>

    <table>
        <thead>
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
                            <br><span class="muted">IMEI: {{ $item->product->imei_serial }}</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">৳{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-end">৳{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary">
        <tr><td>Subtotal</td><td class="text-end">৳{{ number_format($sale->total_amount + $sale->discount, 2) }}</td></tr>
        <tr><td>Discount</td><td class="text-end">- ৳{{ number_format($sale->discount, 2) }}</td></tr>
        <tr class="total-row"><td>Total</td><td class="text-end">৳{{ number_format($sale->total_amount, 2) }}</td></tr>
        <tr><td>Paid</td><td class="text-end">৳{{ number_format($sale->paid_amount, 2) }}</td></tr>
        <tr class="due-row"><td>Due</td><td class="text-end">৳{{ number_format($sale->due_amount, 2) }}</td></tr>
    </table>

    <div class="footer-note">Thank you for your business!</div>

</body>
</html>
