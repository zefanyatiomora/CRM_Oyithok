@extends('layouts.template')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-wallpaper-gradient d-flex align-items-center">
                <h3 class="card-title mb-0">
                    <i class="fas fa-file-invoice mr-2"></i> Data Keterangan Invoice
                </h3>
            </div>

            <div class="card-body">
                <table id="tableKeterangan" class="table table-bordered table-striped">
                    <thead class="text-center">
                        <tr>
                            <th>No</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($keterangans as $key => $ket)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($ket->keterangan, 100) }}</td>
                                <td class="text-center">
                                    <button
                                        onclick="modalAction('{{ url('/keterangan-invoice/' . $ket->keterangan_id . '/edit_ajax') }}')"
                                        class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit fa-sm"></i> Edit
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada data keterangan invoice.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal untuk form edit (kosong, diisi lewat AJAX) -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <!-- konten form edit dimuat dari AJAX -->
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .bg-wallpaper-gradient {
            background: linear-gradient(135deg, #8147be, #c97aeb, #a661c2);
            border-radius: 15px 15px 0 0;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
            color: #fff;
        }
    </style>
@endpush

@push('js')
    <script>
        $(function() {
            $('#tableKeterangan').DataTable({
                responsive: true,
                autoWidth: false,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json"
                }
            });
        });

        function modalAction(url) {
            $.get(url, function(res) {
                $('#editModal .modal-content').html(res);
                $('#editModal').modal('show');
            }).fail(function() {
                toastr.error('Gagal memuat form edit');
            });
        }
    </script>
@endpush
