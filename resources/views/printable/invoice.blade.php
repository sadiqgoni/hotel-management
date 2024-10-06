<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
          * {
            font-family: DejaVu Sans, sans-serif;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            font-size: 12px;
            padding: 0px;
        }

        .invoice-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
        }

        .header, .footer {
            text-align: center;
        }

        .header img {
            width: 150px;
            height: auto;
        }

        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .details-section {
            width: 48%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .payment-mode {
            margin: 20px 0;
            display: flex;
            justify-content: space-between;
        }

        .footer {
            font-size: 0.85em;
            color: #555;
        }

        .status {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1.2em;
            color: red;
        }

        .status.credit {
            color: green;
        }

        @media print {
            button, .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="header">
            <img src="{{ asset('images/hotel_logo.png') }}" alt="White24 Palace Logo">
            <h2>White24 Palace</h2>
            <p>Address: Kano, Nigeria</p>
            <p>Invoice #{{ $checkIn->reservation_number  }}</p>
            <p>Issue Date: {{ \Carbon\Carbon::parse($checkIn->issue_date)->format('l, F j, Y') }}</p>
        </div>

        <!-- Status Display (Unpaid or Credit) -->
        <div class="status {{ $checkIn->is_paid ? 'credit' : 'unpaid' }}">
            {{ $checkIn->is_paid ? 'Credit' : 'Unpaid' }}
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="details-section">
                <h4>INVOICED FROM</h4>
                <p>White24 Palace</p>
                <p>Mobile: 09012345678</p>
                <p>Email: whit24palace@gmail.com</p>
            </div>
            <div class="details-section">
                <h4>INVOICED TO</h4>
                <p>Guest Name: Mr {{ $checkIn->guest_name }}</p>
                <p>Mobile: {{ $checkIn->guest_phone }}</p>
            </div>
        </div>

        <!-- Room Rent Table -->
        <table>
            <thead>
                <tr>
                    <th>Room No.</th>
                    <th>Date</th>
                    <th>No. of Nights</th>
                    <th>Price/Night</th>
                    <th>Discount</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $checkIn->room_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($checkIn->check_in_time)->format('d M, Y') }} - {{ \Carbon\Carbon::parse($checkIn->check_out_time)->format('d M, Y') }}</td>
                    <td>{{ $checkIn->number_of_nights }}</td>
                    <td> ₦ {{ number_format($checkIn->price_per_night, 2) }}</td>
                    <td> ₦ {{ number_format($checkIn->coupon_discount, 2) }}</td>
                    <td> ₦ {{ number_format($checkIn->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Mode and Amount -->
        <div class="payment-mode">
            <p>Payment Mode: {{ $checkIn->payment_mode }}</p>
            <p>Amount: {{ number_format($checkIn->amount_paid, 2) }}</p>
        </div>

        <!-- Footer with Terms and Conditions -->
        <div class="footer">
        <h4>TERMS & CONDITIONS</h4>
            <p>1. Payment is due upon receipt of this invoice.</p>
            <p>2. Late payments may result in additional charges.</p>

            <div class="signature">
                <p>Guest Signature: _____________________</p>
                <p>Authorized Signature: _____________________</p>
            </div>
        </div>
    </div>
   
</body>
</html>
