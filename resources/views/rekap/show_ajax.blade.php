<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {{-- HEADER --}}
       <div class="modal-header">
    <h5 class="modal-title fw-bold">
        Detail Kebutuhan Customer : {{ $interaksi->customer->customer_nama ?? '-' }}
    </h5>
    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
</div>

        <style>
    .card-purple {
        background-color: #ffffff; /* ungu utama */
        color: rgb(0, 0, 0); /* teks putih agar terbaca */
    }
    .card-purple .card-header {
        background-color: #5a32a3; /* ungu header lebih gelap */
        color: white;
    }
    .card-purple .card-tools .btn-tool {
        color: white; /* tombol collapse putih */
    }
    .card-purple .table th {
        background-color: #ffffff; /* header tabel ungu */
        color: rgb(0, 0, 0);
    }
</style>


        <div class="modal-body">
            {{-- Progress Steps --}}
            <div class="d-flex align-items-center justify-content-center mb-4">
                @foreach ($steps as $index => $step)
                    <div class="text-center" style="min-width: 80px;">
                        <div class="rounded-circle 
                            {{-- warna background --}}
                            @if(in_array($index, $skippedSteps))
                                bg-danger text-white
                            @elseif($index <= $currentStep)
                                bg-success text-white
                            @else
                                bg-light text-dark
                            @endif
                            d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; margin: auto;">

                            {{-- ICON / ANGKA --}}
                            @if(in_array($index, $skippedSteps))
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


            {{-- ========== DETAIL CUSTOMER ========== --}}
<div class="card card-purple collapsed-card">
    <div class="card-header">
        <h3 class="card-title">Detail Customer</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
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
                          <th>Status</th>
                          <td>
                              <select id="follow-up-select" class="form-control form-control-sm"
                                      data-id="{{ $interaksi->interaksi_id }}"
                                      data-customer-id="{{ $interaksi->customer_id }}">
                                  @foreach($followUpOptions as $option)
                                      <option value="{{ $option }}" {{ $selectedFollowUp == $option ? 'selected' : '' }}>
                                          {{ $option }}
                                      </option>
                                  @endforeach
                              </select>
                          </td>
                      </tr>
                  </table>
            </div>
              <!-- /.card-body -->
            </div>

{{-- ========== IDENTIFIKASI AWAL ========== --}}
<div class="card card-purple collapsed-card">
    <div class="card-header">
        <h3 class="card-title">Identifikasi Awal</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
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

        @if($interaksiAwalList->isEmpty())
    <div class="alert alert-secondary mt-2">Belum ada data identifikasi awal.</div>
@else
<div id="identifikasi-tabel-container">
    <table id="tabel-identifikasi" class="table table-bordered table-striped table-hover table-sm mt-2">
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
$('#btn-toggle-identifikasi').click(function () {
    let interaksi_id = "{{ $interaksi->interaksi_id }}";
    $.get("{{ route('rekap.createIdentifikasiAwal') }}", { interaksi_id: interaksi_id }, function(res){
        $('#form-identifikasi-container').html(res).slideDown();
    }).fail(function(xhr){
        console.error(xhr.responseText);
        Swal.fire('Error!', 'Gagal memuat form identifikasi awal', 'error');
    });
});
</script>
{{-- ========== KEBUTUHAN HARIAN ========== --}}
<div class="card card-purple collapsed-card">
    <div class="card-header">
        <h3 class="card-title">Identifikasi Harian</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">

        <!-- Tombol tambah data -->
        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalTambahKebutuhan">
            <i class="fas fa-plus"></i>
        </button>

        <!-- Modal Tambah Kebutuhan -->
        <div class="modal fade" id="modalTambahKebutuhan" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="form-kebutuhan-harian">
                        @csrf
                        <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">

                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Kebutuhan Harian</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="date" name="tanggal" class="form-control form-control-sm" required>
                        </div>
                            <div class="form-group">
                                <label>Keterangan</label>
                                <input type="text" name="keterangan" class="form-control form-control-sm" required>              
                            </div>
                            <div class="form-group">
                                <label>PIC</label>
                               <select name="pic_id" class="form-control form-control-sm" required>
    <option value="">-- Pilih PIC --</option>
    @foreach($picList as $pic)
        <option value="{{ $pic->pic_id }}">{{ $pic->pic_nama }}</option>
    @endforeach
