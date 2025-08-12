<div id="modal-user" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {{-- HEADER --}}
        <div class="modal-header">
            <h5 class="modal-title">Detail Kebutuhan & Survey/Pasang</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>

        <div class="modal-body">
            
          {{-- ========== DETAIL PRODUK ========== --}}
<div class="bg-primary text-white px-3 py-2 mb-2 rounded">
    <strong>Detail Kebutuhan</strong>
</div>
<table class="table table-bordered table-striped table-hover table-sm mb-4"> 
    <tr> 
        <th>ID produk</th> 
        <td>{{ $interaksi->produk_id }}</td> 
    </tr> 
    <tr> 
        <th>Kategori</th> 
        <td>{{ $interaksi->produk->kategori->kategori_nama ?? '-' }}</td> 
    </tr> 
    <tr> 
        <th>Nama produk</th> 
        <td>{{ $interaksi->produk->produk_nama ?? '-' }}</td> 
    </tr> 
    <tr> 
        <th>Media</th> 
        <td>{{ $interaksi->media ?? '-' }}</td> 
    </tr>
    <tr>
        <th>Tahapan</th>
        <td>
            <select id="tahapan-select" class="form-control form-control-sm">
                <option value="identifikasi" {{ strtolower($interaksi->tahapan ?? '') === 'identifikasi' ? 'selected' : '' }}>identifikasi</option>
                <option value="rincian" {{ strtolower($interaksi->tahapan ?? '') === 'rincian' ? 'selected' : '' }}>rincian</option>
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
        <th>Follow Up</th>
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
    <tr>
        <th>Close</th>
        <td>
            <input type="text" id="close-input" class="form-control form-control-sm"
                   value="{{ $closeValue }}" readonly>
        </td>
    </tr>
</table>
{{-- ========== DATA SURVEY/PASANG ========== --}}
<div class="bg-success text-white px-3 py-2 mb-2 rounded">
    <strong>Data Survey / Pasang</strong>
</div>
<form id="form-survey">
    @csrf
    <input type="hidden" name="interaksi_id" value="{{ $interaksi->interaksi_id }}">
    
    <table class="table table-bordered table-striped table-hover table-sm mb-4">
        <tr>
            <th>Alamat</th>
            <td>
                <textarea name="alamat" class="form-control form-control-sm" rows="2">{{ $interaksi->alamat ?? '' }}</textarea>
            </td>
        </tr>
        <tr>
            <th>Waktu Survey</th>
            <td>
                <input type="datetime-local" name="waktu_survey" class="form-control form-control-sm"
                       value="{{ $interaksi->waktu_survey ? date('Y-m-d\TH:i', strtotime($interaksi->waktu_survey)) : '' }}">
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
        </div>

        {{-- FOOTER --}}
        <div class="modal-footer">
            <button type="button" id="btn-save-followup" class="btn btn-primary">Simpan</button>
        </div>
    </div>
</div>

<!-- Tambahkan di layout atau sebelum script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Event untuk follow-up -> close otomatis
$(document).on('change', '#follow-up-select', function () {
    let followUpVal = ($(this).val() || '').trim();
    let closeVal = 'Follow Up 1'; // default

    switch (followUpVal.toLowerCase()) {
        case 'follow up 1':
            closeVal = 'Follow Up 2';
            break;
        case 'follow up 2':
            closeVal = 'Broadcast';
            break;
        case 'closing survey':
        case 'closing pasang':
        case 'closing product':
        case 'closing all':
            closeVal = 'Closing';
            break;
    }

    $('#close-input').val(closeVal);
});

// Event untuk tahapan -> PIC otomatis
$(document).on('change', '#tahapan-select', function () {
    let tahapanVal = ($(this).val() || '').trim().toLowerCase();

    if (tahapanVal === 'identifikasi') {
        $('#pic-input').val('CS');
    } else if (tahapanVal === 'rincian') {
        $('#pic-input').val('Konsultan');
    } else {
        $('#pic-input').val('-'); // fallback
    }
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
            follow_up: $('#follow-up-select').val()
        },
        success: function(res) {
            if (res.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Follow up berhasil disimpan',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    $('#modal-user').modal('hide');
                    // Bisa tambahkan reload data table kalau perlu
                    // $('#datatable').DataTable().ajax.reload();
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
