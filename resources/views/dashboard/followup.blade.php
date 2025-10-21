@extends('layouts.template')

@section('title', 'Daftar Customer Status Follow Up')

@section('content')
<div class="card card-outline card-warning">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title">Daftar Customer Status <span class="text-warning">Follow Up</span></h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="table-followup">
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Kode Customer</th>
                    <th>Nama Customer</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal Detail --}}
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog"
     data-backdrop="static" data-keyboard="false" aria-hidden="true"></div>

<!-- Modal Broadcast Pesan -->
<div class="modal fade" id="broadcastModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="broadcastForm">
        <div class="modal-header bg-dark text-white">
          <h5 class="modal-title">
            <i class="fas fa-paper-plane"></i> Kirim Broadcast ke 
            <span id="namaCustomer" class="font-weight-bold"></span>
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <textarea id="pesanBroadcast" name="pesan" rows="6" class="form-control"></textarea>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-dark">Kirim</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('js')
<script>
function modalAction(url = '') {
    $('#myModal').load(url, function () {
        $('#myModal').modal('show');
    });
}

let broadcastUrl = '';

function openBroadcastModal(url, nama) {
    broadcastUrl = url;
    $('#namaCustomer').text(nama);

    const defaultMessage = `Halo kak 👋, kami mau follow up terkait katalog & inspirasi desain yang sudah kami kirim kemarin.
Kalau ada desain atau produk yang kakak suka, bisa langsung balas ya 🙌. Kalau masih bingung, tim kami siap bantu kasih rekomendasi sesuai kebutuhan.
Kalau kakak mau lanjut, tinggal balas aja:
✅ Ya — untuk dibantu rekomendasi produk/desain
❌ Tidak — kalau belum butuh saat ini`;

    $('#pesanBroadcast').val(defaultMessage);
    $('#broadcastModal').modal('show');
}

$('#broadcastForm').on('submit', function (e) {
    e.preventDefault();
    const pesan = $('#pesanBroadcast').val();

    $.ajax({
        url: broadcastUrl,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            pesan: pesan
        },
        beforeSend: function () {
            Swal.fire({
                title: 'Mengirim Pesan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function (res) {
            $('#broadcastModal').modal('hide');
            Swal.fire({
                icon: res.status === 'success' ? 'success' : 'error',
                title: res.status === 'success' ? 'Berhasil' : 'Gagal',
                text: res.message
            });
        },
        error: function () {
            Swal.fire('Error', 'Terjadi kesalahan koneksi server.', 'error');
        }
    });
});

$(document).ready(function () {
    $('#table-followup').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('dashboard.followup') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
            { data: 'customer_kode', name: 'customer_kode' },
            { data: 'customer_nama', name: 'customer_nama' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center' }
        ]
    });
});
</script>
@endpush
