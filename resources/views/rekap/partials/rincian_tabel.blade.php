@forelse($rincianList as $rincian)
    <tr>
        <td>{{ $rincian->produk->kategori->kategori_nama ?? '-' }} {{ $rincian->produk->produk_nama ?? '-' }}</td>
        <td>{{ $rincian->kuantitas }} {{ $rincian->produk->satuan ?? '' }}</td>
        <td>{{ $rincian->deskripsi ?? '-' }}</td>
        <td>
            @if ($rincian->status == 'hold')
                <span class="badge bg-warning text-dark">Hold</span>
            @else
                <span class="badge bg-success">Closing</span>
            @endif
        </td>
        <td>
            <a href="javascript:void(0);" class="btn btn-warning btn-sm"
               onclick="openModal('{{ url('/rincian/' . $rincian->rincian_id . '/edit') }}')">
                <i class="fas fa-edit"></i>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-center text-muted">Tidak ada rincian produk.</td>
    </tr>
@endforelse
