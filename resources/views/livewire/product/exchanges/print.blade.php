<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    {{-- Kept generic: the browser prints <title> as its own page header,
         so keeping the exchange number here would show it twice on paper. --}}
    <title>Exchange</title>
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
        .invoice-no { font-size: 14px; font-weight: bold; }
        .invoice-no span { font-weight: normal; margin-left: 6px; }
        .invoice-no .type-tag {
            display: block;
            margin-top: 4px;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--brand-orange);
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .brand-block { text-align: center; flex: 1; }
        .brand-block img { height: 100px; }

        .contact-block { text-align: right; font-size: 12.5px; line-height: 1.7; }
        .contact-block .addr { margin-top: 4px; font-size: 12px; color: #555; }

        /* ---------- Meta row (date / customer) ---------- */
        .bill-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .bill-row .field { margin-bottom: 6px; }
        .bill-row .field label { display: inline-block; width: 80px; font-weight: bold; }

        /* ---------- Old / New item tables ---------- */
        .two-col { display: flex; gap: 18px; margin-bottom: 0; }
        .two-col > div { flex: 1; }

        table.item-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.item-table th {
            background: var(--brand-orange);
            color: #fff;
            text-align: left;
            padding: 9px 12px;
            font-size: 13.5px;
            font-weight: 600;
        }
        table.item-table td {
            border-left: 1px solid #e2e2e2;
            border-right: 1px solid #e2e2e2;
            border-bottom: 1px solid #e2e2e2;
            padding: 8px 12px;
            font-size: 13.5px;
        }
        table.item-table td:first-child {
            font-weight: 600;
            width: 40%;
            color: #555;
        }
        table.item-table tbody tr:nth-child(even) { background: #fafafa; }

        /* ---------- Summary ---------- */
        table.summary {
            width: 300px;
            margin: 18px 0 0 auto;
            border-collapse: collapse;
        }
        table.summary td {
            border-left: 1px solid #e2e2e2;
            border-right: 1px solid #e2e2e2;
            border-bottom: 1px solid #e2e2e2;
            padding: 8px 12px;
            font-size: 13.5px;
        }
        table.summary td.label { background: #f8f9fa; width: 65%; }
        table.summary td.value { text-align: right; }
        table.summary tr.total-row td {
            background: var(--brand-orange);
            color: #fff;
            font-weight: 700;
            font-size: 14.5px;
        }

        /* ---------- Notes / footer ---------- */
        .notes-block {
            margin-top: 18px;
            font-size: 13px;
            border: 1px solid #e2e2e2;
            padding: 10px 12px;
        }
        .notes-block strong { color: var(--brand-orange); }

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
            @page { margin: 12mm; }
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
            Exchange No. <span>#{{ $exchange->id }}</span>
            <span class="type-tag">{{ ucwords(str_replace('_', ' ', $exchange->exchange_type)) }}</span>
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

    <!-- ===== Meta row ===== -->
    <div class="bill-row">
        <div>
            <div class="field"><label>Customer</label>: {{ $exchange->sale->customer->name ?? 'Walk-in Customer' }}</div>
        </div>
        <div>
            <div class="field"><label>Date</label>: {{ $exchange->exchange_date->format('d M, Y') }}</div>
        </div>
    </div>

    <!-- ===== Old / New item tables ===== -->
    <div class="two-col">
        <div>
            <table class="item-table">
                <tr><th colspan="2">Old / Traded-In Item</th></tr>
                <tr>
                    <td>Item</td>
                    <td>{{ $exchange->oldProduct->name ?? $exchange->old_product_description ?? '—' }}</td>
                </tr>
                <tr>
                    <td>Condition</td>
                    <td>{{ ucfirst($exchange->condition) }}</td>
                </tr>
                <tr>
                    <td>Return Value</td>
                    <td>৳{{ number_format($exchange->old_product_return_value, 2) }}</td>
                </tr>
            </table>
        </div>
        <div>
            <table class="item-table">
                <tr><th colspan="2">New Item</th></tr>
                <tr>
                    <td>Item</td>
                    <td>{{ $exchange->newProduct->name ?? '—' }}</td>
                </tr>
                <tr>
                    <td>Price</td>
                    <td>৳{{ number_format($exchange->new_product_price, 2) }}</td>
                </tr>
                <tr>
                    <td>Customer</td>
                    <td>{{ $exchange->sale->customer->name ?? 'Walk-in' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ===== Summary ===== -->
    <table class="summary">
        <tr>
            <td class="label">New Item Price</td>
            <td class="value">৳{{ number_format($exchange->new_product_price, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Old Item Credit</td>
            <td class="value">- ৳{{ number_format($exchange->old_product_return_value, 2) }}</td>
        </tr>
        <tr class="total-row">
            <td class="label">{{ $exchange->additional_payment >= 0 ? 'Customer Paid' : 'Refunded' }}</td>
            <td class="value">৳{{ number_format(abs($exchange->additional_payment), 2) }}</td>
        </tr>
    </table>

    @if($exchange->notes)
        <div class="notes-block">
            <strong>Notes:</strong> {{ $exchange->notes }}
        </div>
    @endif

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
        <div class="sig">Signature ({{ $exchange->sale->user->name ?? 'Staff' }})</div>
    </div>

</body>
</html>
