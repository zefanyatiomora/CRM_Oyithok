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
            @php
                $originalStep = session('originalStep', $originalStep ?? 0);
                $currentStep = session('currentStep', $currentStep ?? 0);
            @endphp
            <div class="d-flex align-items-center justify-content-center mb-4">
                @foreach ($steps as $index => $step)
                    <div class="text-center" style="min-width: 80px;">
                        <div class="rounded-circle 
                            {{ $index < $currentStep ? 'bg-success text-white' : '' }}
                            {{ $index == $currentStep ? 'bg-primary text-white' : '' }}
                            {{ $index > $currentStep ? 'bg-light text-dark' : '' }}
                            d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; margin: auto;">

                            {{-- LOGIKA ICON --}}
                            @if ($index < $originalStep)
                                {{-- Sudah dilewati sebelumnya --}}
                                <i class="fas fa-check"></i>
                            @elseif ($index > $originalStep && $index < $currentStep)
                                {{-- Tahapan dilewati (skip) --}}
                                <i class="fas fa-times"></i>
                            @elseif ($index == $currentStep)
                                {{-- Step saat ini, tampilkan angka --}}
                                {{ $index + 1 }}
                            @else
                                {{-- Step belum sampai --}}
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
@php
    // Ambil data interaksi awal berdasarkan interaksi_id
    $interaksiAwalList = \App\Models\InteraksiAwalModel::where('interaksi_id', $interaksi->interaksi_id)->get();
@endphp

@if($interaksiAwalList->isEmpty())
    <div class="alert alert-secondary">Belum ada data identifikasi awal.</div>
@else
    <table class="table table-bordered table-striped table-hover table-sm mb-4">
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
{{-- ========== KEBUTUHAN HARIAN ========== --}}
<div class="card card-purple collapsed-card">
    <div class="card-header">
        <h3 class="card-title">Identifikasi Kebutuhan</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
    <div class="card-body">
            <!-- Tombol tambah hanya di header -->
            <button type="button" class="btn btn-sm btn-success" id="btn-add-row-header">
                <i class="fas fa-plus"></i> Tambah Baris
            </button>
        <table class="table table-bordered table-striped table-hover table-sm" id="table-kebutuhan-harian">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="kebutuhan-container">
                @php
                    $kebutuhanList = \App\Models\InteraksiRealtime::where('interaksi_id', $interaksi->interaksi_id)
                                        ->orderBy('tanggal', 'asc')
                                        ->get();
                @endphp

                @if($kebutuhanList->isEmpty())
                    <tr>
                        <td>1</td>
                        <td><input type="date" name="tanggal[]" class="form-control form-control-sm"></td>
                        <td><input type="text" name="keterangan[]" class="form-control form-control-sm"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger btn-remove-row">
                                <i class="fas fa-minus"></i>
                            </button>
                        </td>
                    </tr>
                @else
                    @foreach($kebutuhanList as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><input type="date" name="tanggal[]" class="form-control form-control-sm" value="{{ $item->tanggal }}"></td>
                            <td><input type="text" name="keterangan[]" class="form-control form-control-sm" value="{{ $item->keterangan }}"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger btn-remove-row">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
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
<form id="form-survey">
    @csrf
    <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">
    
    <table class="table table-bordered table-striped table-hover table-sm mb-4">
        <tr>
            <th>Alamat Survey</th>
            <td>
                <textarea name="alamat_survey" class="form-control form-control-sm" rows="2">{{ $interaksi->alamat ?? '' }}</textarea>
            </td>
        </tr>
        <tr>
            <th>Waktu Survey</th>
            <td>
                <input type="datetime-local" name="waktu_survey" class="form-control form-control-sm"
                       value="{{ $interaksi->waktu_survey ? date('Y-m-d\TH:i', strtotime($interaksi->waktu_survey)) : '' }}">
                    </td>
                </tr>
            </table>
        </form>
            </div>
            </div>

{{-- ========== DATA RINCIAN ========== --}}
{{-- <div class="bg-success text-white px-3 py-2 mb-2 rounded">
    <strong>Data Rincian</strong>
</div>

<form id="form-rincian">
    @csrf
    <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">

    <table class="table table-bordered table-striped table-hover table-sm" id="table-rincian">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Keterangan</th>
                <th>Kuantitas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                <select name="produk_id[]" id="produk_id" class="form-control form-control-sm" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach($produkList as $produk)
                        <option value="{{ $produk->produk_id }}"
                            {{ (isset($interaksi->produk) && $interaksi->produk->produk_id == $produk->produk_id) ? 'selected' : '' }}>
                            {{ $produk->produk_nama }}
                        </option>
                    @endforeach
                </select>
                <small id="error-produk_id" class="error-text form-text text-danger"></small>
            </td>

                <td><input type="text" name="keterangan[]" class="form-control form-control-sm"></td>
                <td><input type="number" name="kuantitas[]" class="form-control form-control-sm" min="1"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-success btn-add">
                        +
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</form> --}}

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
</div>

<!-- Tambahkan di layout atau sebelum script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Event untuk tahapan -> PIC otomatis
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
// $(document).ready(function() {
//     // Tambah baris
//     $(document).on('click', '.btn-add', function() {
//         let row = $(this).closest('tr').clone();
//         row.find('input').val(''); // reset input
//         row.find('select').val('pending'); // default status
//         row.find('.btn-add')
//             .removeClass('btn-success btn-add').addClass('btn-danger btn-remove')
//             .text('-'); // ganti jadi tombol hapus
//         $('#table-rincian tbody').append(row);
//     });

//     // Hapus baris
//     $(document).on('click', '.btn-remove', function() {
//         $(this).closest('tr').remove();
//     });
// });
// $(document).ready(function() { 
//         $("#form-rincian").validate({ 
//             rules: { 
//                 produk_kode: {required: true, minlength: 3, maxlength: 20}, 
//                 kategori_id: {required: true, number: true}, 
//                 produk_nama: {required: true, minlength: 3, maxlength: 100}, 
//             }, 
//         });
// });


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