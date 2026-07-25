<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        * { box-sizing: border-box; }

        html {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            color-adjust: exact;
        }

        :root {
            --brand-orange: #e8542a;
            --line-gray: #b8b8b8;
            --text-dark: #212529;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: var(--text-dark);
            margin: 0;
            padding: 28px 34px;
        }

        .print-bar { text-align: right; margin-bottom: 16px; }
        .print-bar button {
            background: var(--brand-orange); color: #fff; border: none;
            padding: 6px 16px; border-radius: 4px; cursor: pointer; font-size: 14px;
        }

        /* ---------- Header ---------- */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid var(--brand-orange);
            padding-bottom: 14px;
            margin-bottom: 18px;
        }
        .invoice-no {
            font-size: 14px;
            font-weight: bold;
        }
        .invoice-no span { font-weight: normal; margin-left: 6px; }

        .brand-block { text-align: center; flex: 1; }
        .brand-block img { height: 100px; }
        .brand-block .brand-name {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-dark);
            margin: 2px 0 4px;
        }
        .brand-block .brand-name .amp { color: var(--brand-orange); }

        .contact-block {
            text-align: right;
            font-size: 12.5px;
            line-height: 1.7;
        }
        .contact-block .addr {
            margin-top: 4px;
            font-size: 12px;
            color: #555;
        }

        /* ---------- Bill info ---------- */
        .bill-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .bill-row .field { margin-bottom: 10px; }
        .bill-row .field label {
            display: inline-block;
            width: 60px;
            font-weight: bold;
        }
        .bill-row .left { width: 62%; }
        .bill-row .right { width: 34%; text-align: left; }

        /* ---------- Items table ---------- */
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        table.items th {
            background: var(--brand-orange);
            color: #fff;
            text-align: left;
            padding: 10px 12px;
            font-size: 13.5px;
            font-weight: 600;
        }
        table.items th.text-center,
        table.items td.text-center { text-align: center; }
        table.items th.text-end,
        table.items td.text-end { text-align: right; }

        table.items td {
            border-left: 1px solid #e2e2e2;
            border-right: 1px solid #e2e2e2;
            border-bottom: 1px solid #e2e2e2;
            padding: 9px 12px;
            font-size: 13.5px;
            vertical-align: top;
        }
        table.items tbody tr:nth-child(even) { background: #fafafa; }
        .muted { color: #6c757d; font-size: 12px; }
        .item-imei-tag {
            font-size: 11px;
            color: #495057;
            margin-top: 2px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* ---------- Summary rows ---------- */
        table.summary {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }
        table.summary td {
            border-left: 1px solid #e2e2e2;
            border-right: 1px solid #e2e2e2;
            border-bottom: 1px solid #e2e2e2;
            padding: 8px 12px;
            font-size: 13.5px;
        }
        table.summary td.label {
            background: var(--brand-orange);
            color: #fff;
            font-weight: 600;
            width: 65%;
        }
        table.summary td.value { text-align: right; }
        table.summary tr.due-row td.value { color: #dc3545; font-weight: bold; }

        /* ---------- IMEI / Warranty block ---------- */
        .meta-block {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border: 1px solid #e2e2e2;
            border-top: none;
            padding: 12px;
            font-size: 13px;
        }
        .imei-line {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .imei-line label {
            font-weight: bold;
            margin-right: 10px;
            white-space: nowrap;
            min-width: 65px;
        }
        .imei-boxes { display: flex; flex-wrap: wrap; gap: 2px; }
        .imei-boxes span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 24px;
            border: 1px solid var(--line-gray);
            font-size: 12px;
            font-weight: bold;
            background: #fff;
        }
        .warranty-line, .guaranty-line {
            margin-bottom: 4px;
        }
        .checkbox {
            display: inline-block;
            width: 12px; height: 12px;
            border: 1px solid var(--text-dark);
            margin-right: 6px;
            position: relative;
            top: 1px;
        }
        .checkbox.checked::after {
            content: "✓";
            position: absolute;
            left: 1px; top: -4px;
            font-size: 11px;
        }

        /* ---------- Footer ---------- */
        .footer-note {
            margin-top: 22px;
            font-size: 12px;
            text-align: center;
            color: #333;
            line-height: 1.6;
        }
        .footer-note .title { font-weight: bold; }
        .footer-note .terms { margin: 6px 0; }
        .footer-note .policy { font-weight: 600; margin-top: 8px; }

        .signature-row {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            font-size: 13px;
        }
        .signature-row .sig {
            width: 220px;
            text-align: center;
            border-top: 1px solid var(--text-dark);
            padding-top: 6px;
        }

        @media print {
            .print-bar { display: none; }
            body { padding: 0; }

            *, *::before, *::after {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            @page {
                margin: 12mm;
            }
        }
    </style>
</head>
<body>

    <div class="print-bar">
        <button onclick="window.print()">Print</button>
    </div>

    <!-- ===== Header ===== -->
    <div class="invoice-header">
        <div class="invoice-no">
            Invoice No. <span>{{ $sale->invoice_no }}</span>
        </div>

        <div class="brand-block">
            <img src="{{ asset('assets/img/apple&series2.png.png') }}" alt="Apple &amp; Series">
        </div>

        <div class="contact-block">
            <div>📞 01777432652, 01631110444</div>
            <div>✉️ appleandseries@gmail.com</div>
            <div>📘 facebook.com/apple&amp;series</div>
            <div class="addr">Shop-34C, Block-C Level-4 Jamuna Future Park</div>
        </div>
    </div>

    <!-- ===== Bill info ===== -->
    <div class="bill-row">
        <div class="left">
            <div class="field"><label>Name</label>: {{ $sale->customer->name ?? 'Walk-in Customer' }}</div>
            <div class="field"><label>Address</label>: {{ $sale->customer->address ?? '' }}</div>
        </div>
        <div class="right">
            <div class="field"><label>Date</label>: {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M, Y') }}</div>
            <div class="field"><label>Mobile</label>: {{ $sale->customer->phone ?? '' }}</div>
        </div>
    </div>

    <!-- ===== Items table ===== -->
    <table class="items">
        <thead>
            <tr>
                <th style="width:8%">NO.</th>
                <th>ITEM DESCRIPTION</th>
                <th class="text-center" style="width:12%">QTY</th>
                <th class="text-end" style="width:18%">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $i => $item)
                @php
                    // Resolve IMEI: First look in sale_item, then fallback to product relationship
                    $itemImei = $item->imei_serial
                        ?? optional($item->product->purchaseItemImeis->first())->imei_serial
                        ?? '';
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div>
                            <strong>{{ $item->product->name ?? 'Deleted Product' }}</strong>
                            {{ $item->product->model ?? '' }}
                            @if(!empty($item->product->country_code))
                                <span class="muted">({{ strtoupper($item->product->country_code) }})</span>
                            @endif
                        </div>
                        @if($itemImei)
                            <div class="item-imei-tag">IMEI: {{ $itemImei }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">৳{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach

            {{-- Pad with blank rows to retain structured invoice layout --}}
            @for($p = 0; $p < max(0, 6 - count($sale->items)); $p++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-end">&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <!-- ===== IMEI / Warranty & Summary Block ===== -->
    <div class="meta-block">
        <div style="width:60%">
            @foreach($sale->items as $item)
                @php
                    $imeiStr = $item->imei_serial
                        ?? optional($item->product->purchaseItemImeis->first())->imei_serial
                        ?? '';
                    $imeiDigits = str_split(substr(preg_replace('/[^0-9A-Za-z]/', '', $imeiStr), 0, 15));
                @endphp
                <div class="imei-line">
                    <label>IMEI NO:</label>
                    <div class="imei-boxes">
                        @for($d = 0; $d < 15; $d++)
                            <span>{{ $imeiDigits[$d] ?? '' }}</span>
                        @endfor
                    </div>
                </div>
            @endforeach
            <div class="warranty-line mt-2">
                <span class="checkbox checked"></span> Warranty: Two year without parts
            </div>
            <div class="guaranty-line">
                <span class="checkbox checked"></span> Guaranty: Fifteen days without display
            </div>
        </div>

        <div style="width:38%">
            <table class="summary">
                @if(($sale->discount ?? 0) > 0)
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value">৳{{ number_format($sale->total_amount + $sale->discount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Discount</td>
                        <td class="value">-৳{{ number_format($sale->discount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="label">Total</td>
                    <td class="value">৳{{ number_format($sale->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Advance</td>
                    <td class="value">৳{{ number_format($sale->paid_amount, 2) }}</td>
                </tr>
                <tr class="due-row">
                    <td class="label" style="background:#6c757d;">Due</td>
                    <td class="value">৳{{ number_format($sale->due_amount, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ===== Footer ===== -->
    <div class="footer-note">
        <div class="title">Dear Honorable Customer :</div>
        <div class="terms">
            We provide 15 days replacement warranty for manufacturing defects for phone. No claim or no warranty
            will be entertained for physical damage/unauthorized software installation. Please check your device
            before purchase/delivery. Sorry for your any inconvenience.
        </div>
        <div class="policy">
            No cash back if you cash back 20-30% less.<br>
            No physical damage no water damage phone Dead not allowed
        </div>
    </div>

    <div class="signature-row">
        <div class="sig">Customer Signature</div>
        <div class="sig">Signature ({{ $sale->user->name ?? 'Authorized' }})</div>
    </div>

</body>
</html>
