<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Invoice</title>
    <style>
        @page {
            margin: 30px 40px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
        }

        .header {
            position: relative;
            margin-bottom: 20px;
        }

        .logo {
            position: absolute;
            top: 0;
            right: 0;
            width: 70px;
        }

        h3 {
            text-align: center;
            font-size: 14px;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid #888;
            padding: 6px 8px;
            text-align: center;
        }

        th {
            background-color: #a874d1;
            color: #fff;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        .badge-lunas {
            background-color: #28a745;
            color: #fff;
            border-radius: 4px;
            padding: 2px 5px;
            font-size: 9px;
        }

        .badge-belum {
            background-color: #dc3545;
            color: #fff;
            border-radius: 4px;
            padding: 2px 5px;
            font-size: 9px;
        }

        .footer {
            width: 100%;
            text-align: center;
            position: fixed;
            bottom: 10px;
            font-size: 9px;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>Data Invoice</h3>
        <!-- Ganti path logo sesuai lokasi file kamu -->
        <img src="{{ public_path('images/Logo WPM.png') }}" class="logo" alt="Logo">
    </div>

    <table>
        <thead>
            <tr>
                <th>Pesanan Masuk</th>
                <th>No Invoice</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Sisa Pelunasan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $inv)
                <tr>
                    <td>{{ $inv->pesanan_masuk ?? '-' }}</td>
                    <td>{{ $inv->nomor_invoice ?? '-' }}</td>
                    <td>{{ $inv->customer_invoice ?? ($inv->customer->nama ?? '-') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($inv->total_akhir, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($inv->sisa_pelunasan, 0, ',', '.') }}</td>
                    <td>
                        @if (!empty($inv->tanggal_pelunasan))
                            <span class="badge-lunas">Lunas</span>
                        @else
                            <span class="badge-belum">Belum Lunas</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <span>Halaman 1 / 1</span>
    </div>

</body>
</html>
