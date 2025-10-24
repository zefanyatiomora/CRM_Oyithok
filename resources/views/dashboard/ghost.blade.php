@extends('layouts.template')

@section('title', 'Daftar Customer Status GHOST')

@section('content')
<div class="card card-outline card-secondary">
<div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">
        Daftar Customer Status <span class="text-secondary">GHOST</span>
    </h3>
</div>
    <div class="card-body">
        <table class="table table-bordered table-striped table-hover table-sm" id="table-interaksi-ghost">
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

{{-- Modal --}}
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog"
     data-backdrop="static" data-keyboard="false" aria-hidden="true"></div>
@endsection
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
@push('css')
{{-- style tambahan kalau perlu --}}
@endpush

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

    const defaultMessage = `Halo kak ${nama}👋, gimana kabarnya hari ini? Semoga sehat selalu ya 🙏
Beberapa waktu lalu kakak sempat hubungi kami.
Kalau sekarang lagi belum butuh, nggak apa-apa kak 😊 Tapi kalau masih ada rencana, kami siap bantu kasih katalog & rekomendasi sesuai kebutuhan kakak.`;

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
    const tableGhost = $('#table-interaksi-ghost').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("dashboard.ghost") }}',
            data: function (d) {
                // Pastikan filter ini ADA dan BERUBAH saat filter di dashboard diubah
                d.bulan = $('#filter-bulan').val();
                d.tahun = $('#filter-tahun').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false},
            {data: 'customer_kode', name: 'customer_kode'},
            {data: 'customer_nama', name: 'customer_nama'},
            {data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center'}
        ]
    });

    // refresh otomatis saat filter bulan/tahun diubah
    $(document).on('change', '#filter-bulan, #filter-tahun', function () {
        console.log('Filter berubah:', $('#filter-bulan').val(), $('#filter-tahun').val());
        tableGhost.ajax.reload();
    });
});
</script>
@endpush
