<form action="{{ url('/produk/ajax') }}" method="POST" id="form-tambah-produk"> 
    @csrf 
    <div id="modal-master" class="modal-dialog modal-lg" role="document"> 
        <div class="modal-content"> 
            <div class="modal-header"> 
                <h5 class="modal-title" id="exampleModalLabel">Tambah Data produk</h5> 
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> 
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
                    <label>Kode produk</label> 
                    <input value="" type="text" name="produk_kode" id="produk_kode" class="form-control" required> 
                    <small id="error-produk_kode" class="error-text form-text text-danger"></small> 
                </div> 
                <div class="form-group"> 
                    <label>Nama produk</label> 
                    <input value="" type="text" name="produk_nama" id="produk_nama" class="form-control" required> 
                    <small id="error-produk_nama" class="error-text form-text text-danger"></small> 
                </div> 
                <div class="form-group"> 
                    <label>Harga Beli</label> 
                    <input value="" type="number" name="harga_beli" id="harga_beli" class="form-control" required> 
                    <small id="error-harga_beli" class="error-text form-text text-danger"></small> 
                </div> 
                <div class="form-group"> 
                    <label>Harga Jual</label> 
                    <input value="" type="number" name="harga_jual" id="harga_jual" class="form-control" required> 
                    <small id="error-harga_jual" class="error-text form-text text-danger"></small> 
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
                kategori_id: {required: true, number: true}, 
                produk_kode: {required: true, minlength: 3, maxlength: 20}, 
                produk_nama: {required: true, minlength: 3, maxlength: 100}, 
                harga_beli: {required: true, number: true}, 
                harga_jual: {required: true, number: true} 
            }, 
            submitHandler: function(form) { 
                $.ajax({ 
                    url: form.action, 
                    type: form.method, 
                    data: $(form).serialize(), 
                    success: function(response) { 
                        if(response.status){ 
                            $('#myModal').modal('hide'); 
                            Swal.fire({ 
                                icon: 'success', 
                                title: 'Berhasil', 
                                text: response.message 
                            }); 
                            dataproduk.ajax.reload(); 
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
