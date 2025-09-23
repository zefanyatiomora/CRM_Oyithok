<form action="{{ url('/produk/ajax') }}" method="POST" id="form-tambah-produk">
    @csrf 
    <div id="modal-master" class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content"> 
            <div class="card-header bg-wallpaper-gradient d-flex justify-content-between align-items-center" 
                 style="border-radius: 0.5rem 0.5rem 0 0; padding: 0.75rem 1.25rem;">
                <h5 class="card-title mb-0 fw-bold text-white">
                    <i class="fas fa-plus-circle me-2"></i> Tambah Data Produk
                </h5>
    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close"
            style="top: 10px; right: 18px; color: #fff; font-size: 1.4rem;">
        <span aria-hidden="true">&times;</span>
    </button>
            </div>
            <div class="modal-body"> 
                <div class="form-group"> 
                    <label>Kategori</label> 
                    <select name="kategori_id" id="kategori_id" class="form-control" required> 
                        <option value="">- Pilih Kategori -</option> 
                        @foreach($kategori as $k) 
                            <option value="{{ $k->kategori_id }}">{{ $k->kategori_nama }}</option> 
                        @endforeach 
                    </select> 
                    <small id="error-kategori_id" class="error-text form-text text-danger"></small> 
                </div> 
                <div class="form-group"> 
                    <label>Nama produk</label> 
                    <input value="" type="text" name="produk_nama" id="produk_nama" class="form-control" required> 
                    <small id="error-produk_nama" class="error-text form-text text-danger"></small> 
                </div> 
                <div class="form-group"> 
    <label>Satuan</label> 
    <input type="text" name="satuan" id="satuan" class="form-control" placeholder="contoh: pcs, kg, liter" required> 
    <small id="error-satuan" class="error-text form-text text-danger"></small> 
</div>
            </div> 
            <div class="modal-footer"> 
                <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button> 
                <button type="submit" class="btn btn-primary">Simpan</button> 
            </div> 
        </div> 
    </div> 
</form> 

<script> 
    $(document).ready(function() { 
        $("#form-tambah-produk").validate({ 
            rules: {  
                kategori_id: {required: true}, 
                produk_nama: {required: true, minlength: 3, maxlength: 100},
                satuan: {required: true, minlength: 1, maxlength: 50}, 
            }, 
            submitHandler: function(form) { 
                $.ajax({ 
                    url: form.action, 
                    type: form.method, 
                    data: $(form).serialize(), 
                    success: function(response) { 
                        if(response.status){ 
                            $('#modal-master').modal('hide');
                            Swal.fire({ 
                                icon: 'success', 
                                title: 'Berhasil', 
                                text: response.message 
                            }); 
                            dataProduk.ajax.reload(); 
                        }else{ 
                            $('.error-text').text(''); 
                            $.each(response.msgField, function(prefix, val) { 
                                $('#error-'+prefix).text(val[0]); 
                            }); 
                            Swal.fire({ 
                                icon: 'error', 
                                title: 'Terjadi Kesalahan', 
                                text: response.message 
                            }); 
                        } 
                    }             
                }); 
                return false; 
            }, 
            errorElement: 'span', 
            errorPlacement: function (error, element) { 
                error.addClass('invalid-feedback'); 
                element.closest('.form-group').append(error); 
            }, 
            highlight: function (element, errorClass, validClass) { 
                $(element).addClass('is-invalid'); 
            }, 
            unhighlight: function (element, errorClass, validClass) { 
                $(element).removeClass('is-invalid'); 
            } 
        }); 
    }); 
</script>
