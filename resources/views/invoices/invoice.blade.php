<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>White24 Palace</title>
    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            line-height: 1.7;
            margin: 0;
            padding: 10px;
        }

        .container {
            max-width: 350px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            max-width: 50px;
            margin-bottom: 5px;
        }

        h2,
        p {
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 2px 0;
        }

        .right-align {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
        }

        .footer {
            margin-top: 10px;
            text-align: center;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <!-- <img src="hotel2.png" alt="Hotel Logo" class="logo"> -->
            <h2>White24 Palace </h2>
            <p>Kano, Nigeria</p>
        </div>
        <p>Date: {{ now()->format('M d, Y') }}</p>
        <div class="divider"></div>
        <table>
            <tr>
                <th>Item</th>
                <th class="right-align">Total</th>
            </tr>
            @foreach ($order->orderItems as $item)
                <tr>
                    <td>{{ $item->menuItem->name }}<br>{{ number_format($item->price, 2) }} x {{ $item->quantity }}</td>
                    <td class="right-align">₦{{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="divider"></div>
                    </td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td>Sub - Total Amount</td>
                <td class="right-align">
                    ₦{{ number_format($order->orderItems->sum(fn($item) => $item->price * $item->quantity), 2) }}</td>
            </tr>
            <tr>
                <td>Service Charge</td>
                <td class="right-align">₦{{ number_format($order->service_charge ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="divider"></div>
                </td>
            </tr>
            <tr class="total-row">
                <td>Grand Total</td>
                <td class="right-align">
                    ₦{{ number_format(($order->orderItems->sum(fn($item) => $item->price * $item->quantity)) + ($order->service_charge ?? 0), 2) }}
                </td>
            </tr>

            <tr>
                <td>Change Due</td>
                <td class="right-align">₦{{ number_format($order->change_amount ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td>Total payment</td>
                <td class="right-align">₦{{ number_format($order->amount_paid, 2) }}</td>
            </tr>
        </table>

        @if($order->guest)
            <p><strong>Billing To:</strong> {{ $order->guest->name }}</p>
        @endif

        @if($order->user)
            <p>Bill By: {{ $order->user->name }}</p>
        @endif


        @if($order->table)
            <p><strong>Table:</strong> {{ $order->table->name }} &nbsp;&nbsp;&nbsp; <strong>Order No.:</strong>
                #{{ $order->id }}</p>
        @else
            <p><strong>Order No.:</strong> #{{ $order->id }}</p>
        @endif

        <div class="footer">
            <p>Thank you very much</p>
        </div>
    </div>
</body>

</html>