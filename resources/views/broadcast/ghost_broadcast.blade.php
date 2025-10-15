<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header bg-dark text-white">
            <h5 class="modal-title">
                <i class="fas fa-paper-plane me-2"></i> Konfirmasi Broadcast Pesan GHOST
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                <span>&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <p>
                Pesan berikut akan dikirim otomatis ke semua <strong>Customer GHOST</strong>:
            </p>

            <div class="border rounded bg-light p-3 mb-3">
                <p class="mb-1">Halo kak👋, gimana kabarnya hari ini? Semoga sehat selalu ya 🙏</p>
                <p class="mb-1">Beberapa waktu lalu kakak sempat hubungi kami.</p>
                <p class="mb-1">Kalau sekarang lagi belum butuh, nggak apa-apa kak 😊 Tapi kalau masih ada rencana, kami siap bantu kasih katalog & rekomendasi sesuai kebutuhan kakak.</p>
            </div>

            <p class="text-muted small">
                Pastikan Anda yakin sebelum mengirim pesan ini ke semua customer dengan status GHOST.
            </p>
        </div>

        <div class="modal-footer">
            <button class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
            <button id="btnSendBroadcastGhost" class="btn btn-dark btn-sm">
                <i class="fas fa-paper-plane"></i> Kirim Pesan
            </button>
        </div>
    </div>
</div>

<script>
    $('#btnSendBroadcastGhost').click(function() {
        $.ajax({
            url: "{{ route('ghost.sendBroadcast') }}",
            type: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                alert(res.message);
                $('#myModal').modal('hide');
            },
            error: function() {
                alert('Gagal mengirim broadcast ke customer GHOST');
            }
        });
    });
</script>
