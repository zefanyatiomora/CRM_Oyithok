<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header bg-danger">
            <h5 class="modal-title">Konfirmasi Broadcast Hold</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <p>
                Apakah Anda yakin ingin mengirim pesan ke semua 
                <strong>Customer dengan status Hold</strong>?
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
            <button id="btnSendHoldBroadcast" class="btn btn-danger btn-sm">
                <i class="fas fa-paper-plane"></i> Kirim
            </button>
        </div>
    </div>
</div>

<script>
    $('#btnSendHoldBroadcast').click(function() {
        $.ajax({
            url: "{{ route('broadcast.sendHold') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                alert(res.message);
                $('#myModal').modal('hide');
            },
            error: function() {
                alert('Gagal mengirim broadcast Hold');
            }
        });
    });
</script>
