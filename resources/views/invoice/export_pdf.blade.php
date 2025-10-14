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
    .logo {
        width: 160px;
        padding-top: 10px; /* atur sesuai kebutuhan */
    }

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
            margin-top: 5px;
            border: 1px solid #ffffff;
            width: 40%;
            float: right;
            border-spacing: 0;
        }
        .summary td {
            padding: 0.5px 1px;
            font-size: 10px;
            line-height: 0.8;
        }
        .summary .label {
            background: #eee;
            font-weight: bold;
        }

        .note {
            font-size: 10px;
            margin-top: 0px;
            line-height: 1.4;
            clear: both;
        }

        /* Footer */
        .footer { margin-top: 5px; font-size: 10ch; }
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

        .overlay-lunas {
        position: absolute;
        top: 70%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.8;
        z-index: 0;
        width: 160px;
    }
    .summary-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    </style>
</head>
<body>

    {{-- Header --}}
    <table>
        <tr>
            <td class="header-title" style="vertical-align: middle;">INVOICE</td>
            <td class="right" style="vertical-align: middle;">
                <img src="{{ public_path('images/Logo WPM.png') }}" alt="Logo" class="logo" style="padding-top: 10px;">
            </td>
        </tr>
    </table>

    <div class="divider"></div>

{{-- Detail Invoice --}}
<table style="border-collapse: collapse; font-size: 11px; line-height: 1.6; width:100%;">
    <tr>
        {{-- Bagian kiri --}}
        <td style="width:60%; vertical-align: top;">
            <table style="border-collapse: collapse; font-size: 11px; line-height: 1.4;">
                <tr>
                    <td style="font-weight:bold; width:120px; padding:2px 4px;">NOMOR INVOICE</td>
                    <td style="width:10px; padding:2px 4px;">:</td>
                    <td style="padding:2px 4px;">{{ $invoice->nomor_invoice }}</td>
                </tr>
                <tr>
                    <td style="font-weight:bold; padding:2px 4px;">CUSTOMER ID</td>
                    <td style="padding:2px 4px;">:</td>
                    <td style="padding:2px 4px;">{{ $invoice->customer_invoice}}</td>
                </tr>
            </table>
        </td>

        {{-- Bagian kanan --}}
        <td style="width:40%; vertical-align: top;">
            <table style="border-collapse: collapse; font-size: 11px; line-height: 1.4; margin-left:auto;">
                <tr>
                    <td style="font-weight: bold; padding: 2px 4px;">PESANAN MASUK</td>
                    <td style="width:10px; padding:2px 4px;">:</td>
                    <td style="padding: 2px 4px;">
                        {{ $invoice->pesanan_masuk ? \Carbon\Carbon::parse($invoice->pesanan_masuk)->format('d/m/Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; padding: 2px 4px;">BATAS PELUNASAN</td>
                    <td style="padding:2px 4px;">:</td>
                    <td style="padding: 2px 4px;">
                        @if($invoice->batas_pelunasan && strtotime($invoice->batas_pelunasan) !== false)
                            {{ \Carbon\Carbon::parse($invoice->batas_pelunasan)->format('d/m/Y') }}
                        @else
                            {{ $invoice->batas_pelunasan ?? '-' }}
                        @endif
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
<td style="padding: 2px 4px;">
    : 
    @php
        $nohp = $detail->pasang->interaksi->customer->customer_nohp ?? '-';
        // Tambahkan 0 di depan jika belum ada
        if ($nohp !== '-' && !str_starts_with($nohp, '0')) {
            $nohp = '0' . $nohp;
        }
    @endphp
    {{ $nohp }}
</td>
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
                    <td class="center">Rp{{ number_format($details->harga_satuan,0,',','.') }}</td>
                    <td class="center">Rp{{ number_format($details->total,0,',','.') }}</td>
                    <td class="center">
                        @if($details->diskon !== null && $details->diskon > 0)
                            <span style="color:red; font-weight:bold;">
                                {{ number_format($details->diskon, 0) }}%
                            </span>
                        @endif
                    </td>
                    <td class="center">Rp{{ number_format($details->grand_total,0,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width:100%; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px;">
    <tr>
        <!-- Kolom Catatan -->
        <td style="vertical-align: top; width: 40%;">
            <table class="catatan" style="font-family: Arial, sans-serif; border-collapse: collapse;">
                <tr>
                    <td style="font-size: 12px;"><strong><em>Catatan:</em></strong></td>
                </tr>
                <tr>
                    <td style="padding-top: 5px; font-size: 10px;">
                        {{ $invoice->catatan ?? '-' }}
                    </td>
                </tr>
            </table>
        </td>
        <!-- Kolom Ringkasan -->
        <td style="vertical-align: top; text-align: right; width: 60%; position: relative;">
        <div class="summary-wrapper" style="position: relative; display: inline-block;">
        {{-- Ringkasan --}}
            <table class="summary" style="margin-left:auto; border-collapse: collapse; font-family: Arial, sans-serif; font-size: 12px; width: 250px;">
                <tr style="font-weight: bold; text-align: center;">
                    <td style="width: 80px;"></td> <!-- kosong -->
                    <td class="label" style="padding: 6px; background-color: #d8c6e5; font-weight: bold;">Total</td>
                    <td class="right" style="padding: 6px; background-color: #d8c6e5; text-align: center;">
                        Rp{{ number_format($invoice->total_produk, 0, ',', '.') }}
                    </td>
                </tr>
                <tr style="text-align: center;">
                    <td style="width: 80px;"></td> <!-- kosong -->
                    <td class="label" style="padding: 6px; background-color: #ffffff; font-weight: bold;">Potongan Harga</td>
                    <td class="right" style="padding: 6px; background-color: #ffffff; text-align: center;">
                    Rp{{ number_format($invoice->potongan_harga ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr style="text-align: center;">
                    <td style="width: 80px;"></td>
                    <td class="label" style="padding: 6px; background-color: #ffffff; font-weight: bold;">Cashback</td>
                    <td class="right" style="padding: 6px; background-color: #ffffff; text-align: center;">
                    Rp{{ number_format($invoice->cashback ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr style="text-align: center;">
                    <td style="width: 80px;"></td>
                    <td class="label" style="padding: 6px; background-color: #ffffff; font-weight: bold;">
                        PPN ({{ number_format($invoice->ppn ?? 0, 0, ',', '.') }}%)
                    </td>
                    <td class="right" style="padding: 6px; background-color: #ffffff; text-align: center;">
                        Rp{{ number_format($invoice->nominal_ppn ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                <tr style="font-weight: bold; text-align: center;">
                    <td style="width: 80px;"></td>
                    <td class="label" style="padding: 6px; background-color: #b28ee1; font-weight: bold;">Grand Total</td>
                    <td class="right" style="padding: 6px; background-color: #b28ee1; text-align: center;">
                    Rp{{ number_format($invoice->total_akhir ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr style="font-weight: bold; text-align: center;">
                    <td style="padding: 6px; text-align: right; font-size: 11px; font-style: italic; white-space: nowrap;">
                        {{ $invoice->tanggal_dp ? \Carbon\Carbon::parse($invoice->tanggal_dp)->format('d/m/Y') : '' }}
                    </td>
                    <td class="label" style="padding: 6px; background-color: #ffffff; font-weight: bold;">DP</td>
                    <td class="right" style="padding: 6px; background-color: #ffffff; text-align: right;">
                    Rp{{ number_format($invoice->dp ?? 0,0,',','.') }}
                    </td>
                </tr>
                <tr style="font-weight: bold; text-align: center;">
                    <td style="padding: 6px; text-align: right; font-size: 11px; font-style: italic; white-space: nowrap;">
                        {{ $invoice->tanggal_pelunasan ? \Carbon\Carbon::parse($invoice->tanggal_pelunasan)->format('d/m/Y') : '' }}
                    </td>
                    <td class="label" style="padding: 6px; background-color: #b28ee1; font-weight: bold;">Sisa Pelunasan</td>
                    <td class="right" style="padding: 6px; background-color: #b28ee1; text-align: center;">
                    Rp{{ number_format($invoice->sisa_pelunasan ?? 0, 0, ',', '.') }}</td>
                </tr>
            </table>
            </td>
            {{-- Gambar LUNAS --}}
            @if(!empty($invoice->tanggal_pelunasan))
            <tr>
                <td style="text-align:right; padding:20;" colspan="100%">
                    <img src="{{ public_path('images/stempel lunas.png') }}"
                        alt="LUNAS"
                        style="width: 110px; opacity:0.8; margin:0; margin-top:5px;">
                </td>
            </tr>
            @endif
        </div>
    </tr>
    </table>

{{-- Catatan --}}
<div class="note">
    <p><strong>KETERANGAN:</strong></p>

    @if($invoice_keterangan && $invoice_keterangan->keterangan)
        <ol>
            @foreach(explode("\n", $invoice_keterangan->keterangan) as $baris)
                <li>{{ $baris }}</li>
            @endforeach
        </ol>
    @else
        <p><em>Tidak ada keterangan.</em></p>
    @endif
</div>


    <p style="text-align:center; margin-top:0px;">
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
                    <img src="{{ asset('storage/' . Auth::user()->ttd) }}" 
                        alt="Tanda Tangan"
                        style="width:100px; height:auto; display:block;">
                </div>
                {{-- Nama + Jabatan --}}
                <div style="text-decoration: underline; line-height: 1;">
                    <strong>{{ Auth::user()->nama }}</strong>
                </div>
                <div style="line-height: 1;">CS & Konsultan</div>
            </td>
        </tr>
    </table>

</body>
</html>
