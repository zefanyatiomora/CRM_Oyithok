@extends('layouts.template')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <!-- PROFIL KIRI -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="modal-header bg-wallpaper-gradient text-white rounded-top position-relative border-bottom-0" style="padding: 1rem 1.5rem;">
                    <h5 class="modal-title mb-0">Profil Anda</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ auth()->user()->image_url }}"
                         id="profile-image-preview"
                         class="rounded-circle img-fluid mb-3 shadow-sm"
                         style="width: 130px; height: 130px; object-fit: cover;"
                         alt="Foto Profil">

                    <h4 class="fw-bold mb-1">{{ auth()->user()->nama }}</h4>
                    <p class="text-muted mb-3">{{ auth()->user()->level->level_nama }}</p>

                    <form action="{{ url('/profil/update_image') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="file" name="image" id="image" class="form-control form-control-sm" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-sm rounded-pill">
                            <i class="fas fa-sync-alt me-1"></i> Ganti Foto Profil
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- DATA DIRI DAN PASSWORD -->
        <div class="col-md-9">
            <div class="card shadow-sm border-0">
                <div class="modal-header bg-wallpaper-gradient text-white rounded-top position-relative border-bottom-0" style="padding: 1rem 1.5rem;">
                    <h5 class="modal-title mb-0">Pengaturan Profil</h5>
                </div>

                <div class="card-header bg-light border-0 p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item">
                            <a class="nav-link active" href="#editdatadiri" data-toggle="tab">
                                <i class="fas fa-user-edit me-1"></i> Edit Data Diri
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#editpw" data-toggle="tab">
                                <i class="fas fa-lock me-1"></i> Edit Password
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content">

                        <!-- TAB EDIT DATA DIRI -->
                        <div class="active tab-pane" id="editdatadiri">
                            <form action="{{ url('/profil/update_data_diri') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Username</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control bg-light" value="{{ $profil->username }}" disabled>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="nama" id="nama" class="form-control" value="{{ $profil->nama }}" required>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="ttd" class="col-sm-2 col-form-label">Tanda Tangan</label>
                                    <div class="col-sm-10">
                                        @if(Auth::user()->ttd)
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $profil->ttd) }}"
                                                     id="ttd-image-preview"
                                                     class="border rounded shadow-sm"
                                                     alt="TTD" width="200">
                                            </div>
                                        @endif
                                        <input type="file" name="ttd" id="ttd" class="form-control form-control-sm" accept="image/*">
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="nohp" class="col-sm-2 col-form-label">No. HP</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="nohp" id="nohp" class="form-control" value="{{ $profil->nohp ?? '' }}">
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                                    <div class="col-sm-10">
                                        <textarea name="alamat" id="alamat" class="form-control" rows="3">{{ $profil->alamat ?? '' }}</textarea>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary rounded-pill">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- TAB EDIT PASSWORD -->
                        <div class="tab-pane" id="editpw">
                            <form action="{{ url('/profil/update_password') }}" method="POST" class="form-horizontal">
                                @csrf
                                <div class="mb-3 row">
                                    <label for="oldPassword" class="col-sm-3 col-form-label">Password Lama</label>
                                    <div class="col-sm-9">
                                        <input type="password" name="old_password" class="form-control" placeholder="Masukkan password lama" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="newPassword" class="col-sm-3 col-form-label">Password Baru</label>
                                    <div class="col-sm-9">
                                        <input type="password" name="new_password" class="form-control" placeholder="Masukkan password baru" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="confirmPassword" class="col-sm-3 col-form-label">Ulangi Password</label>
                                    <div class="col-sm-9">
                                        <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password baru" required>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary rounded-pill">
                                        <i class="fas fa-key me-1"></i> Update Password
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('css')
<style>
    .bg-wallpaper-gradient {
        background: linear-gradient(135deg, #a66dd4, #7a4fcf);
    }

    .card {
        border-radius: 0.75rem;
    }

    .form-control:focus {
        border-color: #a66dd4;
        box-shadow: 0 0 0 0.2rem rgba(166, 109, 212, 0.25);
    }

    .btn-primary {
        background-color: #a66dd4;
        border-color: #a66dd4;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #9559c3;
        border-color: #9559c3;
    }

    .nav-pills .nav-link.active {
        background-color: #a66dd4;
        border-radius: 50px;
    }

    .nav-pills .nav-link:not(.active):hover {
        color: #a66dd4;
    }
</style>
@endpush

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
