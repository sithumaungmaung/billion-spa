<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            /* ✅ Important */
            font-size: 13px;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border-bottom: 1px solid #ddd;
            padding: 6px;
        }

        .total {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2>INVOICE</h2>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th align="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            {{-- Rooms --}}
            @foreach ($invoice['rooms'] as $room)
                <tr>
                    <td>{{ $room['room_name'] }} (Service Type {{ $room['service_type'] }})</td>
                    <td>{{ $room['quantity'] }}</td>
                    <td>{{ number_format($room['unit_price']) }}</td>
                    <td align="right">{{ number_format($room['total_price']) }}</td>
                </tr>
            @endforeach

            {{-- Items --}}
            @foreach ($invoice['items'] as $item)
                <tr>
                    <td>{{ $item['product']['name'] ?? 'Product' }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ number_format($item['unit_price']) }}</td>
                    <td align="right">{{ number_format($item['total_price']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total" align="right">
        Total: {{ number_format($invoice['total']) }} MMK
    </p>
</body>

</html>
