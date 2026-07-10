<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    {{-- Kept generic on purpose: browsers print the <title> as a default page
         header, so including the invoice number here made it appear twice
         when printing (once from the browser header, once from the body). --}}
    <title>Invoice</title>
    <style>
        * { box-sizing: border-box; }

        /* Force background colors/images to actually print instead of
           being stripped to black & white by the browser's default print mode */
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

        /* ---------- Summary rows (Advance / Total / Due) ---------- */
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
            width: 78%;
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
        .imei-line { display: flex; align-items: center; margin-bottom: 8px; }
        .imei-line label { font-weight: bold; margin-right: 10px; white-space: nowrap; }
        .imei-boxes { display: flex; }
        .imei-boxes span {
            display: inline-block;
            width: 20px; height: 24px;
            border: 1px solid var(--line-gray);
            margin-right: 2px;
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

            /* Belt-and-suspenders: re-assert color printing on every
               element that carries a background color, since some
               browsers (Chrome/Edge) ignore the html-level rule for
               certain elements unless "Background graphics" is also
               checked in the print dialog. */
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
            <div class="field"><label>Date</label>: {{ $sale->sale_date->format('d M, Y') }}</div>
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
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        {{ $item->product->name ?? 'Deleted Product' }} {{ $item->product->model ?? '' }}
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-end">৳{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
            {{-- pad with a few blank rows so the table keeps the tall paper-form look --}}
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

    <!-- ===== IMEI / Warranty ===== -->
    <div class="meta-block">
        <div style="width:65%">
            <div class="imei-line">
                <label>IMEI NO:</label>
                <div class="imei-boxes">
                    @php $imei = $sale->items->first()->product->imei_serial ?? ''; @endphp
                    @for($d = 0; $d < 15; $d++)
                        <span>{{ $imei[$d] ?? '' }}</span>
                    @endfor
                </div>
            </div>
            <div class="warranty-line">
                <span class="checkbox"></span> Warranty: Two year without parts
            </div>
            <div class="guaranty-line">
                <span class="checkbox"></span> Guaranty: Fifteen days without display
            </div>
        </div>

        <div style="width:32%">
            <table class="summary">
                <tr>
                    <td class="label">Advance</td>
                    <td class="value">৳{{ number_format($sale->paid_amount, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Total</td>
                    <td class="value">৳{{ number_format($sale->total_amount, 2) }}</td>
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
        <div class="sig">Signature ({{ $sale->user->name }})</div>
    </div>

</body>
</html>
