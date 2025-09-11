<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header bg-success">
            <h5 class="modal-title">Konfirmasi Broadcast Closing</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <p>
                Apakah Anda yakin ingin mengirim pesan broadcast ke semua 
                <strong>Customer dengan status Closing</strong>?
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
            <button id="btnSendClosingBroadcast" class="btn btn-success btn-sm">
                <i class="fas fa-paper-plane"></i> Kirim
            </button>
        </div>
    </div>
</div>

<script>
    $('#btnSendClosingBroadcast').click(function() {
        $.ajax({
            url: "{{ route('broadcast.sendClosing') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                alert(res.message);
                $('#myModal').modal('hide');
            },
            error: function() {
                alert('Gagal mengirim broadcast closing');
            }
        });
    });
</script>
