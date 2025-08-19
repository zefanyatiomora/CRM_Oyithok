<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {{-- HEADER --}}
        <div class="modal-header">
            <h5 class="modal-title">Detail Kebutuhan & Survey/Pasang</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>

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

    {{-- <form action="{{ url('/stok/' . $stock->stok_id . '/update_ajax') }}" method="POST" id="form-edit-stok"> --}}
{{-- ========== DETAIL CUSTOMER ========== --}}
<div class="bg-primary text-white px-3 py-2 mb-2 rounded">
    <strong>Detail Customer</strong>
</div>
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
        <th>Tahapan</th>
        <td>
            <select id="tahapan-select" class="form-control form-control-sm">
                <option value="identifikasi" {{ strtolower($interaksi->tahapan ?? '') === 'identifikasi' ? 'selected' : '' }}>identifikasi</option>
                <option value="rincian" {{ strtolower($interaksi->tahapan ?? '') === 'rincian' ? 'selected' : '' }}>rincian</option>
                <option value="survey" {{ strtolower($interaksi->tahapan ?? '') === 'survey' ? 'selected' : '' }}>survey</option>
                <option value="pasang" {{ strtolower($interaksi->tahapan ?? '') === 'pasang' ? 'selected' : '' }}>pasang</option>
                <option value="order" {{ strtolower($interaksi->tahapan ?? '') === 'order' ? 'selected' : '' }}>order</option>
                <option value="done" {{ strtolower($interaksi->tahapan ?? '') === 'done' ? 'selected' : '' }}>done</option>
            </select>
        </td>
    </tr>
    <tr>
        <th>PIC</th>
        <td>
            <input type="text" id="pic-input" class="form-control form-control-sm" 
                   value="{{ $interaksi->pic ?? (auth()->user()->name ?? '-') }}" readonly>
        </td>
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
   {{-- ========== KEBUTUHAN HARIAN ========== --}}
        <div class="bg-warning text-dark px-3 py-2 mb-2 rounded">
    <strong>Kebutuhan Harian</strong>
</div>
<form id="form-interaksi-realtime">
    @csrf
    <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">
    <div id="kebutuhan-container">
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
    </div>
</form>
{{-- ========== DATA SURVEY ========== --}}
<div class="bg-success text-white px-3 py-2 mb-2 rounded">
    <strong>Data Survey</strong>
</div>
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

{{-- ========== DATA PASANG ========== --}}
<div class="bg-info text-white px-3 py-2 mb-2 rounded">
    <strong>Data Pasang</strong>
</div>
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
