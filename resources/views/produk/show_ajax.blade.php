<div id="modal-user" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Detail produk</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <table class="table table-bordered table-striped table-hover table-sm"> 
                <tr> 
                    <th>ID produk</th> 
                    <td>{{ $produk->produk_id }}</td> 
                </tr> 
                <tr> 
                    <th>Kategori</th> 
                    <td>{{ $produk->kategori->kategori_nama }}</td> 
                </tr> 
                <tr> 
                    <th>Kode produk</th> 
                    <td>{{ $produk->produk_kode }}</td> 
                </tr> 
                <tr> 
                    <th>Nama produk</th> 
                    <td>{{ $produk->produk_nama }}</td> 
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
                        // Populate modal fields with the retrieved data
                        $('#produk_id').text(response.data.produk_id);
                        $('#produk_kode').text(response.data.produk_kode);
                        $('#produk_nama').text(response.data.produk_nama);
                        $('#kategori_nama').text(response.data.kategori_nama);
                        $('#image').html('<img src="/images/produk/' + response.data.image);
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
