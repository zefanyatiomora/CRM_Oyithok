@empty($user)
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Gagal Memuat Data!</h5>
                    Data pengguna yang Anda cari tidak ditemukan.
                </div>
                <a href="{{ url('/user') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    {{-- MODAL EDIT DENGAN TATA LETAK BARU --}}
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ url('/user/' . $user->user_id . '/update_ajax') }}" method="POST" id="form-edit" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Pengguna</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        {{-- KOLOM KIRI: FOTO & DATA UTAMA --}}
                        <div class="col-md-5">
                            
                            {{-- Bagian yang di-tengah-kan --}}
                            <div class="text-center">
                                <label>Foto Profil</label>
                                <div class="mb-3">
                                    <img src="{{ $user->image_url }}"alt="Foto Profil" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #eee;">
                                </div>
                            </div>

                            {{-- Bagian yang tetap rata kiri --}}
                            <div class="form-group">
                                <label>Username</label>
                                <input value="{{ $user->username }}" type="text" name="username" id="username" class="form-control" required>
                                <small id="error-username" class="error-text form-text text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label>Nama</label>
                                <input value="{{ $user->nama }}" type="text" name="nama" id="nama" class="form-control" required>
                                <small id="error-nama" class="error-text form-text text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label>Level Pengguna</label>
                                <select name="level_id" id="level_id" class="form-control" required>
                                    <option value="">- Pilih Level -</option>
                                    @foreach($level as $l)
                                        <option {{ ($l->level_id == $user->level_id) ? 'selected' : '' }} value="{{ $l->level_id }}">
                                            {{ $l->level_nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <small id="error-level_id" class="error-text form-text text-danger"></small>
                            </div>
                        </div>
                        {{-- KOLOM KANAN: KONTAK & DETAIL LAINNYA --}}
                        <div class="col-md-7">
                            <div class="form-group">
                                <label>No HP</label>
                                <input value="{{ $user->nohp }}" type="text" name="nohp" id="nohp" class="form-control">
                                <small id="error-nohp" class="error-text form-text text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="alamat" id="alamat" class="form-control" rows="2">{{ $user->alamat }}</textarea>
                                <small id="error-alamat" class="error-text form-text text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="ttd">Tanda Tangan</label>
                                @if($user->ttd)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $user->ttd) }}" alt="TTD Lama" style="max-width: 150px; border: 1px solid #ddd; padding: 5px;">
                                </div>
                                @endif
                                <input type="file" name="ttd" id="ttd" class="form-control-file" accept="image/*">
                                <small class="form-text text-muted">Unggah TTD baru (jika ingin mengganti).</small>
                                <small id="error-ttd" class="error-text form-text text-danger"></small>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <input value="" type="password" name="password" id="password" class="form-control">
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="" id="show_password" aria-controls="password">
                                    <label class="form-check-label" for="show_password">
                                        Tampilkan password
                                    </label>
                                </div>
                                <small id="error-password" class="error-text form-text text-danger"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Script validasi Anda tidak perlu diubah, hanya pastikan form-edit sudah benar.
            // Pastikan Anda sudah menambahkan enctype="multipart/form-data" pada form
            // dan menangani upload file (TTD) di controller Anda.

            $("#form-edit").validate({
                rules: {
                    level_id: { required: true },
                    username: { required: true, minlength: 3, maxlength: 20 },
                    nama: { required: true, minlength: 3, maxlength: 100 },
                    password: { minlength: 6, maxlength: 20 }
                    // Tambahkan validasi untuk TTD jika perlu, misal ukuran file atau tipe.
                    // ttd: { accept: "image/*" }
                },
                submitHandler: function(form) {
                    var formData = new FormData(form); // Menggunakan FormData untuk upload file

                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: formData,
                        processData: false, // Penting untuk FormData
                        contentType: false, // Penting untuk FormData
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                dataUser.ajax.reload();
                            } else {
                                $('.error-text').text('');
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Tidak dapat terhubung ke server.'
                            });
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    // Menyesuaikan penempatan error untuk input file
                    if (element.prop("type") === "file") {
                        error.insertAfter(element.next('.form-text'));
                    } else {
                        element.closest('.form-group').append(error);
                    }
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
            // toggle show/hide password
            $('#show_password').on('change', function() {
                const pwd = $('#password');
                if (this.checked) {
                    pwd.attr('type', 'text');
                } else {
                    pwd.attr('type', 'password');
                }
            });
        });
    </script>
@endempty