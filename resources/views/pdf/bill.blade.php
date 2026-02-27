<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9pt;
            color: #000;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            /* 225pt = 300px */
            width: 225pt;
        }

        .receipt {
            padding: 10pt;
            width: 100%;
            box-sizing: border-box;
        }

        .title {
            font-size: 14pt;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2pt;
            text-transform: uppercase;
        }

        .header-info {
            text-align: center;
            font-size: 8pt;
            margin-bottom: 10pt;
            border-bottom: 1px dashed #000;
            padding-bottom: 5pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Forces columns to stay within width */
            margin-bottom: 5pt;
        }

        th {
            font-size: 8pt;
            border-bottom: 1px solid #000;
            padding: 3pt 0;
        }

        td {
            font-size: 8pt;
            padding: 4pt 0;
            vertical-align: top;
            word-wrap: break-word;
        }

        /* Adjusted column ratios for 300px width */
        .col-item {
            width: 55%;
        }

        .col-qty {
            width: 15%;
            text-align: center;
        }

        .col-amt {
            width: 30%;
            text-align: right;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            border-top: 1px solid #000;
            margin-top: 5pt;
            padding-top: 5pt;
        }

        .total-row {
            font-size: 11pt;
            font-weight: bold;
            display: block;
            text-align: right;
        }

        .small-text {
            font-size: 7pt;
            color: #555;
            display: block;
        }

        .footer {
            text-align: center;
            font-size: 8pt;
            margin-top: 15pt;
            font-style: italic;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 9pt;
            color: #000;
            line-height: 1.2;
            margin: 0;
            padding: 0;
            width: 100%;
            /* Use full width of the PDF canvas */
        }

        .receipt {
            margin: 0 auto;
            /* This centers the receipt */
            padding: 10pt;
            width: 225pt;
            /* Fixed width: approx 300px */
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="title">Invoice Preview</div>

        <div class="header-info">
            {{-- Invoice: {{ $invoice['invoiceDetail']['invoice_no'] }}<br> --}}
            {{ date('d-M-Y H:i') }}
        </div>

        <table>
            <thead>
                <tr>
                    <th class="col-item">Item</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-amt">Amt</th>
                </tr>
            </thead>
            <tbody>
                {{-- Rooms --}}
                @foreach ($invoice['rooms'] as $room)
                    <tr>
                        <td class="col-item">
                            {{ $room['room_name'] }}
                            <span class="small-text">{{ $room['service_type']['title'] ?? '' }}</span>
                        </td>
                        <td class="col-qty">{{ $room['quantity'] }}</td>
                        <td class="col-amt">
                            {{ number_format($room['total_price']) }}
                            @if ((int) $room['service_type_price'] == 0)
                                <span class="small-text">
                                    Therapist Fees
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach

                {{-- Items --}}
                @foreach ($invoice['items'] as $item)
                    <tr>
                        <td class="col-item">{{ $item['product']['name'] ?? 'Item' }}</td>
                        <td class="col-qty">{{ $item['quantity'] }}</td>
                        <td class="col-amt">{{ number_format($item['total_price']) }}</td>
                    </tr>
                @endforeach

                {{-- Extra Services --}}
                @foreach ($invoice['extraServices'] as $service)
                    <tr>
                        <td class="col-item">{{ $service['extra_service']['title'] ?? 'Service' }}</td>
                        <td class="col-qty">{{ $service['quantity'] }}</td>
                        <td class="col-amt">{{ number_format($service['total_price']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <hr>
        <table>
            <tbody>
                <tr>
                    <td class="col-item">Sub Total</td>
                    <td class="col-qty"> </td>
                    <td class="col-amt">{{ number_format($invoice['total']) }}</td>
                </tr>

                <tr>
                    <td class="col-item">Discount</td>
                    <td class="col-qty">{{ $invoice['disPercentage'] }} %</td>
                    <td class="col-amt"> - {{ $invoice['disAmount'] }}</td>
                </tr>
                <tr>
                    <td class="col-item">service Charge</td>
                    <td class="col-qty">{{ $invoice['serviceChargePercentage'] }} %</td>
                    <td class="col-amt">{{ $invoice['serviceCharge'] }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">

            <div class="total-row">
                TOTAL: {{ number_format($invoice['grandTotal']) }}
            </div>
        </div>

        <div class="footer">
            Thank you for your business!
        </div>
    </div>
</body>

</html>
