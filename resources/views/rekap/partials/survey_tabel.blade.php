@if ($interaksi->survey)
    <tr>
        <td>{{ $interaksi->survey->alamat_survey }}</td>
        <td>{{ \Carbon\Carbon::parse($interaksi->survey->jadwal_survey)->format('d-m-Y H:i') }}</td>
        <td>
            @if ($interaksi->survey->status == 'closing survey')
                <span class="badge bg-success">Closing Survey</span>
            @else
                <span class="badge bg-warning">Pending</span>
            @endif
        </td>
    </tr>
@else
    <tr>
        <td colspan="3" class="text-center text-muted">Tidak ada data survey.</td>
    </tr>
@endif
