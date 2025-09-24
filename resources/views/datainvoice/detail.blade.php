<div class="modal-header">
    <h5 class="modal-title">Detail Invoice #{{ $invoice->nomor_invoice }}</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <div class="row mb-2">
        <div class="col-md-6">
            <label><strong>Customer</strong></label>
            <p>{{ $invoice->customer->customer_nama ?? '-' }}</p>
        </div>
        <div class="col-md-6">
            <label><strong>Pesanan Masuk</strong></label>
            <p>{{ $invoice->pesanan_masuk ?? '-' }}</p>
        </div>
        <div class="col-md-6">
            <label><strong>Batas Pelunasan</strong></label>
            <p>{{ $invoice->batas_pelunasan ?? '-' }}</p>
        </div>
    </div>

    <h6 class="mt-3">Item Invoice</h6>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
                    <th>Diskon</th>
                    <th>Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->details as $d)
                    <tr>
                        <td>{{ $d->pasangkirim->produk->produk_nama ?? '-' }} — {{ $d->pasangkirim->jadwal_pasang_kirim ?? '' }}</td>
                        <td>{{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                        <td>{{ number_format($d->total, 0, ',', '.') }}</td>
                        <td>{{ number_format($d->diskon, 0, ',', '.') }}</td>
                        <td>{{ number_format($d->grand_total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <hr>
    <div class="row">
        <div class="col-md-4">
            <label>Potongan Harga</label>
            <p>{{ number_format($invoice->potongan_harga, 0, ',', '.') }}</p>
        </div>
        <div class="col-md-4">
            <label>Cashback</label>
            <p>{{ number_format($invoice->cashback, 0, ',', '.') }}</p>
        </div>
        <div class="col-md-4">
            <label>Total Akhir</label>
            <p><strong>{{ number_format($invoice->total_akhir, 0, ',', '.') }}</strong></p>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-6">
            <label>DP</label>
            <p>{{ number_format($invoice->dp, 0, ',', '.') }}</p>
        </div>
        <div class="col-md-6">
            <label>Tanggal DP</label>
            <p>{{ $invoice->tanggal_dp ?? '-' }}</p>
        </div>
        <div class="col-md-6">
            <label>Sisa Pelunasan</label>
            <p>{{ number_format($invoice->sisa_pelunasan, 0, ',', '.') }}</p>
        </div>
        <div class="col-md-6">
            <label>Tanggal Pelunasan</label>
            <p>{{ $invoice->tanggal_pelunasan ?? '-' }}</p>
        </div>
        <div class="col-md-12">
            <label>Catatan</label>
            <p>{{ $invoice->catatan ?? '-' }}</p>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>

@push('js')
<script>
    $(document).on('click', '.btn-show-invoice', function () {
    let id = $(this).data('id');
    $.get("{{ url('invoice') }}/" + id, function (res) {
        if (res.status === 'success') {
            $('#myModal .modal-content').html(res.html);
            $('#myModal').modal('show');
        } else {
            toastr.error('Gagal load data invoice');
        }
    }).fail(function () {
        toastr.error('Terjadi kesalahan server');
    });
});
</script>
@endpush