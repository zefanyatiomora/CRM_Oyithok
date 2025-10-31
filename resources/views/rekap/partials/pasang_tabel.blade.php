@forelse($interaksi->pasang as $pasang)
<tr>
    <td>{{ $pasang->produk->kategori->kategori_nama ?? '' }} {{ $pasang->produk->produk_nama ?? '' }}</td>
    <td>{{ $pasang->kuantitas }} {{ $pasang->produk->satuan }}</td>
    <td>{{ $pasang->deskripsi }}</td>
    <td>{{ \Carbon\Carbon::parse($pasang->jadwal_pasang_kirim)->format('d-m-Y H:i') }}</td>
    <td>{{ $pasang->alamat }}</td>
    <td>
        @if ($pasang->status == 'closing all')
            <span class="badge bg-primary">Closing All</span>
        @elseif($pasang->status == 'closing produk')
            <span class="badge bg-success">Closing Produk</span>
        @else
            <span class="badge bg-warning">Closing Pasang</span>
        @endif
    </td>
    <td>
        <a href="javascript:void(0);" class="btn btn-warning btn-sm"
           onclick="openModal('{{ url('/pasang/' . $pasang->pasangkirim_id . '/edit') }}')">
           <i class="fas fa-edit"></i>
        </a>
        {{-- Tombol Hapus --}}
        <a href="javascript:void(0);" class="btn btn-danger btn-sm"
            onclick="openModal('{{ url('/pasang/' . $pasang->pasangkirim_id . '/confirm') }}')">
            <i class="fas fa-trash"></i>
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="7" class="text-center text-muted">Tidak ada pemasangan.</td>
</tr>
@endforelse
