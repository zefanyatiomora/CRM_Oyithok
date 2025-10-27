<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 0;
            padding: 20px;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        td, th { 
            padding: 6px; 
            vertical-align: top; 
        }
        .logo {
            width: 160px;
            padding-top: 10px; /* atur sesuai kebutuhan */
        }

        /* Info Section */
        .section-title {
            font-weight: bold;
            font-size: 10px;
        }

        .border-bottom-header {
            border-bottom: 1px solid;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid #dee2e6; /* Menggunakan warna border yang lebih lembut */
        }
        /* Alignments */
        .right { text-align: right; }
        .center { text-align: center; }

        /* Footer */
        .footer { margin-top: 5px; font-size: 10ch; }
        .signature { text-align: center; font-size: 10px; }

        /* Garis pembatas */
        .divider {
            border-bottom: 2px solid #000;
            margin: 5px 0 10px 0;
        }

        /* ✨ --- CSS BARU/MODIFIKASI UNTUK TABEL BERWARNA --- ✨ */
        .border-all thead th {
            background-color: #8147be; /* Warna ungu seperti di gambar */
            color: #fff;              /* Teks putih */
            text-align: center;       /* Rata tengah untuk header */
            border-color: #8147be;    /* Border header sesuai warna background */
        }

        .border-all tbody td {
            text-align: center; /* Rata tengah untuk kolom No */
        }

        .border-all tbody tr:nth-child(even) {
            background-color: #f8f9fa; /* Warna selang-seling untuk baris genap (opsional) */
        }
        .border-all tbody tr:nth-child(odd) {
            background-color: #ffffff; /* Warna selang-seling untuk baris ganjil (opsional) */
        }
        /* Hanya untuk kolom pertama (No) */
        .border-all tbody tr td:first-child {
            text-align: center;
        }
        /* Untuk kolom lainnya (Kategori, Nama Produk, Satuan) */
        .border-all tbody tr td:not(:first-child) {
            text-align: left;
        }


    </style>
</head>
<body>

    {{-- Header --}}
    <table>
        <tr>
            <td class="right" style="vertical-align: middle;">
                <img src="{{ public_path('images/Logo WPM.png') }}" alt="Logo" class="logo" style="padding-top: 10px;">
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    <h3 class="center">LAPORAN DATA INVOCE</h4>
        <table class="border-all">
            <thead>
            <tr>
                <th>No</th>
                <th>Pesanan Masuk</th>
                <th>No Invoice</th>
                <th>Customer ID</th>
                <th>Total</th>
                <th>Sisa Pelunasan</th>
                <th>Status</th>
            </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $p)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $p->pesanan_masuk }}</td>
                            <td>{{ $p->nomor_invoice }}</td>
                            <td>{{ $p->customer_invoice ?? '-' }}</td>
                            <td>Rp {{ number_format($p->total_akhir, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($p->sisa_pelunasan, 0, ',', '.') }}</td>
                            <td>
                                @if (!empty($p->tanggal_pelunasan))
                                    Lunas
                                @else
                                    Belum Lunas
                                @endif
                            </td>
                        </tr>
                @endforeach
            </tbody>
        </table>
</body>
</html>