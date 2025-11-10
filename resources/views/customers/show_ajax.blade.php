<div id="modal-user" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
<div class="modal-header bg-wallpaper-gradient text-white">
            <h5 class="modal-title">Detail Customer</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true" class="text-white">&times;</span>
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
</div>
<style>
    /* Modal body diberi padding */
#crudModal .modal-body {
    padding: 20px 25px;
}

/* Supaya tombol kemarin & hari ini tidak menabrak input */
#crudModal .input-group .form-control {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

#crudModal .btn-outline-primary {
    border-radius: 0;
}

/* Rapikan spacing antar elemen */
#crudModal .form-group {
    margin-bottom: 18px;
}

/* Form di dalam modal diberi ruang ke bawah */
#crudModal form {
    padding-bottom: 10px;
}

/* Tinggi minimal textarea */
#crudModal textarea {
    min-height: 90px;
}

/* Modal header biar lebih rapi */
.modal-header.bg-wallpaper-gradient {
    padding: 12px 20px;
    border-bottom: none;
    border-radius: 0.5rem 0.5rem 0 0;
}
    </style>


<script>
    $(document).ready(function() {
        $('#modal-user').on('show.bs.modal', function(event) {
            let userId = $(event.relatedTarget).data('id'); // Assuming user ID is passed via data attribute

            $.ajax({
                url: '/user/show_ajax/' + userId,
                type: 'GET',
                success: function(response) {
    if (response.status) {
        // Fungsi untuk pastikan nomor hp ada "0" di depan
        function formatNoHp(nohp) {
            if (!nohp.startsWith("0")) {
                return "0" + nohp;
            }
            return nohp;
        }

        // Populate modal fields
        $('#customer_kode').text(response.data.customer_kode);
        $('#customer_nama').text(response.data.customer_nama);
        $('#customer_alamat').text(response.data.customer_alamat);
        $('#customer_nohp').text(formatNoHp(response.data.customer_nohp)); // <<< di sini
        $('#informasi_media').text(response.data.informasi_media);

        // Format angka ke Rupiah
        function formatRupiah(angka) {
            return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Isi field transaksi & cash spent
        $('#total_transaction').text(response.data.total_transaction);
        $('#total_cash_spent').text(formatRupiah(response.data.total_cash_spent));
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: response.message
        });
    };
};
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
