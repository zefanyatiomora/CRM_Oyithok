<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header bg-success">
            <h5 class="modal-title">Konfirmasi Broadcast Pesan Massal</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <p>
                Apakah Anda yakin ingin mengirim pesan otomatis ke semua 
                <strong>Customer ASK</strong> sesuai kategori?
            </p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
            <button id="btnSendBroadcast" class="btn btn-success btn-sm">
                <i class="fas fa-paper-plane"></i> Kirim
            </button>
        </div>
    </div>
</div>

<script>
    $('#btnSendBroadcast').click(function() {
        $.ajax({
            url: "{{ route('ask.sendBroadcast') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                alert(res.message);
                $('#myModal').modal('hide');
            },
            error: function() {
                alert('Gagal mengirim broadcast');
            }
        });
    });
</script>
