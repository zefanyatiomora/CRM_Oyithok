@extends('layouts.template')

@section('title', 'Daftar Interaksi ASK')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3">Daftar Interaksi dengan Status <span class="text-primary">ASK</span></h2>

    @if($interaksi->isEmpty())
        <div class="alert alert-warning">
            Tidak ada data interaksi dengan status <strong>ASK</strong>.
        </div>
    @else
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Interaksi ID</th>
                </tr>
            </thead>
            <tbody>
                @foreach($interaksi as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->interaksi_id }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
