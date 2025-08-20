{{-- ========== KEBUTUHAN HARIAN ========== --}}
<div class="bg-warning text-dark px-3 py-2 mb-2 rounded">
    <strong>Kebutuhan Harian</strong>
</div>

{{-- Pastikan $interaksi tidak null --}}
@if(isset($interaksi))
    <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">
@else
    <div class="alert alert-danger">
        Data interaksi tidak ditemukan.
    </div>
@endif

<div id="kebutuhan-container">
    <div class="row mb-2 kebutuhan-row">
        <div class="col-md-2 mb-1">
            <button type="button" class="btn btn-success btn-sm w-100 btn-add-row">Tambah</button>
        </div>
    </div>
</div>

<style>
    .thead-purple {
        background-color: #6f42c1; /* Warna ungu bootstrap */
        color: white;
    }
</style>

{{-- LIST DATA KEBUTUHAN --}}
<table class="table table-bordered table-striped table-hover table-sm mt-3">
    <thead class="thead-purple">
        <tr>
            <th>Produk</th>
            <th>Media</th>
            <th>Tahapan</th>
            <th>PIC</th>
            <th>Status</th>
            <th>Detail</th>
        </tr>
    </thead>
    <tbody id="list-realtime">
        {{-- Akan diload via AJAX loadRealtimeList() --}}
    </tbody>
</table>
