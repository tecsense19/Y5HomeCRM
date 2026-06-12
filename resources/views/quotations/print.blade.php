<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation {{ $quotation->quotation_number }} – Y5Home Technologies LLP</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}?v={{ time() }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ── Variables ────────────────────────────── */
        :root {
            --primary:        #034b25;   /* dark green */
            --primary-dark:   #022d17;   /* deeper green */
            --primary-mid:    #065e2e;   /* mid green */
            --accent:         #034b25;
            --primary-light:  #e6f4ec;   /* light green tint */
            --text-dark:      #0f1f0f;
            --text-muted:     #4a6050;
            --border:         #a8d5b5;   /* green border */
            --bg-light:       #f2faf5;   /* very light green bg */
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            background: #e0e0e0;
            font-size: 10pt;
            line-height: 1.5;
        }

        /* ── Screen print bar ─────────────────────── */
        .no-print-bar {
            background: var(--primary-dark);
            padding: 12px 24px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 20px;
            font-size: 9.5pt;
            font-weight: 600;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-family: 'Inter', sans-serif;
            transition: all 0.15s;
        }
        .btn-print { background: #ffffff; color: #022d17; }
        .btn-print:hover { background: #e6f4ec; }
        .btn-close  { background: transparent; color: #7fbf99; border: 1px solid #4d9e6e; }
        .btn-close:hover { background: rgba(255,255,255,0.08); }

        /* ── A4 Page wrapper ──────────────────────── */
        .page {
            width: 210mm;
            margin: 20px auto;
            background: #ffffff;
            box-shadow: 0 4px 30px rgba(0,0,0,0.18);
        }

        /* ── ① BRANDED HEADER BAR ─────────────────── */
        .header-bar {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            /* background: var(--primary); */
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Left: Logo panel */
        .hdr-logo {
            background: #ffffff;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 160px;
            max-width: 200px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .hdr-logo img {
            max-height: 56px;
            max-width: 160px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
        }

        /* Centre: Company name */
        .hdr-company {
            flex: 1;
            padding: 14px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .hdr-company .co-name {
            color: #ffffff;
            font-size: 13pt;
            font-weight: 800;
            letter-spacing: 0.2px;
            line-height: 1.1;
        }
        .hdr-company .co-tag {
            color: #a8d5b5;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            margin-top: 4px;
        }

        /* Right: QUOTATION title panel */
        .hdr-title {
            background: #FFFFFF;
            padding: 14px 24px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            justify-content: center;
            min-width: 160px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .hdr-title .doc-word {
            color: var(--primary);
            font-size: 15pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            line-height: 1;
        }
        .hdr-title .doc-no {
            color: var(--primary);
            font-size: 8.5pt;
            margin-top: 5px;
            font-weight: 500;
        }

        /* ── ② META INFO STRIP ────────────────────── */
        .meta-strip {
            background: var(--primary-light);
            border-bottom: 2px solid var(--border);
            display: flex;
            justify-content: space-between;
            gap: 28px;
            padding: 6px 24px;
            font-size: 8pt;
            color: var(--text-muted);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .meta-strip .mi { display: flex; gap: 5px; }
        .meta-strip .mi-label { color: var(--text-muted); }
        .meta-strip .mi-val { color: var(--primary); font-weight: 600; }

        /* ── ③ BODY AREA ──────────────────────────── */
        .body-wrap { padding: 18px 24px 24px; flex: 1; }

        /* ── FROM / FOR TABLE ─────────────────────── */
        .addr-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .addr-table td {
            width: 50%;
            vertical-align: top;
            padding: 12px 16px;
            border: 1px solid var(--border);
        }
        .addr-table td.from-cell {
            background: var(--bg-light);
            border-right: 2px solid var(--primary);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .addr-table td.for-cell {
            background: var(--bg-light);
        }
        .addr-label {
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
            padding-bottom: 4px;
            margin-bottom: 8px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .addr-name {
            font-size: 10.5pt;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }
        .addr-row {
            font-size: 8pt;
            color: var(--text-muted);
            margin-bottom: 2px;
            display: flex;
            gap: 5px;
        }
        .addr-row .ar-key {
            font-weight: 600;
            color: var(--text-dark);
            min-width: 100px;
        }

        /* ── ITEMS TABLE ──────────────────────────── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 8pt;
        }
        .items-table thead {
            display: table-header-group; /* ensures thead repeats on every print page */
        }
        .items-table thead tr {
            background-color: #034b25 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .items-table th {
            color: #ffffff !important;
            background-color: #034b25 !important;
            font-weight: 600;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 8px 6px;
            border: 1px solid #065e2e;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .items-table td {
            padding: 8px 6px;
            border: 1px solid var(--border);
            vertical-align: top;
        }
        .items-table tbody tr:nth-child(even) td {
            background-color: #f2faf5 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .item-name { font-weight: 600; color: var(--text-dark); margin-bottom: 2px; }
        .item-desc { font-size: 7.5pt; color: var(--text-muted); }
        .tr { text-align: right; }
        .tc { text-align: center; }

        /* ── TOTALS TABLE ─────────────────────────── */
        .totals-outer {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 18px;
        }
        .totals-table {
            width: 46%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        .totals-table td {
            padding: 5px 9px;
            border-bottom: 1px solid var(--border);
        }
        .totals-table .tl { color: var(--text-muted); }
        .totals-table .tv { text-align: right; font-weight: 500; }
        .totals-table .disc-r td { color: #065e2e; }
        .totals-table .grand-r td {
            background: var(--primary);
            color: #ffffff;
            font-size: 11pt;
            font-weight: 700;
            padding: 9px 9px;
            border: none;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── Notes ────────────────────────────────── */
        .notes-section {
            background: #ffffff;
        }

        .notes-section .desc {
            white-space:pre-wrap;
            color: var(--text-muted);
            font-size: 7.5pt;
        }            

        /* ── TERMS ────────────────────────────────── */
        .terms-block {
            border-top: 1.5px solid var(--border);
            padding-top: 12px;
            margin-top: 16px;
            font-size: 7.5pt;
            color: var(--text-muted);
        }
        .terms-block .t-title {
            font-weight: 700;
            font-size: 8pt;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 5px;
        }
        .terms-block p { margin-bottom: 3px; }

        /* ── SIGNATURE ROW ────────────────────────── */
        .sig-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 36px;
            font-size: 8pt;
            color: var(--text-muted);
        }
        .sig-box {
            text-align: center;
            width: 150px;
            border-top: 1px solid #aaa;
            padding-top: 5px;
        }

        /* ── FOOTER BAR ───────────────────────────── */
        .footer-bar {
            background: #02381c;
            padding: 7px 24px;
            font-size: 7pt;
            color: #7fbf99;
            display: flex;
            justify-content: space-between;
            align-items: center;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .footer-bar span { color: #c3e3d1; }
        .footer-bar a {
            color: #c3e3d1;
            text-decoration: none;
            font-weight: 600;
        }

        /* ── PRINT OVERRIDES ──────────────────────── */
        @media print {
            body { background: #fff; }
            .no-print-bar { display: none !important; }
            .page {
                width: 100%;
                margin: 0;
                box-shadow: none;
            }
            @page { size: A4; margin: 12mm 12mm 15mm 12mm; }

            /* Force all background colors to print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Prevent page break inside key blocks */
            .addr-table  { page-break-inside: avoid; }
            .totals-outer { page-break-inside: avoid; }
            .terms-block  { page-break-inside: avoid; }
            .notes-section  { page-break-inside: avoid; }
            .sig-row      { page-break-inside: avoid; }
            .footer-bar   { page-break-inside: avoid; }

            /* thead repeats on every page automatically via display:table-header-group */
        }
    </style>
</head>
<body>

<!-- Screen-only action bar -->
<div class="no-print-bar">
    <button onclick="window.print();" class="btn btn-print">
        <svg width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
        </svg>
        Print / Save as PDF
    </button>
    <button onclick="window.close();" class="btn btn-close">✕ Close</button>
</div>

<div class="page">

    <!-- ① BRANDED HEADER -->
    <div class="header-bar">
        <div class="hdr-logo">
            <img src="{{ asset('Y5home_Technologies.webp') }}"
                 alt="Y5Home Logo"
                 onerror="this.onerror=null; this.src='{{ asset('Y5home.png') }}';"
            >
        </div>
        <!-- <div class="hdr-company">
            <div class="co-name">Y5Home Technologies LLP</div>
            <div class="co-tag">Smart Home Automation</div>
        </div> -->
        <div class="hdr-title">
            <div class="doc-word">Quotation</div>
            <div class="doc-no">{{ $quotation->quotation_number }}</div>
        </div>
    </div>

    <!-- ② META STRIP -->
    <div class="meta-strip">
        <div class="mi">
            <span class="mi-label">Date:</span>
            <span class="mi-val">{{ $quotation->quotation_date ? $quotation->quotation_date->format('d M Y') : '–' }}</span>
        </div>
        <div class="mi">
            <span class="mi-label">Last Updated:</span>
            <span class="mi-val">{{ $quotation->updated_at ? $quotation->updated_at->format('d M Y, h:i A') : '–' }}</span>
        </div>
        <div class="mi">
            <span class="mi-label">Version:</span>
            <span class="mi-val">v{{ $quotation->version_number }}</span>
        </div>
        <div class="mi">
            <span class="mi-label">Status:</span>
            <span class="mi-val" style="text-transform:uppercase;">{{ $quotation->status }}</span>
        </div>
    </div>

    <!-- ③ BODY -->
    <div class="body-wrap">

        <!-- FROM / FOR -->
        <table class="addr-table">
            <tr>
                <!-- QUOTATION FROM -->
                <td class="from-cell">
                    @php $ec = $quotation->opportunity && $quotation->opportunity->lead ? $quotation->opportunity->lead->experienceCenter : null; @endphp
                    <div class="addr-label">Quotation From</div>
                    <div class="addr-name">{{ $ec && $ec->company_name ? $ec->company_name : ($ec && $ec->center_name ? $ec->center_name : '-') }}</div>
                    <div class="addr-row">
                        <span class="ar-key">Address:</span>
                        <span>
                            @if($ec && $ec->address)
                                {{ $ec->address }}, {{ $ec->city }}, {{ $ec->state }} - {{ $ec->country }}
                            @elseif($ec && $ec->city)
                                {{ $ec->city }}, {{ $ec->state }} - {{ $ec->country }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="addr-row"><span class="ar-key">GSTIN:</span>
                        <span>{{ $ec && $ec->gst_number ? $ec->gst_number : '-' }}</span>
                    </div>
                    <div class="addr-row"><span class="ar-key">PAN:</span>
                        <span>{{ $ec && $ec->pan_number ? $ec->pan_number : '-' }}</span>
                    </div>
                    <div class="addr-row"><span class="ar-key">Email:</span>
                        <span>{{ $ec && $ec->email ? $ec->email : '-' }}</span>
                    </div>
                    <div class="addr-row"><span class="ar-key">Phone:</span>
                        <span>{{ $ec && $ec->mobile_number ? $ec->mobile_number : '-' }}</span>
                    </div>
                    <div class="addr-row"><span class="ar-key">MSME UDYAM NO:</span>
                        <span>{{ $ec && $ec->msme_udyam_number ? $ec->msme_udyam_number : '-' }}</span>
                    </div>
                </td>

                <!-- QUOTATION FOR -->
                <td class="for-cell">
                    <div class="addr-label">Quotation For</div>
                    <div class="addr-name">{{ $quotation->customer_name }}</div>
                    @if($quotation->customer)
                        @if($quotation->customer->address)
                            <div class="addr-row"><span class="ar-key">Address:</span><span>{{ $quotation->customer->address }}</span></div>
                        @endif
                        <div class="addr-row">
                            <span class="ar-key">City / State:</span>
                            <span>
                                {{ ($quotation->customer->city ?: 'Ahmedabad') . ($quotation->customer->state ? ', ' . $quotation->customer->state : '') . ($quotation->customer->country ? ' – ' . $quotation->customer->country : '') }}
                            </span>
                        </div>
                        @if($quotation->customer->email)
                            <div class="addr-row"><span class="ar-key">Email:</span><span>{{ $quotation->customer->email }}</span></div>
                        @endif
                        @if($quotation->customer->mobile_number)
                            <div class="addr-row"><span class="ar-key">Phone:</span><span>{{ $quotation->customer->mobile_number }}</span></div>
                        @endif
                    @else
                        <div class="addr-row"><span class="ar-key">City / State:</span><span>Ahmedabad, Gujarat – India</span></div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- ITEMS TABLE -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:4%;" class="tc">#</th>
                    <th style="width:27%;">Item Description</th>
                    <th style="width:6%;" class="tr">GST</th>
                    <th style="width:5%;" class="tc">Qty</th>
                    <th style="width:10%;" class="tr">Rate (₹)</th>
                    <th style="width:7%;" class="tr">Disc</th>
                    <th style="width:13%;" class="tr">Amount (₹)</th>
                    <th style="width:14%;" class="tr">CGST (₹)</th>
                    <th style="width:14%;" class="tr">SGST (₹)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subTotal     = 0;
                    $totalDiscount = 0;
                    $totalTaxable = 0;
                    $totalCgst    = 0;
                    $totalSgst    = 0;
                @endphp
                @if($quotation->items && count($quotation->items) > 0)
                    @foreach($quotation->items as $index => $item)
                        @php
                            $qty   = floatval($item['quantity'] ?? 0);
                            $rate  = floatval($item['rate'] ?? 0);
                            $disc  = floatval($item['discount'] ?? 0);
                            $gst   = floatval($item['gst_rate'] ?? 0);

                            $gross   = $qty * $rate;
                            $discAmt = $gross * ($disc / 100);
                            $net     = $gross - $discAmt;
                            $cgst    = $net * ($gst / 2) / 100;
                            $sgst    = $net * ($gst / 2) / 100;

                            $subTotal      += $gross;
                            $totalDiscount += $discAmt;
                            $totalTaxable  += $net;
                            $totalCgst     += $cgst;
                            $totalSgst     += $sgst;
                        @endphp
                        <tr>
                            <td class="tc">{{ $index + 1 }}</td>
                            <td>
                                <div class="item-name">{{ $item['name'] }}</div>
                                @if(!empty($item['description']))
                                    <div class="item-desc">{{ $item['description'] }}</div>
                                @endif
                            </td>
                            <td class="tr">{{ $gst }}%</td>
                            <td class="tc">{{ $qty }}</td>
                            <td class="tr">₹{{ number_format($rate, 2) }}</td>
                            <td class="tr">{{ $disc > 0 ? $disc.'%' : '–' }}</td>
                            <td class="tr">₹{{ number_format($net, 2) }}</td>
                            <td class="tr">₹{{ number_format($cgst, 2) }}</td>
                            <td class="tr">₹{{ number_format($sgst, 2) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" style="text-align:center; color:#aaa; padding:18px;">No items added to this quotation.</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- TOTALS -->
        @php
            $totalPreRound = $totalTaxable + $totalCgst + $totalSgst;
            $grandTotal    = round($totalPreRound);
            $roundUp       = $grandTotal - $totalPreRound;
        @endphp
        <div class="totals-outer">
            <table class="totals-table">
                <tr>
                    <td class="tl">Sub Total</td>
                    <td class="tv">₹{{ number_format($subTotal, 2) }}</td>
                </tr>
                @if($totalDiscount > 0)
                <tr class="disc-r">
                    <td class="tl">Discount</td>
                    <td class="tv">(₹{{ number_format($totalDiscount, 2) }})</td>
                </tr>
                @endif
                <tr>
                    <td class="tl">Taxable Amount</td>
                    <td class="tv">₹{{ number_format($totalTaxable, 2) }}</td>
                </tr>
                @if($totalCgst > 0)
                <tr>
                    <td class="tl">CGST</td>
                    <td class="tv">₹{{ number_format($totalCgst, 2) }}</td>
                </tr>
                @endif
                @if($totalSgst > 0)
                <tr>
                    <td class="tl">SGST</td>
                    <td class="tv">₹{{ number_format($totalSgst, 2) }}</td>
                </tr>
                @endif
                @if($roundUp != 0)
                <tr>
                    <td class="tl">Round Off</td>
                    <td class="tv">₹{{ ($roundUp >= 0 ? '+' : '') . number_format($roundUp, 2) }}</td>
                </tr>
                @endif
                <tr class="grand-r">
                    <td class="tl" style="color:#fff;">Total (INR)</td>
                    <td class="tv" style="color:#fff;">₹{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Notes -->
        <div class="notes-section">
            <div class="addr-label">Notes</div>
            <div class="desc">{{ $quotation->notes }}</div>
        </div>

        <!-- TERMS & CONDITIONS -->
        <div class="terms-block">
            <div class="t-title">Terms &amp; Conditions</div>
            <p>1. Work will resume after advance payment.</p>
            <p>2. Delivery will take 20 to 25 business days after confi rmation of order.</p>
            <p>3. Final invoice would be have 10% to 15% variation based on quantities.</p>
            <p>4. Once order confi rm it will not change or cancel. If this activities happened then advance amount will be forfeited</p>
            <p>4. Transportation charges will be extra.</p>
        </div>

        <!-- SIGNATURE -->
        <div class="sig-row">
            <div>
                Prepared By: <strong style="color:var(--text-dark);">{{ $quotation->preparedBy?->name ?? 'Sales Representative' }}</strong>
            </div>
            <div class="sig-box">
                Authorized Signatory
            </div>
        </div>

    </div><!-- /body-wrap -->

    <!-- FOOTER BAR -->
    <div class="footer-bar">
        @php
            $footerEmail = $ec && $ec->email ? $ec->email : 'info@y5home.in';
            $footerPhone = $ec && $ec->mobile_number ? $ec->mobile_number : '+91 82004 93704';
        @endphp
        <span>For any enquiry, reach out via email at <a href="mailto:{{ $footerEmail }}">{{ $footerEmail }}</a>, call on <a href="tel:{{ $footerPhone }}">{{ $footerPhone }}</a>.</span>
        <span>{{ $quotation->quotation_number }} &nbsp;·&nbsp; {{ $quotation->quotation_date ? $quotation->quotation_date->format('d M Y') : '' }}</span>
    </div>

</div><!-- /page -->

</body>
</html>
