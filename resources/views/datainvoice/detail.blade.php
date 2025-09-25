<div class="modal-header">
    <h5 class="modal-title">Detail Invoice #{{ $invoice->nomor_invoice }}</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    {{-- Bagian Atas: Customer, Pesanan Masuk, Batas Pelunasan --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <label><strong>Customer</strong></label>
            <p class="form-control-plaintext border p-2">
                {{ $invoice->details->first()->pasang->interaksi->customer->customer_nama ?? '-' }}
            </p>
        </div>
        <div class="col-md-4">
            <label><strong>Pesanan Masuk</strong></label>
            <p class="form-control-plaintext border p-2">
                {{ $invoice->pesanan_masuk ?? '-' }}
            </p>
        </div>
        <div class="col-md-4">
            <label><strong>Batas Pelunasan</strong></label>
            <p class="form-control-plaintext border p-2">
                {{ $invoice->batas_pelunasan ?? '-' }}
            </p>
        </div>
    </div>

    {{-- Item Invoice --}}
    <h6 class="mt-3">Item Invoice</h6>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Produk</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
                    <th>Diskon</th>
                    <th>Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->details as $d)
                    <tr>
                        <td>{{ $d->pasang->produk->produk_nama ?? '-' }} — {{ $d->pasang->jadwal_pasang_kirim ?? '' }}
                        </td>
                        <td>Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($d->total, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($d->diskon, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($d->grand_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <hr>

    {{-- Bagian bawah tabel: kiri tanggal, kanan angka --}}
    <div class="row g-3">
        {{-- Kiri --}}
        <div class="col-md-6">
            <div class="mb-2">
                <label>Tanggal DP</label>
                <p class="form-control-plaintext border p-2">
                    {{ $invoice->tanggal_dp ?? '-' }}
                </p>
            </div>
            <div>
                <label>Tanggal Pelunasan</label>
                <p class="form-control-plaintext border p-2">
                    {{ $invoice->tanggal_pelunasan ?? '-' }}
                </p>
            </div>
        </div>

        {{-- Kanan --}}
        <div class="col-md-6">
            <div class="mb-2">
                <label>Potongan Harga</label>
                <p class="form-control-plaintext border p-2">
                    Rp {{ number_format($invoice->potongan_harga, 0, ',', '.') }}
                </p>
            </div>
            <div class="mb-2">
                <label>Cashback</label>
                <p class="form-control-plaintext border p-2">
                    Rp {{ number_format($invoice->cashback, 0, ',', '.') }}
                </p>
            </div>
            <div class="mb-2">
                <label>DP</label>
                <p class="form-control-plaintext border p-2">
                    Rp {{ number_format($invoice->dp, 0, ',', '.') }}
                </p>
            </div>
            <div class="mb-2">
                <label>Sisa Pelunasan</label>
                <p class="form-control-plaintext border p-2">
                    Rp {{ number_format($invoice->sisa_pelunasan, 0, ',', '.') }}
                </p>
            </div>
            <div>
                <label>Total Akhir</label>
                <p class="form-control-plaintext border p-2 fw-bold">
                    Rp {{ number_format($invoice->total_akhir, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Catatan --}}
    <div class="mt-3">
        <label>Catatan</label>
        <p class="form-control-plaintext border p-2">
            {{ $invoice->catatan ?? '-' }}
        </p>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>
