{{-- ========================================================== --}}
{{-- File: resources/views/rekap/index_rincian.blade.php        --}}
{{-- ========================================================== --}}

<div class="bg-success text-white px-3 py-2 mb-2 rounded">
    <strong>Data Rincian</strong>
</div>

<form id="form-rincian">
    @csrf
    <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">

    <table class="table table-bordered table-striped table-hover table-sm" id="table-rincian">
        <thead class="table-success">
            <tr>
                <th style="width: 40%">Produk</th>
                <th style="width: 30%">Keterangan</th>
                <th style="width: 15%">Kuantitas</th>
                <th style="width: 10%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <select class="form-control select2-produk" name="produk_id[]" required></select>
                    <small class="error-text form-text text-danger"></small>
                </td>
                <td><input type="text" name="keterangan[]" class="form-control form-control-sm"></td>
                <td><input type="number" name="kuantitas[]" class="form-control form-control-sm" min="1" value="1"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-success btn-add">+</button>
                </td>
            </tr>
        </tbody>
    </table>
</form>

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important;
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
function initSelect2Produk(container) {
    $(container).find('.select2-produk').select2({
        placeholder: 'Cari produk...',
        allowClear: true,
        dropdownParent: $(container).closest('.modal').length 
                        ? $(container).closest('.modal')   // jika di modal
                        : $(document.body),                // jika bukan modal
        ajax: {
            url: '{{ route("rekap.searchProduct") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { keyword: params.term };
            },
            processResults: function (data) {
    // 'data' adalah seluruh respons: { "results": [...] }
    // Kita perlu memberitahu Select2 bahwa datanya ada di dalam 'data.results'
    return {
        results: data.results 
    };
},
            cache: true
        }
    });
}

// init pertama kali
$(document).ready(function () {
    initSelect2Produk(document);
});

// jika ada modal dengan id #myModal
$(document).on('shown.bs.modal', '#myModal', function () {
    initSelect2Produk(this);
});

// tambah row baru
$(document).on('click', '.btn-add', function () {
    let row = `
        <tr>
            <td>
                <select class="form-control select2-produk" name="produk_id[]" required></select>
                <small class="error-text form-text text-danger"></small>
            </td>
            <td><input type="text" name="keterangan[]" class="form-control form-control-sm"></td>
            <td><input type="number" name="kuantitas[]" class="form-control form-control-sm" min="1" value="1"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger btn-remove">-</button>
            </td>
        </tr>
    `;
    $('#table-rincian tbody').append(row);
    initSelect2Produk($('#table-rincian tbody tr:last'));
});

// hapus row
$(document).on('click', '.btn-remove', function () {
    $(this).closest('tr').remove();
});
</script>
@endpush
