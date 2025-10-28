@foreach ($realtimeList as $key => $item)
<tr>
    <td>{{ $key + 1 }}</td>
    <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
    <td>{{ $item->keterangan }}</td>
    <td>{{ $item->user->nama ?? '-' }}</td>
    <td class="text-center" style="width: 80px;">
       <a href="javascript:void(0);" 
   onclick="openModal('{{ route('realtime.edit', $item->realtime_id) }}')" 
   class="text-warning me-2" title="Edit">
   <i class="fas fa-edit"></i>
</a>

<a href="javascript:void(0);" 
   onclick="deleteRealtime('{{ $item->realtime_id }}')" 
   class="text-danger" title="Hapus">
   <i class="fas fa-trash"></i>
</a>
    </td>
</tr>
@endforeach
