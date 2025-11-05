@extends('layouts.template')
@section('content')
<div class="container-fluid position-relative">
    <form id="formKebutuhan" action="{{ route('kebutuhan.store') }}" method="POST">
        @csrf
        <div class="card card-outline mt-3">
            <div class="card-header bg-gradient-purple d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-user mr-2"></i> Data Customer
                </h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="tanggal_chat">Tanggal Interaksi</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="tanggal_chat" name="tanggal_chat"
                            value="{{ old('tanggal_chat', \Carbon\Carbon::today()->format('d-m-Y')) }}"
                            required placeholder="dd-mm-yyyy">
                        <button type="button" class="btn btn-outline-purple" id="btn-today">Hari Ini</button>
                        <button type="button" class="btn btn-outline-purple" id="btn-yesterday">Kemarin</button>
                    </div>
                    <small class="text-muted">Format: Tanggal-Bulan-Tahun</small>
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
                            value="{{ old('customer_kode') }}" required placeholder="Otomatis (mis. 201025-8)">
                        </div>

                        <div class="form-group">
                            <label for="customer_nohp">No HP</label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input type="text" class="form-control" id="customer_nohp" name="customer_nohp"
                                    placeholder="81234567890"
                                    value="{{ old('customer_nohp') ? ltrim(old('customer_nohp'), '+62') : '' }}"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_alamat">Alamat</label>
                            <textarea class="form-control" name="customer_alamat" id="customer_alamat">{{ old('customer_alamat') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="informasi_media">Informasi Media</label>
                            <select class="form-control" name="informasi_media" id="informasi_media">
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
    </form>

    <div id="show_ajax_container"></div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.bg-gradient-purple {
    background: linear-gradient(135deg, #8147be, #c97aeb, #a661c2) !important;
    border-radius: 15px 15px 0 0 !important;
    color: #fff !important;
    font-weight: bold;
}
.btn-outline-purple {
    color: #A374FF;
    border: 1px solid #A374FF;
    background-color: #fff;
}
.btn-outline-purple:hover {
    color: #fff;
    background-color: #A374FF;
}
.btn-purple {
    color: #fff;
    background-color: #A374FF;
}
.btn-purple:hover {
    background-color: #9364f2;
}
#customer_list {
    max-height: 200px;
    overflow-y: auto;
    background-color: white;
    border: 1px solid #ced4da;
    z-index: 9999;
}
#show_ajax_container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(3px);
    z-index: 1050;
    display: none;
    justify-content: center;
    align-items: center;
    padding: 30px;
}
#show_ajax_container > .overlay-content {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.2);
    padding: 30px;
    max-width: 900px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    animation: fadeIn 0.3s ease forwards;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@push('js')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function() {
    // === AUTO-GENERATE KODE CUSTOMER ===
function updateCustomerCodeFromDate() {
    let tanggal = $('#tanggal_chat').val();
    if (tanggal) {
        let parts = tanggal.split('-');
        if (parts.length === 3) {
            let yy = parts[2].slice(-2);
            let kode = parts[0] + parts[1] + yy;
            // Hapus baris ini 👇
            // $('#customer_kode').val(kode + '-AUTO');
        }
    }
}
   // 🔹 Ambil kode customer default saat form dibuka
    $.get("{{ route('customers.nextCode') }}", function(data) {
        if (data.kode) {
            $('#customer_kode').val(data.kode);
        }
    });
    $("#tanggal_chat").datepicker({
        dateFormat: "dd-mm-yy",
        changeMonth: true,
        changeYear: true,
        maxDate: 0,
        onSelect: updateCustomerCodeFromDate
    });

    $('#btn-today').click(function() {
        let today = $.datepicker.formatDate('dd-mm-yy', new Date());
        $('#tanggal_chat').val(today);
        updateCustomerCodeFromDate();
    });

    $('#btn-yesterday').click(function() {
        let d = new Date();
        d.setDate(d.getDate() - 1);
        let yesterday = $.datepicker.formatDate('dd-mm-yy', d);
        $('#tanggal_chat').val(yesterday);
        updateCustomerCodeFromDate();
    });

    updateCustomerCodeFromDate();

    // === AUTOCOMPLETE CUSTOMER ===
    $(document).on('input', '#customer_nama', function() {
        let keyword = $(this).val();
        let customerList = $('#customer_list');
        if (keyword.length >= 2) {
            $.get('{{ route('kebutuhan.searchCustomer') }}', { keyword }, function(data) {
                let list = data.length
                    ? data.map(c => `
                        <a href="#" class="list-group-item list-group-item-action"
                            data-id="${c.customer_id}"
                            data-nama="${c.customer_nama}"
                            data-kode="${c.customer_kode}"
                            data-nohp="${c.customer_nohp}"
                            data-alamat="${c.customer_alamat}"
                            data-media="${c.informasi_media}">
                            ${c.customer_nama} - ${c.customer_nohp}
                        </a>`).join('')
                    : `<a href="#" class="list-group-item list-group-item-action disabled">Tidak ditemukan</a>`;
                customerList.html(list).show();
            });
        } else customerList.hide();
    });

    $(document).on('click', '#customer_list .list-group-item', function(e) {
        e.preventDefault();
        if ($(this).hasClass('disabled')) return;
        // Ambil data nohp dengan aman
        let nohp = String($(this).data('nohp') || '');
        $('#customer_id').val($(this).data('id'));
        $('#customer_nama').val($(this).data('nama'));
        $('#customer_kode').val($(this).data('kode'));
        $('#customer_nohp').val(nohp.replace(/\D/g, '').replace(/^0/, ''));        
        $('#customer_alamat').val($(this).data('alamat'));
        $('#informasi_media').val($(this).data('media')).trigger('change');
        $('#customer_list').hide();
    });

    // === SUBMIT FORM ===
    $('#formKebutuhan').submit(function(e) {
        e.preventDefault();
        $.post(this.action, $(this).serialize(), function(response) {
            if (response.status) {
                if (response.customer_kode) {
        $('#customer_kode').val(response.customer_kode);
    }
                Swal.fire({
                    title: 'Customer berhasil tersimpan',
                    text: 'Lanjut isi data interaksi?',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Ya',
                    cancelButtonText: 'Tidak'
                }).then(result => {
                    if (result.isConfirmed) {
                        let showUrl = "{{ route('rekap.show_ajax', ['interaksi_id' => '__ID__']) }}"
                            .replace('__ID__', response.interaksi_id);
                        $('#formKebutuhan').css('pointer-events', 'none');
                        $('#show_ajax_container').show().html('<div class="text-center p-5">Memuat data interaksi...</div>');
                        $.get(showUrl, function(html) {
                            $('#show_ajax_container').css('display', 'flex').html(`<div class="overlay-content">${html}</div>`).hide().fadeIn(300);
                        });
                    } else window.location.href = "{{ route('rekap.index') }}";
                });
            } else {
                Swal.fire('Terjadi Kesalahan', response.message, 'error');
            }
        });
    });

    $(document).on('click', '#btnKembaliForm', function() {
        $('#show_ajax_container').fadeOut(300, function() {
            $('#formKebutuhan').css('pointer-events', 'auto');
            $('#show_ajax_container').empty();
        });
    });

    $(document).on('input', '#customer_nohp', function() {
        let val = $(this).val().replace(/\D/g, '').replace(/^0/, '');
        $(this).val(val);
    });

    $(document).click(function(e) {
        if (!$(e.target).closest('#customer_nama, #customer_list').length) $('#customer_list').hide();
    });
});
</script>
@endpush
