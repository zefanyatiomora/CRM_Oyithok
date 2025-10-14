@if ($realtimeList->isEmpty())
    <tr>
        <td colspan="4" class="text-center text-muted">Belum ada data</td>
    </tr>
@else
    @foreach($realtimeList as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ \App\Helpers\FormatHelper::tanggalIndo($item->tanggal) }}</td>
            <td>{{ $item->keterangan }}</td>
            <td>{{ $item->user->nama ?? '-' }}</td>
        </tr>
    @endforeach
@endif
