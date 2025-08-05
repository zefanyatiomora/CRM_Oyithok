@extends('layouts.template')
@section('content')
<div class="container-fluid">
    <form id="formKebutuhan" action="{{ route('kebutuhan.store') }}" method="POST">
        @csrf
        <div class="card card-info mt-3">
            <div class="card-header">
                <h3 class="card-title">Data Customer</h3>
            </div>
            <div class="card-body row">
                <div class="col-md-6">
                    <div class="form-group position-relative">
                        <label for="customer_nama">Nama Pelanggan</label>
                        <input type="text" class="form-control" id="customer_nama" name="customer_nama" value="{{ old('customer_nama') }}" required autocomplete="off">
                        <input type="hidden" id="customer_id" name="customer_id">
                        <div id="customer_list" class="list-group mt-1 position-absolute w-100"></div>
                    </div>
                    <div class="form-group">
                    <label for="customer_kode">Kode Pelanggan</label>
                    <input type="text" class="form-control" id="customer_kode" name="customer_kode" value="{{ old('customer_kode') }}" required>
                </div>
                    <div class="form-group">
                        <label for="customer_nohp">No HP</label>
                        <input type="text" class="form-control" id="customer_nohp" name="customer_nohp" value="{{ old('customer_nohp') }}" required>
                    </div>
                </div>
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

        {{-- Data Kebutuhan --}}
        <div class="card card-primary mt-3">
            <div class="card-header">
                <h3 class="card-title">Data Kebutuhan</h3>
            </div>
            <div class="card-body row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal_chat">Tanggal Chat</label>
                        <input type="date" class="form-control" name="tanggal_chat" id="tanggal_chat" value="{{ old('tanggal_chat') }}" required>
                    </div>
                   <select name="produk_id" class="form-control" required>
    <option value="">Pilih Produk</option>
    @foreach ($produks as $produk)
        <option value="{{ $produk->produk_id }}">{{ $produk->produk_nama }}</option>
    @endforeach
</select>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="identifikasi_kebutuhan">Identifikasi Kebutuhan</label>
                        <textarea class="form-control" name="identifikasi_kebutuhan" id="identifikasi_kebutuhan" required>{{ old('identifikasi_kebutuhan') }}</textarea>
                    </div>
                <div class="form-group">
                    <label for="media">Media</label>
                    <input type="text" class="form-control" name="media" id="media" value="{{ old('media') }}">
                </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Simpan</button>
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
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function () {
    // Inisialisasi select2
    $('#produk_id').select2({
        placeholder: 'Pilih produk yang dibutuhkan',
        allowClear: true
    });

    // Auto-suggest nama pelanggan
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
                    list = `<a href="#" class="list-group-item list-group-item-action disabled">Tidak ditemukan</a>`;
                }
                $('#customer_list').html(list).show();
            });
        } else {
            $('#customer_list').hide();
        }
    });

    // Ketika dipilih dari hasil pencarian
    $('#customer_list').on('click', '.list-group-item', function (e) {
        e.preventDefault();
        $('#customer_id').val($(this).data('id'));
        $('#customer_nama').val($(this).data('nama'));
        $('#customer_kode').val($(this).data('kode'));
        $('#customer_nohp').val($(this).data('nohp'));
        $('#customer_alamat').val($(this).data('alamat'));
        $('#informasi_media').val($(this).data('media')).trigger('change');
        $('#customer_list').hide();
    });

    // Menyembunyikan daftar saat klik di luar
    $(document).click(function (e) {
        if (!$(e.target).closest('#customer_nama, #customer_list').length) {
            $('#customer_list').hide();
        }
    });

    console.log('📦 Memulai validasi form...');

$('#formKebutuhan').validate({
    submitHandler: function(form) {
        console.log('✅ Form tervalidasi, siap submit...');

        $.ajax({
            url: form.action,
            type: form.method,
            data: $(form).serialize(),
            beforeSend: function () {
                console.log('⏳ Mengirim AJAX...');
            },
            success: function(response) {
                console.log('🎉 AJAX sukses:', response);

                if (response.status) {
                    $('#myModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    });
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
            error: function(xhr, status, error) {
                console.error('❌ AJAX error:', status, error);
                console.error('🔎 Full error:', xhr.responseText);
            }
        });

        return false; // prevent default form submit
    },
    invalidHandler: function(event, validator) {
        console.warn('⚠️ Form tidak valid. Error:', validator.errorList);
    }
});


});
</script>

@endpush
