<div class="card card-warning">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title">Kebutuhan Harian</h3>
        @if(isset($customer))
            <a href="{{ route('tambahkebutuhan.create', $customer->customer_id) }}" 
               class="btn btn-success btn-sm"
               onclick="event.preventDefault(); modalAction('{{ route('tambahkebutuhan.create', $customer->customer_id) }}')">
               Tambah
            </a>
        @endif
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm">
            <thead class="thead-purple">
                <tr>
                    <th>Produk</th>
                    <th>Tahapan</th>
                    <th>PIC</th>
                    <th>Status</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td>
                <select name="produk_id[]" id="produk_id" class="form-control form-control-sm" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($produkList as $produk)
                        <option value="{{ $produk->produk_id }}"
                            {{ (isset($interaksi->produk) && $interaksi->produk->produk_id == $produk->produk_id) ? 'selected' : '' }}>
                            {{ $produk->produk_nama }}
                        </option>
                    @endforeach
                </select>
                <small id="error-produk_id" class="error-text form-text text-danger"></small>
            </td>  
            {{-- @forelse($interaksis as $interaksi)
                <tr>
                    <td>{{ $interaksi->produk_nama }}</td>
                    <td>{{ $interaksi->tahapan ?? '-' }}</td>
                    <td>{{ $interaksi->pic ?? '-' }}</td>
                    <td>{{ $interaksi->status ?? '-' }}</td>
                    <td>
                        @if($interaksi->identifikasi_kebutuhan)
                            {{ $interaksi->identifikasi_kebutuhan }}<br>
                            <a href="{{ route('tambahkebutuhan.edit', $interaksi->interaksi_id) }}" 
                               class="btn btn-sm btn-warning mt-1"
                               onclick="event.preventDefault(); modalAction('{{ route('tambahkebutuhan.edit', $interaksi->interaksi_id) }}')">
                               Edit
                            </a>
                        @else
                            <a href="{{ route('tambahkebutuhan.edit', $interaksi->interaksi_id) }}" 
                               class="btn btn-sm btn-primary"
                               onclick="event.preventDefault(); modalAction('{{ route('tambahkebutuhan.edit', $interaksi->interaksi_id) }}')">
                               Isi
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Belum ada data interaksi</td>
                </tr>
            @endforelse --}}

            </tbody>
        </table>
    </div>
</div>

{{-- Modal --}}
<div id="myModal" class="modal fade" tabindex="-1" role="dialog"></div>


@push('css')
<style>
.thead-purple { background-color: #6f42c1; color: white; }
</style>
@endpush

@push('js')
<script>
function modalAction(url){
    $('#myModal').load(url,function(){
        $('#myModal').modal('show');
    });
}

// Submit form tambah kebutuhan via AJAX
$(document).on('submit','#form-tambah-kebutuhan',function(e){
    e.preventDefault();
    $.post($(this).attr('action'), $(this).serialize(), function(res){
        if(res.success){
            Swal.fire('Berhasil!', res.success, 'success');
            $('#myModal').modal('hide');
            location.reload(); // reload page agar tabel update
        } else {
            Swal.fire('Gagal!', res.error || 'Terjadi kesalahan', 'error');
        }
    }).fail(function(){
        Swal.fire('Error!','Terjadi kesalahan saat menyimpan','error');
    });
});
</script>
@endpush
