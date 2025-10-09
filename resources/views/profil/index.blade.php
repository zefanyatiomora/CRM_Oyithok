@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                    <img src="{{ auth()->user()->image_url }}"
                    id="profile-image-preview" class="rounded-circle img-fluid mb-3" style="width: 150px; height: 150px;" alt="Image">
                    </div>

                    <h3 class="profile-username text-center">{{ auth()->user()->nama }}</h3>
                    <p class="text-muted text-center">{{ auth()->user()->level->level_nama }}</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <form action="{{ url('/profil/update_image') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                        <div class="form-group row">
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="file" name="image" id="image" class="form-control" required>
                                    <div class="input-group-append">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Ganti Foto Profil</button>
                        </form>
                        
                    </ul>

                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->

        <div class="col-md-9">
            <div class="card card-primary card-outline">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#editdatadiri" data-toggle="tab">Edit Data Diri</a></li>
                        <li class="nav-item"><a class="nav-link" href="#editpw" data-toggle="tab">Edit Password</a></li>
                    </ul>
                </div>
                <!-- /.card-header -->

                <div class="card-body">
                    <div class="tab-content">
                        <div class="active tab-pane" id="editdatadiri">
                            <form action="{{ url('/profil/update_data_diri') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Username</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{ $profil->username }}" disabled>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="nama" id="nama" class="form-control" value="{{ $profil->nama }}" required>
                                        <small id="error-nama" class="error-text form-text text-danger"></small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="ttd" class="col-sm-2 col-form-label">Tanda Tangan</label>
                                    <div class="col-sm-10">

                                        {{-- Tampilkan TTD lama kalau ada --}}
                                        @if(Auth::user()->ttd)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $profil->ttd) }}"id="ttd-image-preview" alt="TTD" width="200">
                                            </div>
                                        @endif

                                        {{-- Input untuk upload TTD baru --}}
                                        <input type="file" name="ttd" id="ttd" class="form-control" accept="image/*">
                                        <small id="error-ttd" class="error-text form-text text-danger"></small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="nohp" class="col-sm-2 col-form-label">No. HP</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="nohp" id="nohp" class="form-control" value="{{ $profil->nohp ?? '' }}">
                                    </div>
                                </div>
                        
                                <div class="form-group row">
                                    <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                                    <div class="col-sm-10">
                                        <textarea name="alamat" id="alamat" class="form-control">{{ $profil->alamat ?? '' }}</textarea>
                                    </div>
                                </div>
                        
                                <div class="form-group row">
                                    <div class="col-sm-10 offset-sm-2">
                                        <button type="submit" class="btn btn-primary">Update Profil</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- /.tab-pane -->

                        <div class="tab-pane" id="editpw">
                            <form action="{{ url('/profil/update_password') }}" method="POST" class="form-horizontal">
                                @csrf
                                <div class="form-group row">
                                    <label for="oldPassword" class="col-sm-2 col-form-label">Password Lama</label>
                                    <div class="col-sm-10">
                                        <input type="password" name="old_password" class="form-control" id="oldPassword" placeholder="Masukkan password lama" required>
                                        @if($errors->has('old_password'))
                                            <small class="text-danger">{{ $errors->first('old_password') }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="newPassword" class="col-sm-2 col-form-label">Password Baru</label>
                                    <div class="col-sm-10">
                                        <input type="password" name="new_password" class="form-control" id="newPassword" placeholder="Masukkan password baru" required>
                                        @if($errors->has('new_password'))
                                            <small class="text-danger">{{ $errors->first('new_password') }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="confirmPassword" class="col-sm-2 col-form-label">Ulangi Password Baru</label>
                                    <div class="col-sm-10">
                                        <input type="password" name="confirm_password" class="form-control" id="confirmPassword" placeholder="Ulangi password baru" required>
                                        @if($errors->has('confirm_password'))
                                            <small class="text-danger">{{ $errors->first('confirm_password') }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                        <button type="submit" class="btn btn-primary">Update Password</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</div><!-- /.container-fluid -->
@endsection
@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- PREVIEW UNTUK FOTO PROFIL ---
        const inputGambar = document.getElementById('image');
        const previewGambar = document.getElementById('profile-image-preview');

        inputGambar.addEventListener('change', function() {
            const file = this.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Update src dari elemen img dengan data URL gambar baru
                    previewGambar.src = e.target.result;
                }

                // Baca file sebagai Data URL (format base64)
                reader.readAsDataURL(file);
            }
        });


        // --- PREVIEW UNTUK TANDA TANGAN (TTD) ---
        const inputTtd = document.getElementById('ttd');
        const previewTtd = document.getElementById('ttd-image-preview');

        inputTtd.addEventListener('change', function() {
            const file = this.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Update src dari elemen img dengan data URL ttd baru
                    previewTtd.src = e.target.result;
                    // Pastikan gambar terlihat jika sebelumnya disembunyikan
                    previewTtd.style.display = 'block'; 
                }

                reader.readAsDataURL(file);
            }
        });

    });
    // Opsi konfigurasi untuk Toastr (opsional, tapi disarankan)
    toastr.options = {
        "closeButton": true,
        // "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        // "showDuration": "300",
        // "hideDuration": "1000",
        // "timeOut": "5000",
        // "extendedTimeOut": "1000",
    };

    // Skrip untuk menampilkan notifikasi Toastr berdasarkan session
    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif

    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif
</script>
@endpush

@push('css')
<style>
    /* Mendefinisikan variabel warna ungu */
    :root {
        --primary-color: #a66dd4;
        --primary-color-hover: #9559c3;
    }

    /* Mengubah warna tombol primary */
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover,
    .btn-primary:focus,
    .btn-primary:active {
        background-color: var(--primary-color-hover);
        border-color: var(--primary-color-hover);
    }

    /* Mengubah warna garis atas pada card-outline */
    .card-primary.card-outline {
        border-top-color: var(--primary-color);
    }

    /* (Opsional) Mengubah warna tab navigasi yang aktif agar serasi */
    .nav-pills .nav-link.active,
    .nav-pills .show>.nav-link {
        background-color: var(--primary-color);
    }
    /* Mengubah warna TEKS saat hover pada tab yang tidak aktif */
    .nav-pills .nav-link:not(.active):hover {
        color: var(--primary-color);
    }
</style>
@endpush
