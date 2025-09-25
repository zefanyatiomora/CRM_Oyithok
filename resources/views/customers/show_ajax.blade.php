<div id="modal-user" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Detail Barang</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>Kode Customer</th> 
                    <td>{{ $customer->customer_kode }}</td> 
                </tr> 
                <tr> 
                    <th>Nama</th> 
                    <td>{{ $customer->customer_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Alamat</th> 
                    <td>{{ $customer->customer_alamat }}</td> 
                </tr> 
                <tr> 
                    <th>No HP</th> 
                    <td>{{ $customer->customer_nohp }}</td> 
                </tr> 
                <tr> 
                    <th>Informasi Media</th> 
                    <td>{{ $customer->informasi_media }}</td> 
                </tr> 
                 <tr>
    <th>Total Transaksi</th>
    <td>{{ $customer->total_transaction }}</td>
</tr>
<tr>
    <th>Total Cash Spent</th>
    <td>Rp {{ number_format($customer->total_cash_spent, 0, ',', '.') }}</td>
</tr>

            </table>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#modal-user').on('show.bs.modal', function(event) {
            let userId = $(event.relatedTarget).data('id'); // Assuming user ID is passed via data attribute

            $.ajax({
                url: '/user/show_ajax/' + userId,
                type: 'GET',
                success: function(response) {
    if (response.status) {
        // Populate modal fields
        $('#customer_kode').text(response.data.customer_kode);
        $('#customer_nama').text(response.data.customer_nama);
        $('#customer_alamat').text(response.data.customer_alamat);
        $('#customer_nohp').text(response.data.customer_nohp);
        $('#informasi_media').text(response.data.informasi_media);

        // Format angka ke Rupiah
        function formatRupiah(angka) {
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Isi field transaksi & cash spent
        $('#total_transaction').text(response.data.total_transaction);
        $('#total_cash_spent').text(formatRupiah(response.data.total_cash_spent));
                        // Populate other fields as necessary
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unable to fetch user data.'
                    });
                }
            });
        });
    });
</script>
