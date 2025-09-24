<!-- resources/views/rekap/create_invoice.blade.php -->
<div class="modal-header">
    <h5 class="modal-title">Tambah Invoice</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="form-create-invoice">
    @csrf

    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-md-6">
                <label>Nomor Invoice</label>
                <input type="text" name="nomor_invoice" class="form-control"
                       value="{{ $lastInvoice->nomor_invoice ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Customer Invoice</label>
                <input type="text" name="customer_invoice" class="form-control"
                       value="{{ $lastInvoice->customer_invoice ?? ($interaksi->customer->customer_nama ?? '') }}">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <label>Pesanan Masuk</label>
                <input type="date" name="pesanan_masuk" class="form-control">
            </div>
            <div class="col-md-6">
                <label>Batas Pelunasan</label>
                <select name="batas_pelunasan" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="H+1 setelah pasang">H+1 setelah pasang</option>
                    <option value="H-1 sebelum kirim">H-1 sebelum kirim</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="tablePasang">
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
                    @foreach($pasang as $p)
                        <tr>
                            <td>{{ $p->produk->produk_nama ?? '-' }} — {{ $p->jadwal_pasang_kirim }}</td>
                            <td><input type="number" name="kuantitas[]" class="form-control qty" value="{{ $p->kuantitas ?? 1 }}"></td>
                            <td><input type="text" name="harga_satuan[]" class="form-control rupiah harga" value=""></td>
                            <td><input type="text" name="total[]" class="form-control rupiah total" readonly></td>
                            <td><input type="number" name="diskon[]" class="form-control diskon" value=""></td>
                            <td><input type="text" name="grand_total[]" class="form-control rupiah grand_total" readonly></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <label>Potongan Harga</label>
                <input type="text" name="potongan_harga" class="form-control rupiah manual" value="">
            </div>
            <div class="col-md-6">
                <label>Cashback</label>
                <input type="text" name="cashback" class="form-control rupiah manual" value="">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <label>DP</label>
                <input type="text" name="dp" class="form-control rupiah manual" value="">
            </div>
            <div class="col-md-6">
                <label>Sisa Pelunasan</label>
                <input type="text" name="sisa_pelunasan" class="form-control rupiah" readonly>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <label>Total Akhir</label>
                <input type="text" name="total_akhir" class="form-control rupiah" readonly>
            </div>
            <div class="col-md-6">
                <label>Tanggal DP</label>
                <input type="date" name="tanggal_dp" class="form-control"
                       value="{{ $lastInvoice->tanggal_dp ?? '' }}">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <label>Tanggal Pelunasan</label>
                <input type="date" name="tanggal_pelunasan" class="form-control"
                       value="{{ $lastInvoice->tanggal_pelunasan ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Catatan</label>
                <textarea name="catatan" class="form-control" rows="2">{{ $lastInvoice->catatan ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
    </div>
</form>

<script>
$(function(){
    function formatRupiah(angka) {
        if(!angka) return '';
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function parseRupiah(str) {
        return parseInt(str.replace(/[^0-9]/g, '')) || 0;
    }

    // Hitung otomatis
    function hitungRow(row) {
        let qty = parseInt($(row).find('.qty').val()) || 0;
        let harga = parseRupiah($(row).find('.harga').val());
        let diskon = parseFloat($(row).find('.diskon').val()) || 0;

        let total = qty * harga;
        let grand = total - (total * (diskon / 100));

        $(row).find('.total').val(formatRupiah(total));
        $(row).find('.grand_total').val(formatRupiah(grand));

        hitungSummary();
    }

    function hitungSummary() {
        let grandTotal = 0;
        $('#tablePasang tbody tr').each(function(){
            grandTotal += parseRupiah($(this).find('.grand_total').val());
        });

        let potongan = parseRupiah($('input[name="potongan_harga"]').val());
        let cashback = parseRupiah($('input[name="cashback"]').val());
        let dp = parseRupiah($('input[name="dp"]').val());

        let totalAkhir = grandTotal - potongan - cashback;
        let sisa = totalAkhir - dp;

        $('input[name="total_akhir"]').val(formatRupiah(totalAkhir));
        $('input[name="sisa_pelunasan"]').val(formatRupiah(sisa));
    }

    $(document).on('input', '.rupiah', function () {
        let value = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(value ? formatRupiah(value) : '');
        hitungSummary();
    });

    $(document).on('input', '.qty, .harga, .diskon', function () {
        let row = $(this).closest('tr');
        hitungRow(row);
    });

    $(document).on('input', '.manual', function(){
        hitungSummary();
    });
});
</script>
