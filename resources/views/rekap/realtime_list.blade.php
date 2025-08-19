{{-- resources/views/rekap/realtime_list.blade.php --}}
@if($realtime->isEmpty())
    <div class="alert alert-info text-center">Belum ada data kebutuhan harian.</div>
@else
    <table class="table table-bordered table-striped table-hover table-sm">
        <thead class="thead-light">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($realtime as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td>
                        <button class="btn btn-danger btn-sm btn-delete-realtime" 
                                data-id="{{ $item->id }}">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<script>
    // Hapus data realtime
    $(document).off('click', '.btn-delete-realtime').on('click', '.btn-delete-realtime', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin ingin menghapus?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if(result.isConfirmed) {
                $.ajax({
                    url: "{{ url('rekap/realtime/delete') }}/" + id,
                    type: "DELETE",
                    data: {_token: "{{ csrf_token() }}"},
                    success: function(res) {
                        if(res.status === 'success') {
                            Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                            // Reload list
                            let interaksiId = $('input[name="interaksi_id"]').val();
                            $.get("{{ url('/rekap/realtime/list') }}/" + interaksiId, function(html){
                                $('#list-realtime').html(html);
                            });
                        } else {
                            Swal.fire('Gagal!', res.message || 'Data gagal dihapus.', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    });
</script>
