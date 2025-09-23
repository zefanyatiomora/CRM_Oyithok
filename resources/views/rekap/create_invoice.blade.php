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
                <input type="text" name="nomor_invoice" class="form-control">
            </div>
            <div class="col-md-6">
                <label>Customer Invoice</label>
                <input type="text" name="customer_invoice" class="form-control" value="{{ $interaksi->customer->customer_nama ?? '' }}">
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

        <div class="mb-2 text-right">
            <button type="button" id="btn-add-item" class="btn btn-sm btn-success">
                <i class="fa fa-plus"></i> Tambah Item
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered" id="table-items">
                <thead>
                    <tr>
                        <th>Pasang/Kirim (pilih)</th>
                        <th>Harga Satuan</th>
                        <th>Total</th>
                        <th>Diskon</th>
                        <th>Grand Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="item-row">
                        <td>
                            <select name="pasangkirim_id[]" class="form-control select-pasang">
                                <option value="">-- Pilih Pasang/Kirim --</option>
                                @foreach($pasang as $p)
                                    <option value="{{ $p->pasangkirim_id }}"
                                        data-harga="{{ $p->harga_satuan ?? 0 }}">
                                        {{ $p->produk->produk_nama ?? '-' }} — {{ $p->jadwal_pasang_kirim }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td><input type="number" step="0.01" name="harga_satuan[]" class="form-control harga_satuan" value="0"></td>
                        <td><input type="number" step="0.01" name="total[]" class="form-control total" value="0"></td>
                        <td><input type="number" step="0.01" name="diskon[]" class="form-control diskon" value="0"></td>
                        <td><input type="number" step="0.01" name="grand_total[]" class="form-control grand_total" value="0"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger btn-remove-row"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-4">
                <label>Potongan Harga</label>
                <input type="number" step="0.01" name="potongan_harga" class="form-control" value="0">
            </div>
            <div class="col-md-4">
                <label>Cashback</label>
                <input type="number" step="0.01" name="cashback" class="form-control" value="0">
            </div>
            <div class="col-md-4">
                <label>Total Akhir</label>
                <input type="number" step="0.01" name="total_akhir" class="form-control" value="0">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <label>DP</label>
                <input type="number" step="0.01" name="dp" class="form-control" value="0">
            </div>
            <div class="col-md-6">
                <label>Tanggal DP</label>
                <input type="date" name="tanggal_dp" class="form-control">
            </div>

            <div class="col-md-6 mt-2">
                <label>Sisa Pelunasan</label>
                <input type="number" step="0.01" name="sisa_pelunasan" class="form-control" value="0">
            </div>
            <div class="col-md-6 mt-2">
                <label>Tanggal Pelunasan</label>
                <input type="date" name="tanggal_pelunasan" class="form-control">
            </div>

            <div class="col-md-12 mt-2">
                <label>Catatan</label>
                <textarea name="catatan" class="form-control" rows="3"></textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
    </div>
</form>

<script>
$(function () {
    // tambah baris item
    $('#btn-add-item').click(function () {
        let row = $('#table-items tbody tr.item-row:first').clone();
        row.find('input').val('0');
        row.find('select').val('');
        $('#table-items tbody').append(row);
    });

    // hapus baris
    $(document).on('click', '.btn-remove-row', function () {
        if ($('#table-items tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            // kosongkan jika tinggal 1 baris
            let r = $(this).closest('tr');
            r.find('input').val('0');
            r.find('select').val('');
        }
    });

    // otomatis isi harga_satuan ketika pilih pasang (jika pasang punya harga)
    $(document).on('change', '.select-pasang', function () {
        let harga = $(this).find(':selected').data('harga') || 0;
        let row = $(this).closest('tr');
        row.find('.harga_satuan').val(harga);
        // jika total dihitung dari harga * qty, kamu bisa set di sini (qty field belum disediakan)
    });

    // submit form via AJAX (akan mengembalikan JSON)
    $('#form-create-invoice').submit(function (e) {
        e.preventDefault();
        let form = this;
        let data = new FormData(form);

        $.ajax({
            url: "{{ route('invoice.store') }}",
            method: 'POST',
            data: data,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status === 'success') {
                    toastr.success(res.message);
                    // reload bagian yang perlu di UI, atau tutup modal:
                    $('#myModal').modal('hide');
                    // optional: reload halaman / datatable
                } else {
                    toastr.error(res.message || 'Simpan gagal');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                toastr.error('Terjadi kesalahan server');
            }
        });
    });
});
</script>
