<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Invoice - {{ $purchase->invoice_no }}</title>
    <style>
        body { font-family: 'DejaVu Sans', Arial, sans-serif; color: #1a1a1a; margin: 0; padding: 30px; }
        .invoice-box { max-width: 900px; margin: auto; border: 1px solid #ddd; padding: 30px; }
        .header { display: flex; justify-content: space-between; border-bottom: 1px solid #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .header .company h2 { margin: 0; color: #0d6efd; }
        .header .company p { margin: 2px 0; font-size: 13px; }
        .header .meta { text-align: right; font-size: 13px; }
        .title { text-align: center; font-size: 20px; font-weight: bold; color: #0d6efd; margin: 20px 0; letter-spacing: 1px; }
        .vendor-row { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 13px; }
        .vendor-row strong.label { color: #0d6efd; display: block; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th { border-top: 2px solid #333; border-bottom: 1px solid #333; padding: 8px; text-align: left; }
        tbody td { padding: 8px; border-bottom: 1px solid #eee; vertical-align: top; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .imei-line { font-size: 11px; color: #666; margin-top: 2px; }
        .summary-table td { border: none; padding: 4px 8px; }
        .summary-table { width: 300px; margin-left: auto; margin-top: 10px; }
        .summary-table .label { text-align: right; font-weight: 600; }
        .summary-table .value { text-align: right; }
        .outstanding { color: #dc3545; font-weight: bold; }
        .words { margin-top: 15px; font-size: 13px; }
        .print-btn { margin-bottom: 15px; }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
            .invoice-box { border: none; }
        }
    </style>
</head>
<body>

    <div class="print-btn">
        <button onclick="window.print()">🖨 Print</button>
        <a href="{{ url()->previous() }}">← Back</a>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="company">
                <h2>{{ config('app.company_name', 'Apple Series') }}</h2>
                <p>{{ config('app.company_address', 'Jamuna Future Park, Level # 4, Block # A, Shop # 004D, Dhaka-1229') }}</p>
                <p>Phone: {{ config('app.company_phone', '01868008524') }}</p>
                <p>Mobile: {{ config('app.company_mobile', '01777432652') }}</p>
                <p>Email: {{ config('app.company_email', 'appleseriesbd@gmail.com') }}</p>
            </div>
            <div class="meta">
                <p><strong>Invoice N°:</strong> {{ $purchase->invoice_no }}</p>
                <p><strong>PurSL #:</strong> {{ $purchase->id }}</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d M Y') }}</p>
            </div>
        </div>

        <div class="title">PURCHASE INVOICE</div>

        <div class="vendor-row">
            <div>
                <strong class="label">Vendor</strong>
                {{ $purchase->supplier?->name ?? 'Walk-in / No Supplier' }}<br>
                Contact: {{ $purchase->supplier?->phone ?? '—' }}<br>
                Client ID: {{ $purchase->supplier?->client_id ?? $purchase->supplier_id }}
            </div>
            <div>
                <strong class="label">Address</strong>
                {{ $purchase->supplier?->address ?? '—' }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:30px;">N°</th>
                    <th>Description (Code)</th>
                    <th class="text-end" style="width:90px;">Price</th>
                    <th class="text-center" style="width:60px;">Qty</th>
                    <th class="text-end" style="width:60px;">Dis</th>
                    <th class="text-end" style="width:100px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchase->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            {{ $item->product?->name }}
                            @if($item->product?->color) - {{ $item->product->color }} @endif
                            @if($item->product?->country_code) - {{ strtoupper($item->product->country_code) }} @endif

                            @if($item->imeis->isNotEmpty())
                                <div class="imei-line">
                                    IMEI#{{ $item->imeis->pluck('imei_serial')->implode(', IMEI#') }}
                                </div>
                            @endif
                        </td>
                        <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center">{{ $item->quantity }} Pcs</td>
                        <td class="text-end">0.00</td>
                        <td class="text-end">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="summary-table">
            <tr>
                <td class="label">Sub Total</td>
                <td class="value">{{ number_format($purchase->items->sum('subtotal'), 2) }}</td>
            </tr>
            <tr>
                <td class="label">Gross Total</td>
                <td class="value">{{ number_format($purchase->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Paid Amount</td>
                <td class="value">{{ number_format($purchase->paid_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Outstanding</td>
                <td class="value outstanding">{{ number_format($purchase->due_amount, 2) }}</td>
            </tr>
        </table>

        <div class="words">
            <strong>Amount in words:</strong>
            {{ \App\Helpers\NumberToWords::convert($purchase->total_amount) }}
        </div>
    </div>

</body>
</html>
