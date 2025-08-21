<form id="form-tambah-kebutuhan" action="{{ route('rekap.storeKebutuhanProduk') }}" method="POST">
    @csrf
    <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">
    <table class="table table-bordered table-striped table-hover table-sm" id="table-kebutuhan">
        <thead class="thead-purple">
            <tr>
                <th>Produk</th>
                <th>Tahapan</th>
                <th>PIC</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select name="produk_id[]" class="form-control form-control-sm" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($produkList as $produk)
                        <option value="{{ $produk->produk_id }}">{{ $produk->produk_nama }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="tahapan[]" class="form-control form-control-sm tahapan-select" required>
                        <option value="identifikasi">Identifikasi</option>
                        <option value="rincian">Rincian</option>
                        <option value="survey">Survey</option>
                        <option value="pasang">Pasang</option>
                        <option value="order">Order</option>
                        <option value="done">Done</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="pic_display[]" class="form-control form-control-sm pic-display" value="CS" readonly>
                    <input type="hidden" name="pic[]" class="pic-hidden" value="CS">
                </td>
                <td>
                    <select name="status[]" class="form-control form-control-sm" required>
                        <option value="Ask">Ask</option>
                        <option value="Follow Up">Follow Up</option>
                        <option value="Closing Survey">Closing Survey</option>
                        <option value="Closing Pasang">Closing Pasang</option>
                        <option value="Closing Product">Closing Product</option>
                        <option value="Closing ALL">Closing ALL</option>
                    </select>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm btn-remove">Hapus</button></td>
            </tr>
        </tbody>
    </table>

    <div class="text-right mb-3">
        <button type="button" class="btn btn-primary" id="addRow">+ Tambah</button>
    </div>
    <div class="text-right">
        <button type="submit" class="btn btn-success">Simpan</button>
    </div>
</form>

@push('js')
<script>
$(document).ready(function(){
    // tambah baris
    $('#addRow').click(function(){
        let row = `<tr>
            <td>
                <select name="produk_id[]" class="form-control form-control-sm" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($produkList as $produk)
                    <option value="{{ $produk->produk_id }}">{{ $produk->produk_nama }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="tahapan[]" class="form-control form-control-sm tahapan-select" required>
                    <option value="identifikasi">Identifikasi</option>
                    <option value="rincian">Rincian</option>
                    <option value="survey">Survey</option>
                    <option value="pasang">Pasang</option>
                    <option value="order">Order</option>
                    <option value="done">Done</option>
                </select>
            </td>
            <td>
                <input type="text" name="pic_display[]" class="form-control form-control-sm pic-display" value="CS" readonly>
                <input type="hidden" name="pic[]" class="pic-hidden" value="CS">
            </td>
            <td>
                <select name="status[]" class="form-control form-control-sm" required>
                    <option value="Ask">Ask</option>
                    <option value="Follow Up">Follow Up</option>
                    <option value="Closing Survey">Closing Survey</option>
                    <option value="Closing Pasang">Closing Pasang</option>
                    <option value="Closing Product">Closing Product</option>
                    <option value="Closing ALL">Closing ALL</option>
                </select>
            </td>
            <td><button type="button" class="btn btn-danger btn-sm btn-remove">Hapus</button></td>
        </tr>`;
        $('#table-kebutuhan tbody').append(row);
    });

    // auto set PIC
    $(document).on('change', '.tahapan-select', function(){
        let tahapan = $(this).val();
        let tr = $(this).closest('tr');
        let pic = (tahapan === 'identifikasi') ? 'CS' : 'Konsultan';
        tr.find('.pic-display').val(pic);
        tr.find('.pic-hidden').val(pic);
    });

    // hapus baris
    $(document).on('click','.btn-remove', function(){
        $(this).closest('tr').remove();
    });

    // submit ajax
    $(document).on('submit','#form-tambah-kebutuhan',function(e){
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: "POST",
            data: $(this).serialize(),
            success: function(res){
                if(res.success){
                    Swal.fire('Berhasil!', res.success, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Gagal!', res.error || 'Terjadi kesalahan', 'error');
                }
            },
            error: function(xhr){
                Swal.fire('Error!','Terjadi kesalahan saat menyimpan','error');
            }
        });
    });
});
</script>
@endpush
