@extends('layouts.template')
@section('content')
<div class="container-fluid">
    <form id="formKebutuhan" action="{{ route('kebutuhan.store') }}" method="POST">
        @csrf
        <div class="card card-info mt-3">
    <div class="card-header bg-purple text-white">
        <h3 class="card-title">Data Customer</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label for="tanggal_chat">Tanggal Interaksi</label>
            <div class="input-group">
                <input type="date" class="form-control" id="tanggal_chat" name="tanggal_chat"
                    value="{{ old('tanggal_chat', \Carbon\Carbon::today()->format('Y-m-d')) }}" required>
                <button type="button" class="btn btn-outline-purple" id="btn-today">Hari Ini</button>
                <button type="button" class="btn btn-outline-purple" id="btn-yesterday">Kemarin</button>
            </div>
            <small class="text-muted">Format: Tahun-Bulan-Tanggal</small>
        </div>

        <div class="row">
            <!-- Kolom Kiri -->
            <div class="col-md-6">
                <div class="form-group position-relative">
                    <label for="customer_nama">Nama Pelanggan</label>
                    <input type="text" class="form-control" id="customer_nama" name="customer_nama"
                        value="{{ old('customer_nama') }}" required autocomplete="off">
                    <input type="hidden" id="customer_id" name="customer_id">
                    <div id="customer_list" class="list-group mt-1 position-absolute w-100"></div>
                </div>

                <div class="form-group">
                    <label for="customer_kode">Kode Pelanggan</label>
                    <input type="text" class="form-control" id="customer_kode" name="customer_kode"
                        value="{{ old('customer_kode') }}" required>
                </div>

                <div class="form-group">
                    <label for="customer_nohp">No HP</label>
                    <div class="input-group">
                        <span class="input-group-text">+62</span>
                        <input type="text" class="form-control" id="customer_nohp" name="customer_nohp"
                            placeholder="81234567890"
                            value="{{ old('customer_nohp') ? ltrim(old('customer_nohp'), '+62') : '' }}" required>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="col-md-6">
                <div class="form-group">
                    <label for="customer_alamat">Alamat</label>
                    <textarea class="form-control" name="customer_alamat" id="customer_alamat" required>{{ old('customer_alamat') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="informasi_media">Informasi Media</label>
                    <select class="form-control" name="informasi_media" id="informasi_media" required>
                        <option value="">-- Pilih Media --</option>
                        <option value="google" {{ old('informasi_media') == 'google' ? 'selected' : '' }}>Google</option>
                        <option value="medsos" {{ old('informasi_media') == 'medsos' ? 'selected' : '' }}>Media Sosial</option>
                        <option value="offline" {{ old('informasi_media') == 'offline' ? 'selected' : '' }}>Offline</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>


            <div class="card-footer text-right">
                <button type="submit" class="btn btn-purple">Simpan</button>
            </div>
        </div>
    </form>
</div>
@endsection


@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    #customer_list {
        max-height: 200px;
        overflow-y: auto;
        background-color: white;
        border: 1px solid #ced4da;
        z-index: 9999;
    }
    .btn-outline-purple {
        color: #A374FF;              /* warna teks purple */
        border: 1px solid #A374FF;   /* outline purple */
        background-color: #fff;      /* background putih */
    }

    .btn-outline-purple:hover,
    .btn-outline-purple:focus,
    .btn-outline-purple:active,
    .btn-outline-purple.active {
        color: #fff;                 /* teks jadi putih */
        background-color: #A374FF;   /* background purple */
        border-color: #A374FF;       /* outline purple */
    }
    .btn-purple {
    color: #fff;
    background-color: #A374FF;
    }   
    .btn-purple:hover {
        color: #fff;
        background-color: #9364f2;
    }

</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    // Tombol "Hari Ini" dan "Kemarin"
    $('#btn-today').click(function() {
        let today = new Date().toISOString().split('T')[0];
        $('#tanggal_chat').val(today);
    });

    $('#btn-yesterday').click(function() {
        let d = new Date();
        d.setDate(d.getDate() - 1);
        let yesterday = d.toISOString().split('T')[0];
        $('#tanggal_chat').val(yesterday);
    });
    // Select2
    $('#produk_id').select2({
        placeholder: 'Pilih produk yang dibutuhkan',
        allowClear: true
    });

    // Autosuggest nama pelanggan
    $('#customer_nama').on('input', function () {
        let keyword = $(this).val();
        if (keyword.length >= 2) {
            $.get('{{ route("kebutuhan.searchCustomer") }}', { keyword: keyword }, function (data) {
                let list = '';
                if (data.length > 0) {
                    data.forEach(customer => {
                        list += `<a href="#" class="list-group-item list-group-item-action" 
                                        data-id="${customer.customer_id}" 
                                        data-nama="${customer.customer_nama}"
                                        data-kode="${customer.customer_kode}"
                                        data-nohp="${customer.customer_nohp}" 
                                        data-alamat="${customer.customer_alamat}" 
                                        data-media="${customer.informasi_media}">
                                        ${customer.customer_nama} - ${customer.customer_nohp}
                                    </a>`;
                    });
                } else {
                    $('#customer_id').val(''); // reset kalau tidak ada
                    list = `<a href="#" class="list-group-item list-group-item-action disabled">Tidak ditemukan</a>`;
                }
                $('#customer_list').html(list).show();
            });
        } else {
            $('#customer_list').hide();
        }
    });

    // Pilih dari hasil pencarian
    $('#customer_list').on('click', '.list-group-item', function (e) {
        e.preventDefault();
        if ($(this).hasClass('disabled')) return;

        $('#customer_id').val($(this).data('id'));
        $('#customer_nama').val($(this).data('nama'));
        $('#customer_kode').val($(this).data('kode'));

        // FIX: Ensure the data is a string before calling .replace()
        let nohp = $(this).data('nohp');
        if (nohp !== null && nohp !== undefined) {
            nohp = nohp.toString().replace(/\D/g, '');
            if (nohp.startsWith('0')) {
                nohp = nohp.substring(1);
            }
            $('#customer_nohp').val(nohp);
        } else {
            $('#customer_nohp').val(''); // Clear the field if data is null or undefined
        }

        $('#customer_alamat').val($(this).data('alamat'));
        $('#informasi_media').val($(this).data('media')).trigger('change');
        $('#customer_list').hide();
    });

    // Sembunyikan daftar saat klik di luar
    $(document).click(function (e) {
        if (!$(e.target).closest('#customer_nama, #customer_list').length) {
            $('#customer_list').hide();
        }
    });

    // Format input nomor hp
    $('#customer_nohp').on('input', function () {
        let val = $(this).val().replace(/\D/g, '');
        if (val.startsWith('0')) {
            val = val.substring(1);
        }
        $(this).val(val);
    });

    // Submit via AJAX
    $('#formKebutuhan').on('submit', function(e){
        e.preventDefault();
        $.ajax({
            url: this.action,
            type: this.method,
            data: $(this).serialize(),
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        title: 'Customer berhasil tersimpan',
                        text: "Lanjut isi data interaksi?",
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Tidak',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                        // Jika klik Ya → buka show_ajax
                        // window.location.href = "{{ url('rekap') }}/" + response.interaksi_id + "/show_ajax";
                        modalAction("{{ url('/rekap')}}/" + response.interaksi_id . "/show_ajax"); 
                        } else {
                            // Jika klik Tidak → balik ke index
                            window.location.href = "{{ route('rekap.index') }}";
                        }                        
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
            // Tambahkan penanganan error, misalnya validasi dari server
            let errors = xhr.responseJSON.errors;
            let errorMessage = "Terjadi kesalahan. Pastikan semua data terisi dengan benar.";
            if (errors) {
                errorMessage = Object.values(errors).join('<br>');
            }
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: errorMessage
            });
        }
        });
    });
});
</script>
@endpush