<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->nomor_invoice }}</title>
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

        /* Judul INVOICE */
        .header-title {
            font-family: 'Times New Roman', Times, serif;
            font-size: 56px;
            color: #815bb4;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .logo { width: 200px; }

        /* Info Section */
        .section-title {
            font-weight: bold;
            font-size: 10px;
        }

        /* Tabel Barang */
        .border th, .border td { border: 1px solid #ffffff; }
        .border th {
            background: #d8c6e5;
            color: #000000;
            font-size: 10px;
            text-transform: capitalize each word;
            text-align: center;
            padding: 4px;
        }
        .border td { font-size: 10px; padding: 6px; }

        /* Alignments */
        .right { text-align: right; }
        .center { text-align: center; }

        /* Summary */
        .summary {
            margin-top: 8px;
            border: 1px solid #ffffff;
            width: 40%;
            float: right;
        }
        .summary td {
            padding: 6px;
            font-size: 10px;
        }
        .summary .label {
            background: #eee;
            font-weight: bold;
        }

        /* Note */
        .note {
            font-size: 10px;
            margin-top: 150px;
            line-height: 1.5;
        }

        /* Footer */
        .footer { margin-top: 25px; font-size: 11px; }
        .signature { text-align: center; font-size: 10px; }

         /* Garis pembatas */
        .divider {
            border-bottom: 2px solid #000;
            margin: 5px 0 10px 0;
        }

         /* Garis pembatas */
        .divider2 {
            border-bottom: 1px solid #000;
            margin: 5px 0 10px 0;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table>
        <tr>
            <td class="header-title">INVOICE</td>
            <td class="right">
                <img src="{{ public_path('images/Logo WPM.png') }}" alt="Logo" class="logo">
            </td>
        </tr>
    </table>

    <div class="divider"></div>

{{-- Detail Invoice --}}
<table style="margin-top: 15px; width: 100%; font-family: Arial, sans-serif; font-size: 11px; border-collapse: collapse;">
    <tr>
        <td style="font-weight: bold; width: 40%;">
            <table style="border-collapse: collapse; font-size: 11px; line-height: 1.2;">
                <tr>
                    <td style="width: 100px; text-align: right; padding: 2px 4px;">NOMOR INVOICE</td>
                    <td style="padding: 2px 4px;">: {{ $invoice->nomor_invoice }}</td>
                </tr>
                <tr>
                    <td style="text-align: right; padding: 2px 4px;">CUSTOMER ID</td>
                    <td style="padding: 2px 4px;">: {{ $invoice->customer_id }}</td>
                </tr>
            </table>
        </td>
        <td style="width: 40%;"></td> {{-- kolom kosong untuk dorong ke kanan --}}
        <td style="width: 60%; text-align: right;">
            <table style="border-collapse: collapse; font-size: 11px; line-height: 1.2; margin-left: auto;">
                <tr>
                    <td style="font-weight: bold; text-align: right; padding: 2px 4px;">PESANAN MASUK</td>
                    <td style="padding: 2px 4px;">: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; text-align: right; padding: 2px 4px;">BATAS PELUNASAN</td>
                    <td style="padding: 2px 4px;">: <i>H+1 Setelah Pasang</i></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

    <div class="divider2"></div>

{{-- Customer & Payment --}}
<table style="margin-top: 15px; width: 100%; font-family: Arial, sans-serif; font-size: 11px; border-collapse: collapse;">
    <tr>
    <!-- Kolom Customer -->
    <td style="vertical-align: top; width: 50%;">
        <table style="width: 100%; border-collapse: collapse; font-size: 11px; line-height: 1.2;">
            @foreach($invoice->details as $detail)
                @if($detail->pasang && $detail->pasang->interaksi && $detail->pasang->interaksi->customer)
                    <tr>
                        <td style="font-weight: bold; width: 80px; padding: 2px 4px;">Nama</td>
                        <td style="padding: 2px 4px;">: {{ $detail->pasang->interaksi->customer->customer_nama }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; padding: 2px 4px;">Alamat</td>
                        <td style="padding: 2px 4px;">: {{ $detail->pasang->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; padding: 2px 4px;">No Telp</td>
                        <td style="padding: 2px 4px;">: {{ $detail->pasang->interaksi->customer->customer_nohp ?? '-' }}</td>
                    </tr>
                @endif
            @endforeach
        </table>
    </td>
        <!-- Kolom Payment -->
        <td style="vertical-align: top; width: 30%; text-align: left;">
            <span style="font-weight: bold; display: inline-block; margin-bottom: 6px;">
                Payment Info:
            </span><br>
            BCA a/n {{ $invoice->payment_name ?? 'Oktrin Rustika' }} <br>
            {{ $invoice->payment_rek ?? '3151379654' }}
        </td>
    </tr>
</table>


    {{-- Tabel Barang --}}
    <table class="border" style="margin-top: 15px;">
        <thead>
            <tr>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga Satuan</th>
                <th>Total</th>
                <th>Diskon</th>
                <th>Grand Total</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($invoice->details as $details)
                @php
                    $lineTotal = $details->qty * $details->harga_satuan;
                    $diskonNominal = $details->diskon ? ($lineTotal * $details->diskon / 100) : 0;
                    $lineGrand = $lineTotal - $diskonNominal;
                    $grandTotal += $lineGrand;
                @endphp
                <tr>
                    <td class="center">{{ $details->pasang->produk->kategori->kategori_kode ?? '-' }}</td>
                    <td>{{ $details->pasang->produk->kategori->kategori_nama ?? '-' }} {{ $details->pasang->produk->produk_nama ?? '-' }}</td>
                    <td class="center">{{ $details->pasang->kuantitas }}</td>
                    <td class="center">{{ $details->pasang->produk->satuan ?? '-' }}</td>
                    <td class="right">Rp{{ number_format($details->harga_satuan,0,',','.') }}</td>
                    <td class="right">Rp{{ number_format($lineTotal,0,',','.') }}</td>
                    <td class="center" style="color: red;">{{ $details->diskon ? $details->diskon.'%' : '-' }}</td>
                    <td class="right">Rp{{ number_format($lineGrand,0,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <table style="width:100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <tr>
        <!-- Kolom Catatan -->
        <td style="vertical-align: top; width: 40%;">
        <table class="catatan">
            <tr>
            <td><strong><em>Catatan:</em></strong></td>
            </tr>
        </table>
        </td>

        <!-- Kolom Ringkasan -->
        <td style="vertical-align: top; text-align: right; width: 60%;">
        {{-- Ringkasan --}}
            <table class="summary" style="margin-left:auto; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; width: 250px;">
                <tr style="font-weight: bold; text-align: center;">
                    <td class="label" style="padding: 6px; background-color: #d8c6e5; font-weight: bold;">Total</td>
                    <td class="right" style="padding: 6px; background-color: #d8c6e5; text-align: center;">
                        Rp{{ number_format($total ?? 0,0,',','.') }}
                    </td>
                </tr>
                <tr style="text-align: center;">
                    <td class="label" style="padding: 6px; background-color: #ffffff; font-weight: bold;">Potongan Harga</td>
                    <td class="right" style="padding: 6px; background-color: #ffffff; text-align: center;">
                    Rp{{ number_format($discount ?? 0,0,',','.') }}</td>
                </tr>
                <tr style="text-align: center;">
                    <td class="label" style="padding: 6px; background-color: #ffffff; font-weight: bold;">Cashback</td>
                    <td class="right" style="padding: 6px; background-color: #ffffff; text-align: center;">
                    Rp{{ number_format($cashback ?? 0,0,',','.') }}</td>
                </tr>
            <tr style="font-weight: bold; text-align: center;">
                    <td class="label" style="padding: 6px; background-color: #b28ee1; font-weight: bold;">Grand Total</td>
                    <td class="right" style="padding: 6px; background-color: #b28ee1; text-align: center;">
                    Rp{{ number_format($grandTotal ?? 0,0,',','.') }}</td>
            </tr>
                <tr style="text-align: center;">
                    <td class="label" style="padding: 6px; background-color: #ffffff; font-weight: bold;">DP</td>
                    <td class="right" style="padding: 6px; background-color: #ffffff; text-align: center;">
                    Rp{{ number_format($invoice->dp ?? 0,0,',','.') }}</td>
            </tr>
            <tr style="font-weight: bold; text-align: center;">
                    <td class="label" style="padding: 6px; background-color: #b28ee1; font-weight: bold;">Sisa Pelunasan</td>
                    <td class="right" style="padding: 6px; background-color: #b28ee1; text-align: center;">
                    Rp{{ number_format($grandTotal - ($invoice->dp ?? 0),0,',','.') }}</td>
            </tr>
            </table>

        </td>
    </tr>
    </table>

    {{-- Catatan --}}
    <div class="note">
        <p><strong>KETERANGAN:</strong></p>
        <ol>
            <li>DP yang sudah masuk tidak dapat direfund dengan alasan apapun.</li>
            <li>Barang tidak dapat diretur atau dikembalikan.</li>
            <li>Jika terjadi batal/tukar/retur, maka pembayaran hangus secara profesional.</li>
            <li>Barang wajib dipasang maksimal 1 bulan setelah tanggal pembelian  (untuk wallpaper).</li>
            <li>Harga diatas tanpa PPN.</li>
            <li>Jika pemesanan produk dengan jasa pemasangan wajib melakukan pembayaran DP 60% diawal, pelunasan dilakukan setelah
                pemasangan selesai.</li>
            <li>Pengiriman tanpa jasa pasang wajib melakukan pembayaran DP 70% diawal, pelunasan dilakukan setelah barang siap dan sebelum
                barang dikirim.</li>
            <li>Jika pemesanan jasa pasang wajib melakukan pembayaran DP 60%-70% (*syarat dan ketentuan berlaku) diawal, pelunasan
                dilakukan setelah pemasangan selesai.</li>
            <li>Peraturan ini berlaku setelah nota dibuat dan telah dijelaskan oleh Admin.</li>
        </ol>
    </div>

    <p style="text-align:center; margin-top:15px;">
        <strong>Terima kasih telah mempercayakan kami sebagai partner kerjasama Anda!</strong>
    </p>

    <div class="divider"></div>

    {{-- Footer --}}
    <table class="footer">
        <tr>
            <td style="text-align: left; width: 80%;">
                <div style="margin-bottom: 6px;">
                    <span style="font-weight: bold;">Wallpaper Malang ID</span>
                </div>
                <div>
                    <span style="font-weight: bold;">WhatsApp</span>
                    &nbsp; 62 87803144655
                </div>
            <td class="signature" style="text-align: left; width: 20%;">
                <div>Admin,</div>
                {{-- Gambar tanda tangan --}}
                <div style="margin: 0 0 2px 0;"> {{-- atas=0, bawah=2px --}}
                    <img src="{{ public_path('images/ttd Anisa.png') }}" 
                        alt="Tanda Tangan"
                        style="width:100px; height:auto; display:block;">
                </div>
                {{-- Nama + Jabatan --}}
                <div style="text-decoration: underline; line-height: 1;">
                    <strong>Anisa Rahman</strong>
                </div>
                <div style="line-height: 1;">CS & Konsultan</div>
            </td>
        </tr>
    </table>

</body>
</html>
