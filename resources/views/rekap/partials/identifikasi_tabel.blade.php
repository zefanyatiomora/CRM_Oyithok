@if ($interaksiAwalList->isEmpty())
    <div class="alert alert-secondary">Belum ada data identifikasi awal.</div>
@else
    <table id="tabel-identifikasi" class="table table-bordered table-striped table-hover table-sm">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($interaksiAwalList as $index => $awal)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $awal->kategori_nama }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
