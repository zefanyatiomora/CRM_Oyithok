{{-- ========== KEBUTUHAN HARIAN ========== --}}
<div class="bg-warning text-dark px-3 py-2 mb-2 rounded">
    <strong>Kebutuhan Harian</strong>
</div>

@if(isset($customer))
    <input type="hidden" name="customer_id" value="{{ $customer->customer_id }}">
@else
    <div class="alert alert-danger">
        Data customer tidak ditemukan.
    </div>
@endif

<div id="kebutuhan-container">
    <div class="row mb-2 kebutuhan-row">
        <div class="col-md-2 mb-1">
            <a href="{{ route('tambahkebutuhan.create', $customer->customer_id) }}" class="btn btn-success btn-sm w-100">Tambah</a>
        </div>
    </div>
</div>

<style>
    .thead-purple {
        background-color: #6f42c1;
        color: white;
    }
</style>

<table class="table table-bordered table-striped table-hover table-sm mt-3">
    <thead class="thead-purple">
        <tr>
            <th>Produk</th>
            <th>Tahapan</th>
            <th>PIC</th>
            <th>Status</th>
            <th>Detail</th>
        </tr>
    </thead>
    <tbody>
        @foreach($interaksis as $interaksi)
            <tr>
                <td>{{ $interaksi->produk_nama }}</td>
                <td>{{ $interaksi->tahapan ?? '-' }}</td>
                <td>{{ $interaksi->pic ?? '-' }}</td>
                <td>{{ $interaksi->status ?? '-' }}</td>
                <td>
                    @if($interaksi->identifikasi_kebutuhan)
                        {{ $interaksi->identifikasi_kebutuhan }}<br>
                        <a href="{{ route('tambahkebutuhan.edit', $interaksi->interaksi_id) }}" class="btn btn-sm btn-warning mt-1">Edit</a>
                    @else
                        <a href="{{ route('tambahkebutuhan.edit', $interaksi->interaksi_id) }}" class="btn btn-sm btn-primary">Isi</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
