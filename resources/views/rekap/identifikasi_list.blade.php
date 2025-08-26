@if($interaksiAwalList->isEmpty())
    <div class="alert alert-secondary mt-2">Belum ada data identifikasi awal.</div>
@else
    <table class="table table-bordered table-striped table-hover table-sm mt-2">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Kategori</th>
            </tr>
        </thead>
        <tbody>
            @foreach($interaksiAwalList as $index => $awal)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $awal->kategori->kategori_nama ?? $awal->kategori_nama }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
