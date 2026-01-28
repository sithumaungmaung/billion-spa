<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* Header Section */
        .invoice-header {
            width: 100%;
            margin-bottom: 30px;
        }

        .invoice-header td {
            border: none;
            vertical-align: top;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            color: #444;
            text-transform: uppercase;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #f2f2f2;
            color: #444;
            font-weight: bold;
            text-align: left;
            padding: 10px 8px;
            border-bottom: 2px solid #ddd;
        }

        td {
            padding: 10px 8px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        /* Summary Section */
        .summary-wrapper {
            margin-top: 20px;
            width: 100%;
        }

        .total-box {
            float: right;
            width: 250px;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }

        .total-row {
            font-size: 14px;
            font-weight: bold;
            display: block;
            text-align: right;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <table class="invoice-header">
        <tr>
            <td>
                <div class="title">BILLION SPA INVOICE</div>
                {{-- <p>
                    <strong>Company Name</strong><br>
                    Contact: 09-xxxxxxxxx<br>
                    Address: Yangon, Myanmar
                </p> --}}
            </td>
            <td class="text-right">
                <p>
                    <strong>Date:</strong> {{ date('d-M-Y H:i') }}<br>

                </p>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="50%">Description</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="15%" class="text-right">Unit Price</th>
                <th width="20%" class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            {{-- Rooms Section --}}
            @foreach ($invoice['rooms'] as $room)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $room['room_name'] }}</strong><br>
                        <small style="color: #666;">Service: {{ $room['service_type']['title'] ?? 'N/A' }}</small>
                    </td>
                    <td class="text-center">{{ $room['quantity'] }}</td>
                    <td class="text-right">{{ number_format($room['unit_price']) }}</td>
                    <td class="text-right">{{ number_format($room['total_price']) }}</td>
                </tr>
            @endforeach

            {{-- Items Section --}}
            @if (count($invoice['items']) > 0)
                @foreach ($invoice['items'] as $item)
                    <tr>
                        <td class="text-center">{{ count($invoice['rooms']) + $loop->iteration }}</td>
                        <td>{{ $item['product']['name'] ?? 'Extra Product' }}</td>
                        <td class="text-center">{{ $item['quantity'] }}</td>
                        <td class="text-right">{{ number_format($item['unit_price']) }}</td>
                        <td class="text-right">{{ number_format($item['total_price']) }}</td>
                    </tr>
                @endforeach
            @endif


            @if (count($invoice['extraServices']) > 0)
                @foreach ($invoice['extraServices'] as $service)
                    <tr>
                        <td class="text-center">{{ count($invoice['items']) + $loop->iteration }}</td>
                        <td>{{ $service['extra_service']['title'] ?? 'Extra Service' }}</td>
                        <td class="text-center">1 </td>
                        <td class="text-right">{{ number_format($service['unit_price']) }}</td>
                        <td class="text-right">{{ number_format($service['total_price']) }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <div class="summary-wrapper">
        <div class="total-box">
            <span class="total-row">
                Total: <span style="color: #2c3e50;">{{ number_format($invoice['total']) }}</span>
            </span>
        </div>
    </div>

    <div style="margin-top: 50px; font-style: italic; color: #888;">
        {{-- * Thank you for your business! --}}
    </div>
</body>

</html>
