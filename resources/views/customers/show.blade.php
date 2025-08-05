<div class="modal-header">
    <h5 class="modal-title">Detail Customer</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body">
    <table class="table table-bordered">
        <tr>
            <th>Kode Customer</th>
            <td>{{ $customer->customer_kode }}</td>
        </tr>
        <tr>
            <th>Nama Customer</th>
            <td>{{ $customer->customer_nama }}</td>
        </tr>
        <tr>
            <th>Alamat Customer</th>
            <td>{{ $customer->customer_alamat }}</td>
        </tr>
        <tr>
            <th>No HP Customer</th>
            <td>{{ $customer->customer_nohp }}</td>
        </tr>
        <tr>
            <th>Media Informasi</th>
            <td>{{ ucfirst($customer->informasi_media) }}</td>
        </tr>
        <tr>
            <th>Loyalty Point</th>
            <td>{{ $customer->loyalty_point }}</td>
        </tr>
    </table>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>

<script>
function modalAction(url) {
    $.ajax({
        url: '/customers/' + userId,
        type: 'GET',
        success: function (response) {
            $('#globalModal .modal-content').html(response);
            $('#globalModal').modal('show');
        },
        error: function (xhr) {
            alert('Gagal memuat data.');
        }
    });
}
</script>

