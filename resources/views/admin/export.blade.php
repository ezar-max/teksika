<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Data Booking</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h2>Data Booking</h2>
    <table>
        <thead>
            <tr>
                <th>Nama User</th>
                <th>Tukang</th>
                <th>Layanan</th>
                <th>Kode Booking</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bookings as $booking)
            <tr>
                <td>{{ $booking->user->name ?? '-' }}</td>
                <td>{{ $booking->worker->name ?? '-' }}</td>
                <td>{{ $booking->layanan }}</td>
                <td>{{ $booking->kode_booking }}</td>
                <td>{{ \Carbon\Carbon::parse($booking->created_at)->format('d-m-Y') }}</td>
                <td>{{ $booking->status_admin }}</td>
                <td>Rp{{ number_format($booking->jumlah_pembayaran, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
