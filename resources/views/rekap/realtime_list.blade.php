<table class="table table-bordered table-striped table-hover table-sm">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>PIC</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($list as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->tanggal }}</td>
                <td>{{ $item->keterangan }}</td>
                <td>{{ $item->pic->pic_nama ?? '-' }}</td>
                <td>
                    <button class="btn btn-sm btn-danger btn-delete-realtime" data-id="{{ $item->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5">Belum ada data</td>
            </tr>
        @endforelse
    </tbody>
</table>

<script>
$(document).on('click', '.btn-delete-realtime', function(){
    let id = $(this).data('id');
    Swal.fire({
        title: 'Yakin hapus?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus'
    }).then((res)=>{
        if(res.isConfirmed){
            $.ajax({
                url: "{{ url('/rekap/realtime/delete') }}/"+id,
                type: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res){
                    if(res.status === 'success') loadRealtimeList();
                }
            })
        }
    });
});
</script>
