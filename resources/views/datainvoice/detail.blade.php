<div class="modal-header bg-wallpaper-gradient text-white">
            <h5 class="modal-title">Detail Invoice</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="text-white">&times;</span>
            </button>
        </div>

<div class="modal-body">
    <!-- Nomor & Customer -->
    <div class="row mb-2">
        <div class="col-md-6">
            <label>Nomor Invoice</label>
            <input type="text" class="form-control" value="{{ $invoice->nomor_invoice }}" disabled>
        </div>
        <div class="col-md-6">
            <label>Customer Invoice</label>
            <input type="text" class="form-control"
                value="{{ $invoice->customer_invoice ?? ($invoice->customer->customer_nama ?? '-') }}" disabled>
        </div>
    </div>

    <!-- Pesanan masuk & Batas pelunasan -->
    <div class="row mb-2">
        <div class="col-md-6">
            <label>Pesanan Masuk</label>
            <input type="date" class="form-control" value="{{ $invoice->pesanan_masuk }}" disabled>
        </div>
        <div class="col-md-6">
            <label>Batas Pelunasan</label>
            <input type="text" class="form-control" value="{{ $invoice->batas_pelunasan }}" disabled>
        </div>
    </div>

    {{-- Item Invoice --}}
    <h6 class="mt-3">Item Invoice</h6>
    <!-- Table Pasang/Kirim -->
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>Pasang/Kirim</th>
                    <th>Kuantitas</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
                    <th>Diskon (%)</th>
                    <th>Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->details as $detail)
                    @php
                        $p = $detail->pasang;
                        $produk = $p->produk ?? null;
                    @endphp
                    <tr>
                        <td>{{ $produk->produk_nama ?? '-' }} — {{ $p->jadwal_pasang_kirim ?? '-' }}</td>
                        <td><input type="number" class="form-control" value="{{ $p->kuantitas ?? 1 }}" disabled></td>
                        <td><input type="text" class="form-control" value="Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}" disabled></td>
                        <td><input type="text" class="form-control" value="Rp {{ number_format($detail->total, 0, ',', '.') }}" disabled></td>
                        <td><input type="text" class="form-control" value="{{ $detail->diskon }}" disabled></td>
                        <td><input type="text" class="form-control" value="Rp {{ number_format($detail->grand_total, 0, ',', '.') }}" disabled></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <hr>

    <div class="row">
        <!-- Kiri -->
        <div class="col-md-6">
            <label>Tanggal DP</label>
            <input type="date" class="form-control" value="{{ $invoice->tanggal_dp }}" disabled>

            <label class="mt-2">Tanggal Pelunasan</label>
            <input type="date" class="form-control" value="{{ $invoice->tanggal_pelunasan }}" disabled>
        </div>

        <!-- Kanan -->
        <div class="col-md-6">
            <label>Potongan Harga</label>
            <input type="text" class="form-control" value="Rp {{ number_format($invoice->potongan_harga, 0, ',', '.') }}" disabled>

            <label class="mt-2">Cashback</label>
            <input type="text" class="form-control" value="Rp {{ number_format($invoice->cashback, 0, ',', '.') }}" disabled>

            <label class="mt-2">DP</label>
            <input type="text" class="form-control" value="Rp {{ number_format($invoice->dp, 0, ',', '.') }}" disabled>

            <label class="mt-2">Sisa Pelunasan</label>
            <input type="text" class="form-control" value="Rp {{ number_format($invoice->sisa_pelunasan, 0, ',', '.') }}" disabled>

            <label class="mt-2">Total Akhir</label>
            <input type="text" class="form-control" value="Rp {{ number_format($invoice->total_akhir, 0, ',', '.') }}" disabled>
        </div>
    </div>

    <!-- Catatan -->
    <div class="row mt-3">
        <div class="col-12">
            <label>Catatan</label>
            <textarea class="form-control" rows="2" disabled>{{ $invoice->catatan ?? '-' }}</textarea>
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