</select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel kebutuhan -->
        <div id="list-realtime" class="mt-3">
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
                            <td>{{ $index+1 }}</td>
                            <td>{{ $item->tanggal }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td>{{ $item->pic->pic_nama ?? '-' }}</td>
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
</div>

<script>
$(document).ready(function() {
    function updateNomor() {
        $('#kebutuhan-container tr').each(function(index){
            $(this).find('td:first').text(index + 1);
        });
    }

    // Tambah baris hanya dari tombol header
    $('#btn-add-row-header').click(function() {
        let newRow = `<tr>
            <td></td>
            <td><input type="date" name="tanggal[]" class="form-control form-control-sm"></td>
            <td><input type="text" name="keterangan[]" class="form-control form-control-sm"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger btn-remove-row">
                    <i class="fas fa-minus"></i>
                </button>
            </td>
        </tr>`;
        $('#kebutuhan-container').append(newRow);
        updateNomor();
    });
// Simpan kebutuhan harian
$(document).on('submit', '#form-kebutuhan-harian', function(e){
    e.preventDefault();

    $.post("{{ url('rekap/realtime/store') }}", $(this).serialize(), function(res){
        if(res.status === 'success'){
            $('#modalTambahKebutuhan').modal('hide');
            Swal.fire({icon:'success', title:'Berhasil', text:'Data berhasil ditambahkan', timer:1500, showConfirmButton:false});
            loadRealtimeList();
        }else{
            Swal.fire({icon:'error', title:'Gagal', text:res.message || 'Gagal menyimpan data'});
        }
    }).fail(function(xhr){
        console.error(xhr.responseText);
        Swal.fire({icon:'error', title:'Error', text:'Terjadi kesalahan server'});
    });
});

function loadRealtimeList(){
    let id = "{{ $interaksi->interaksi_id }}";
    $.get("{{ url('/rekap/realtime/list') }}/"+id, function(html){
        $('#list-realtime').html(html);
    });
}
    // Hapus baris
    $(document).on('click', '.btn-remove-row', function() {
        $(this).closest('tr').remove();
        updateNomor();
    });

    updateNomor();
});
</script>



{{-- ========== DATA SURVEY ========== --}}
<div class="card card-purple collapsed-card">
    <div class="card-header">
        <h3 class="card-title">Data Survey</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
        <h4 class="mt-4 d-flex justify-content-between">
            <span style="font-size:17px;">Alamat Survey</span>
            <!-- Icon Tambah Rincian -->
            <a href="javascript:void(0);" 
            onclick="openModal('{{ route('survey.create', $interaksi->interaksi_id) }}')" 
            class="text-primary" 
            title="Tambah Survey">
                <i class="fas fa-plus fa-xs"></i>
            </a>
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
                    @if($interaksi->alamat)
                        <tr>
                            <td>{{ $interaksi->alamat}}</td>
                            <td>{{ $interaksi->jadwal_survey}}</td>
                            <td>{{ $interaksi->status}}</td>
                            {{-- <td>
                                <a href="javascript:void(0);" class="btn btn-warning btn-sm" onclick="openModal('{{ route() }}')">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td> --}}
                        </tr>
                    @else
                        <tr>
                            <td colspan="4">Tidak ada data survey.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <h4 class="mt-4 d-flex justify-content-between">
                <span style="font-size:17px;">Rincian Produk</span>
                <!-- Icon Tambah Rincian -->
                <a href="javascript:void(0);" 
                onclick="openModal('{{ route('rincian.create', $interaksi->interaksi_id) }}')" 
                class="text-primary" 
                title="Tambah Rincian">
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
                                <td>{{ $rincian->produk->produk_nama}}</td>
                                <td>{{ $rincian->kuantitas}} {{ $rincian->satuan}}</td>
                                <td>{{ $rincian->deskripsi}}</td>
                                <td>
                                    @if($rincian->status == 'hold')
                                        <span class="badge bg-warning text-dark">Hold</span>
                                    @elseif(in_array($rincian->status, ['closing all', 'closing produk', 'closing pasang']))
                                        <span class="badge bg-success"> {{ ucfirst($rincian->status) }} </span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($rincian->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Tombol Edit -->
                                    <a href="javascript:void(0);" class="btn btn-warning btn-sm" onclick="openModal('{{ url('/rincian/' . $rincian->rincian_id . '/edit') }}')">
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
<div class="card card-purple collapsed-card">
    <div class="card-header">
        <h3 class="card-title">Data Pasang</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
<form id="form-pasang">
    @csrf
    <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">
    
    <table class="table table-bordered table-striped table-hover table-sm mb-4">
        <tr>
            <th>Alamat Pasang</th>
            <td>
                <textarea name="alamat_pasang" class="form-control form-control-sm" rows="2">{{ $interaksi->alamat ?? '' }}</textarea>
            </td>
        </tr>
        <tr>
            <th>Waktu Pasang</th>
            <td>
                <input type="datetime-local" name="waktu_pasang" class="form-control form-control-sm"
                       value="{{ $interaksi->waktu_pasang ? date('Y-m-d\TH:i', strtotime($interaksi->waktu_pasang)) : '' }}">
            </td>
        </tr>
    </table>
</form>
        </div> {{-- end modal-body --}}

        {{-- FOOTER --}}
        <div class="modal-footer">
            <button type="button" id="btn-save-followup" class="btn btn-primary">Simpan</button>
        </div>
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
</style>
@endpush
    
@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Tambahkan di layout atau sebelum script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document)
    .off('click', '#btn-save-followup') // hapus event lama
    .on('change', '#tahapan-select', function () {
        let tahapanVal = ($(this).val() || '').trim().toLowerCase();

        if (tahapanVal === 'identifikasi') {
        $('#pic-input').val('CS');
        } else if (
            tahapanVal === 'rincian' ||
            tahapanVal === 'survey' ||
            tahapanVal === 'pasang' ||
            tahapanVal === 'order' ||
            tahapanVal === 'done'
        ) {
            $('#pic-input').val('Konsultan');
        } else {
            $('#pic-input').val('-'); // fallback
        }

    });
    // Tambah baris baru
    $(document).on('click', '.btn-add-row', function(){
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
    $(document).on('click', '.btn-remove-row', function(){
        $(this).closest('.kebutuhan-row').remove();
    });
    function modalAction(url) {
        $.get(url, function (res) {
            $('#mainModal .modal-body').html(res);   // load view dari controller
            $('#mainModal').modal('show');           // tampilkan modal
        });
    }
        // Tambah baris produk survey
    $(document).on('click', '.add-row-survey', function() {
        let row = $(this).closest('tr.produk-row').clone();
        row.find('input, select').val('');
        $(this).closest('tbody').append(row);
        row.find('.select2').select2({ width: '100%' }); // re-init select2
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


    // Submit form (sama seperti sebelumnya)
    $(document).on('submit', '#form-interaksi-realtime', function(e){
        e.preventDefault();
        $.post("{{ route('rekap.storeRealtime') }}", $(this).serialize(), function(res){
            if(res.status === 'success'){
                Swal.fire({icon:'success',title:'Berhasil!',text:'Data kebutuhan harian berhasil ditambahkan',timer:1500,showConfirmButton:false});
                $('#form-interaksi-realtime')[0].reset();
                $('#kebutuhan-container').html(` 
                    <div class="row mb-2 kebutuhan-row">
                        <div class="col-md-4 mb-1">
                            <input type="date" name="tanggal[]" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6 mb-1">
                            <input type="text" name="keterangan[]" class="form-control form-control-sm" placeholder="Keterangan">
                        </div>
                        <div class="col-md-2 mb-1">
                            <button type="button" class="btn btn-success btn-sm w-100 btn-add-row">Tambah</button>
                        </div>
                    </div>
                `);
                loadRealtimeList();
            }else Swal.fire({icon:'error',title:'Gagal!',text:res.message||'Tidak bisa menyimpan data'});
        }).fail(function(){ Swal.fire({icon:'error',title:'Error!',text:'Terjadi kesalahan saat menyimpan'}); });
    });


    // Load list realtime
    function loadRealtimeList(){
        let id = $('input[name="interaksi_id"]').val();
        $.get("{{ url('/rekap/realtime/list') }}/"+id, function(html){ $('#list-realtime').html(html); });
    }
    $('#produk_id').select2({
    placeholder: 'Cari produk...',
    width: '100%'
});


    // Simpan data follow-up
    $(document).on('click', '#btn-save-followup', function () {
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