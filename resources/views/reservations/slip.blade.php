<!DOCTYPE html>
<html>
<head>
    <title>Reservation Slip</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; margin-top: 20px; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: left; }
    </style>
</head>
<body>
    <h1>Reservation Slip</h1>

    <p><strong>Reservation ID:</strong> {{ $reservation->id }}</p>
    <p><strong>Guest Name:</strong> {{ $guest->name }}</p>
    <p><strong>Phone Number:</strong> {{ $guest->phone_number }}</p>
    <p><strong>NIN Number:</strong> {{ $guest->nin_number }}</p>
    <p><strong>Room:</strong> {{ $room->room_number }}</p>
    <p><strong>Check-In Date:</strong> {{ $reservation->check_in_date}}</p>
    <p><strong>Check-Out Date:</strong> {{ $reservation->check_out_date}}</p>
    <p><strong>Total Amount:</strong> {{ $reservation->total_amount }}</p>

    <table>
        <thead>
            <tr>
                <th>Service</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <!-- You can loop through additional services or items -->
            <tr>
                <td>Room Charge</td>
                <td>{{ $room->price_per_night }}</td>
            </tr>
            <tr>
                <td>Total</td>
                <td>{{ $reservation->total_amount }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
