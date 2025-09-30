<div id="modal-master" class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
        {{-- Header Profesional --}}
        <div class="modal-header bg-wallpaper-gradient position-relative border-bottom-0" style="padding: 1rem 1.5rem;">
            <h5 class="modal-title fw-bold text-white">
                <i class="fas fa-user-check me-2"></i> Detail Kebutuhan Customer
                : {{ $interaksi->customer->customer_nama ?? '-' }}
            </h5>
            <button type="button" class="close position-absolute" style="top: 10px; right: 15px;" data-dismiss="modal">
                <span>&times;</span>
            </button>
        </div>
        <div class="modal-body pt-4">
            {{-- Progress Steps --}}
            <div class="d-flex align-items-center justify-content-center mb-4">
                @foreach ($steps as $index => $step)
                    <div class="text-center" style="min-width: 80px;">
                        <div class="rounded-circle 
                    @if (in_array($index, $skippedSteps)) bg-danger text-white
                    @elseif($index <= $currentStep)
                        bg-success text-white
                    @else
                        bg-light text-dark @endif
                    d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; margin: auto;">
                            @if (in_array($index, $skippedSteps))
                                <i class="fas fa-times"></i>
                            @elseif($index <= $currentStep)
                                <i class="fas fa-check"></i>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <small>{{ $step }}</small>
                    </div>

                    {{-- Garis penghubung antar step --}}
                    @if ($index < count($steps) - 1)
                        <div class="flex-grow-1 border-top mx-2" style="height: 2px;"></div>
                    @endif
                @endforeach
            </div>
            <style>
                .card-purple {
                    background-color: #ffffff;
                    /* ungu utama */
                    color: rgb(0, 0, 0);
                    /* teks putih agar terbaca */
                }

                .card-purple .card-tools .btn-tool {
                    color: white;
                    /* tombol collapse putih */
                }

                .bg-wallpaper-gradient {
                    background: linear-gradient(135deg, #8147be, #c97aeb, #a661c2);
                    border-radius: 15px 15px 0 0;
                    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
                    color: #fff;
                }

                .modal-content .card-title i {
                    margin-right: 8px;
                }

                .bg-gradient-purple {
                    background: linear-gradient(135deg, #6a4fbf, #9b7de0);
                    color: #fff;
                    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
                }

                .card-header .card-title i {
                    font-size: 1.1rem;
                }

                .btn-tool i {
                    transition: transform 0.3s;
                }

                .collapsed-card .btn-tool i {
                    transform: rotate(0deg);
                }

                .collapsed-card[data-card-widget="collapse"]:not(.collapsed) i {
                    transform: rotate(45deg);
                    /* icon plus jadi X saat expand */
                }
            </style>
            {{-- ========== DETAIL CUSTOMER ========== --}}
            <div class="card card-purple collapsed-card shadow-sm border-0">
                <div class="card-header bg-gradient-purple position-relative"
                    style="border-radius: 0.5rem 0.5rem 0 0; padding: 0.75rem 1.25rem;">
                    <h3 class="card-title fw-bold text-white mb-0">
                        <i class="fas fa-user me-2"></i> Detail Customer
                    </h3>
                    {{-- Tombol collapse di pojok kanan --}}
                    <div class="card-tools position-absolute" style="top: 12px; right: 15px;">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover table-sm mb-4">
                        <tr>
                            <th>Kode Customer</th>
                            <td>{{ $interaksi->customer->customer_kode ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Nama Customer</th>
                            <td>{{ $interaksi->customer->customer_nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $interaksi->customer->customer_alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>No. HP</th>
                            <td>{{ $interaksi->customer->customer_hp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Number of Transaction</th>
                            <td>{{ $interaksi->customer->total_transaction }}</td>
                        </tr>
                        <tr>
                        <tr>
                            <th>Number of Cash</th>
                            <td>Rp {{ number_format($interaksi->customer->total_cash_spent, 0, ',', '.') }}</td>
                            {{-- <td>Rp {{ number_format($invoice->total_akhir, 0, ',', '.') }}</td> --}}
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <div class="input-group">
                                    <select id="follow-up-select" class="form-control form-control-sm"
                                        data-id="{{ $interaksi->interaksi_id }}"
                                        data-customer-id="{{ $interaksi->customer_id }}">
                                        @foreach ($followUpOptions as $option)
                                            <option value="{{ $option }}"
                                                {{ $selectedFollowUp == $option ? 'selected' : '' }}>
                                                {{ $option }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-sm btn-primary" id="btn-save-status">
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <script>
                $(document).on('click', '#btn-save-status', function() {
                    let interaksiId = $('#follow-up-select').data('id');
                    let status = $('#follow-up-select').val();

                    $.ajax({
                        url: "{{ route('rekap.updateStatus', ':id') }}".replace(':id', interaksiId),
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            status: status
                        },
                        success: function(res) {
                            toastr.success('Status berhasil disimpan');
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            toastr.error('Terjadi kesalahan server');
                        }
                    });
                });
            </script>

            {{-- ========== IDENTIFIKASI AWAL ========== --}}
            <div class="card card-purple collapsed-card shadow-sm border-0">
                <div class="card-header bg-gradient-purple d-flex justify-content-between align-items-center"
                    style="border-radius: 0.5rem 0.5rem 0 0; padding: 0.75rem 1.25rem;">
                    <h3 class="card-title fw-bold text-white mb-0">
                        <i class="fas fa-search me-2"></i> Identifikasi Awal
                    </h3>
                    <div class="card-tools position-absolute" style="top: 12px; right: 15px;">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-sm btn-success" id="btn-toggle-identifikasi">
                        <i class="fas fa-plus"></i> Tambah Kategori
                    </button>

                    <!-- Container form -->
                    <div id="form-identifikasi-container" style="display:none; margin-top:10px;"></div>

                    @if ($interaksiAwalList->isEmpty())
                        <div class="alert alert-secondary mt-2">Belum ada data identifikasi awal.</div>
                    @else
                        <div id="identifikasi-tabel-container">
                            <table id="tabel-identifikasi"
                                class="table table-bordered table-striped table-hover table-sm mt-2">
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
                        </div>
                    @endif
                </div>
            </div>

            <script>
                // Handler tombol tambah kategori
                $('#btn-toggle-identifikasi').click(function() {
                    let interaksi_id = "{{ $interaksi->interaksi_id }}";
                    $.get("{{ route('rekap.createIdentifikasiAwal') }}", {
                        interaksi_id: interaksi_id
                    }, function(res) {
                        $('#form-identifikasi-container').html(res).slideDown();
                    }).fail(function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire('Error!', 'Gagal memuat form identifikasi awal', 'error');
                    });
                });
            </script>
            {{-- ========== KEBUTUHAN HARIAN ========== --}}
            <div class="card card-purple collapsed-card shadow-sm border-0">
                <div class="card-header bg-gradient-purple position-relative"
                    style="border-radius: 0.5rem 0.5rem 0 0; padding: 0.75rem 1.25rem;">
                    <h3 class="card-title fw-bold text-white mb-0">
                        <i class="fas fa-calendar-day me-2"></i> Identifikasi Harian
                    </h3>
                    {{-- Tombol collapse di pojok kanan --}}
                    <div class="card-tools position-absolute" style="top: 12px; right: 15px;">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Icon Tambah Rincian -->
                    <a href="javascript:void(0);"
                        onclick="openModal('{{ route('realtime.create', $interaksi->interaksi_id) }}')"
                        class="text-primary" title="Tambah Rincian">
                        <i class="fas fa-plus fa-xs"></i>
                    </a>
                    <!-- Tabel kebutuhan -->
                    <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">
                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>PIC</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($kebutuhanList as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->tanggal }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->user->nama ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ========== DATA SURVEY ========== --}}
            <div class="card card-purple collapsed-card shadow-sm border-0">
                <div class="card-header bg-gradient-purple position-relative"
                    style="border-radius: 0.5rem 0.5rem 0 0; padding: 0.75rem 1.25rem;">
                    <h3 class="card-title fw-bold text-white mb-0">
                        <i class="fas fa-tools me-2"></i> Survey & Rincian
                    </h3>
                    {{-- Tombol collapse di pojok kanan --}}
                    <div class="card-tools position-absolute" style="top: 12px; right: 15px;">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="mt-4 d-flex justify-content-between">
                        <span style="font-size:17px;">Survey</span>

                        {{-- Icon Tambah Survey hanya muncul kalau BELUM ada survey --}}
                        @if (!$interaksi->survey)
                            <a href="javascript:void(0);"
                                onclick="openModal('{{ route('survey.create', $interaksi->interaksi_id) }}')"
                                class="text-primary" title="Tambah Survey">
                                <i class="fas fa-plus fa-xs"></i>
                            </a>
                        @endif
                    </h4>

                    <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">

                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Alamat Survey</th>
                                <th>Waktu Survey</th>
                                <th>Status</th>
                                {{-- <th>Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @if ($interaksi->survey)
                                <tr>
                                    <td>{{ $interaksi->survey->alamat_survey }}</td>
                                    <td>{{ $interaksi->survey->jadwal_survey }}</td>
                                    <td>
                                        @if ($interaksi->survey->status == 'closing survey')
                                            <span class="badge bg-success">Closing Survey</span>
                                        @else
                                            <span class="badge bg-warning">pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @else
                                <tr>
                                    <td colspan="3">Tidak ada data survey.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <h4 class="mt-4 d-flex justify-content-between">
                        <span style="font-size:17px;">Rincian Produk</span>
                        <!-- Icon Tambah Rincian -->
                        <a href="javascript:void(0);"
                            onclick="openModal('{{ route('rincian.create', $interaksi->interaksi_id) }}')"
                            class="text-primary" title="Tambah Rincian">
                            <i class="fas fa-plus fa-xs"></i>
                        </a>
                    </h4>

                    <table class="table table-bordered table-striped table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($interaksi->rincian as $rincian)
                                <tr class="produk-row">
                                    <td>{{ $rincian->produk->kategori->kategori_nama }}
                                        {{ $rincian->produk->produk_nama }}</td>
                                    <td>{{ $rincian->kuantitas }} {{ $rincian->produk->satuan }}</td>
                                    <td>{{ $rincian->deskripsi }}</td>
                                    <td>
                                        @if ($rincian->status == 'hold')
                                            <span class="badge bg-warning text-dark">Hold</span>
                                            {{-- @elseif(in_array($rincian->status, ['closing']))
                                <span class="badge bg-success"> {{ ucfirst($rincian->status) }} </span> --}}
                                        @else
                                            <span class="badge bg-success">Closing</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Tombol Edit -->
                                        <a href="javascript:void(0);" class="btn btn-warning btn-sm"
                                            onclick="openModal('{{ url('/rincian/' . $rincian->rincian_id . '/edit') }}')">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Tidak ada rincian produk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div> {{-- card-body rincian --}}
            </div> {{-- card rincian produk --}}

            {{-- ========== DATA PASANG ========== --}}
            <div class="card card-purple collapsed-card shadow-sm border-0">
                <div class="card-header bg-gradient-purple position-relative"
                    style="border-radius: 0.5rem 0.5rem 0 0; padding: 0.75rem 1.25rem;">
                    <h3 class="card-title fw-bold text-white mb-0">
                        <i class="fas fa-truck me-2"></i> Pasang/Kirim
                    </h3>
                    {{-- Tombol collapse di pojok kanan --}}
                    <div class="card-tools position-absolute" style="top: 12px; right: 15px;">
                        <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <h4 class="mt-4 d-flex justify-content-between">
                        <span style="font-size:17px;">Jadwal Pasang</span>
                        <!-- Icon Tambah Pasang -->
                        <a href="javascript:void(0);"
                            onclick="openModal('{{ route('pasang.create', $interaksi->interaksi_id) }}')"
                            class="text-primary" title="Tambah Rincian">
                            <i class="fas fa-plus fa-xs"></i>
                        </a>
                    </h4>
                    <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">
                    <table class="table table-bordered table-striped table-hover table-sm mb-4">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Kuantitas</th>
                                <th>Deskripsi</th>
                                <th>Jadwal</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($interaksi->pasang as $pasang)
                                <tr>
                                    <td>{{ $pasang->produk->kategori->kategori_nama }}
                                        {{ $pasang->produk->produk_nama }} </td>
                                    <td>{{ $pasang->kuantitas }} {{ $pasang->produk->satuan }}</td>
                                    <td>{{ $pasang->deskripsi }}</td>
                                    <td>{{ $pasang->jadwal_pasang_kirim }}</td>
                                    <td>{{ $pasang->alamat }}</td>
                                    <td>
                                        @if ($pasang->status == 'closing all')
                                            <span class="badge bg-primary">Closing All</span>
                                        @elseif($pasang->status == 'closing produk')
                                            <span class="badge bg-success">Closing Produk </span>
                                        @else
                                            <span class="badge bg-warning">Closing Pasang</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- Tombol Edit -->
                                        <a href="javascript:void(0);" class="btn btn-warning btn-sm"
                                            onclick="openModal('{{ url('/pasang/' . $pasang->pasangkirim_id . '/edit') }}')">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Tidak ada pemasangan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </table>
                    <h4 class="mt-4 d-flex justify-content-end gap-2">
                        <a href="javascript:void(0);"
                            onclick="openModal('{{ route('invoice.create', $interaksi->interaksi_id) }}')"
                            class="btn btn-sm btn-primary" title="Buat Invoice">
                            <i class="fas fa-plus fa-sm"></i> Buat Invoice
                        </a>
                        <a href="{{ route('invoice.export_pdf', $interaksi->interaksi_id) }}"
                            class="btn btn-sm btn-danger" title="Export PDF" target="_blank">
                            <i class="fas fa-file-pdf fa-sm"></i> PDF
                        </a>
                    </h4>
                </div> {{-- end modal-body --}}
            </div> {{-- end card --}}
        </div>
    </div>


    @push('css')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <style>
            .custom-modal .modal-content {
                background-color: none;
                border: none;
                box-shadow: none;
            }

            .modal-body {
                max-height: 70vh;
                /* 70% tinggi layar */
                overflow-y: auto;
                /* supaya bisa discroll ke bawah */
            }
        </style>
    @endpush

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- Tambahkan di layout atau sebelum script -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document)
                .off('click', '#btn-save-followup') // hapus event lama
            // Tambah baris baru
            $(document).on('click', '.btn-add-row', function() {
                let newRow = `<div class="row mb-2 kebutuhan-row">
            <div class="col-md-4 mb-1">
                <input type="date" name="tanggal[]" class="form-control form-control-sm" required>
            </div>
            <div class="col-md-6 mb-1">
                <input type="text" name="keterangan[]" class="form-control form-control-sm" placeholder="Keterangan">
            </div>
            <div class="col-md-2 mb-1">
                <button type="button" class="btn btn-danger btn-sm w-100 btn-remove-row">Hapus</button>
            </div>
        </div>`;
                $('#kebutuhan-container').append(newRow);
            });

            // Hapus baris
            $(document).on('click', '.btn-remove-row', function() {
                $(this).closest('.kebutuhan-row').remove();
            });

            function modalAction(url) {
                $.get(url, function(res) {
                    $('#mainModal .modal-body').html(res); // load view dari controller
                    $('#mainModal').modal('show'); // tampilkan modal
                });
            }
            // Tambah baris produk survey
            $(document).on('click', '.add-row-survey', function() {
                let row = $(this).closest('tr.produk-row').clone();
                row.find('input, select').val('');
                $(this).closest('tbody').append(row);
                row.find('.select2').select2({
                    width: '100%'
                }); // re-init select2
            });

            // Hapus baris produk survey
            $(document).on('click', '.remove-row-survey', function() {
                let tbody = $(this).closest('tbody');
                if (tbody.find('tr.produk-row').length > 1) {
                    $(this).closest('tr.produk-row').remove();
                } else {
                    alert('Minimal 1 produk harus ada.');
                }
            });


            // Simpan kebutuhan harian
            $(document).on('submit', '#form-kebutuhan-harian', function(e) {
                e.preventDefault();

                $.post("{{ url('rekap/realtime/store') }}", $(this).serialize(), function(res) {
                    if (res.status === 'success') {
                        $('#modalTambahKebutuhan').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data berhasil ditambahkan',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadRealtimeList();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message || 'Gagal menyimpan data'
                        });
                    }
                }).fail(function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan server'
                    });
                });
            });


            // Load list realtime
            function loadRealtimeList() {
                let id = $('input[name="interaksi_id"]').val();
                $.get("{{ url('/rekap/realtime/list') }}/" + id, function(html) {
                    $('#list-realtime').html(html);
                });
            }
            $('#produk_id').select2({
                placeholder: 'Cari produk...',
                width: '100%'
            });


            // Simpan data follow-up
            $(document).on('click', '#btn-save-followup', function() {
                $.ajax({
                    url: "{{ route('rekap.updateFollowUp') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        interaksi_id: $('#follow-up-select').data('id'),
                        customer_id: $('#follow-up-select').data('customer-id'),
                        tahapan: $('#tahapan-select').val(),
                        pic: $('#pic-input').val(),
                        status: $('#follow-up-select').val()
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            tableRekap.ajax.reload(null, false);
                            $('#myModal').modal('hide');

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Follow up berhasil disimpan',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal menyimpan follow up'
                            });
                        }
                    },
                    error: function(err) {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menyimpan'
                        });
                    }
                });
            });
        </script>
    @endpush
