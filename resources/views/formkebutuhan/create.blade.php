@extends('layouts.template')
@section('content')
    <div class="container-fluid">
        <form id="formKebutuhan" action="{{ route('kebutuhan.store') }}" method="POST">
            @csrf
            <div class="card card-outline mt-3">
                <div class="card-header bg-gradient-purple d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-user mr-2"></i> Data Customer
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="tanggal_chat">Tanggal Interaksi</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="tanggal_chat" name="tanggal_chat"
                                value="{{ old('tanggal_chat', \Carbon\Carbon::today()->format('d-m-Y')) }}" required
                                placeholder="dd-mm-yyyy">
                            <button type="button" class="btn btn-outline-purple" id="btn-today">Hari Ini</button>
                            <button type="button" class="btn btn-outline-purple" id="btn-yesterday">Kemarin</button>
                        </div>
                        <small class="text-muted">Format: Tanggal-Bulan-Tahun</small>
                    </div>

                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="form-group position-relative">
                                <label for="customer_nama">Nama Pelanggan</label>
                                <input type="text" class="form-control" id="customer_nama" name="customer_nama"
                                    value="{{ old('customer_nama') }}" required autocomplete="off">
                                <input type="hidden" id="customer_id" name="customer_id">
                                <div id="customer_list" class="list-group mt-1 position-absolute w-100"></div>
                            </div>

                            <div class="form-group">
                                <label for="customer_kode">Kode Pelanggan</label>
                                <input type="text" class="form-control" id="customer_kode" name="customer_kode"
                                    value="{{ old('customer_kode') }}" required>
                            </div>

                            <div class="form-group">
                                <label for="customer_nohp">No HP</label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input type="text" class="form-control" id="customer_nohp" name="customer_nohp"
                                        placeholder="81234567890"
                                        value="{{ old('customer_nohp') ? ltrim(old('customer_nohp'), '+62') : '' }}"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_alamat">Alamat</label>
                                <textarea class="form-control" name="customer_alamat" id="customer_alamat" required>{{ old('customer_alamat') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label for="informasi_media">Informasi Media</label>
                                <select class="form-control" name="informasi_media" id="informasi_media" required>
                                    <option value="">-- Pilih Media --</option>
                                    <option value="google" {{ old('informasi_media') == 'google' ? 'selected' : '' }}>Google
                                    </option>
                                    <option value="medsos" {{ old('informasi_media') == 'medsos' ? 'selected' : '' }}>Media
                                        Sosial</option>
                                    <option value="offline" {{ old('informasi_media') == 'offline' ? 'selected' : '' }}>
                                        Offline</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card-footer text-right">
                <button type="submit" class="btn btn-purple">Simpan</button>
            </div>
    </div>
    </form>
    </div>
@endsection


@push('css')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Gradient card header ungu */
        .bg-gradient-purple {
            background: linear-gradient(135deg, #8147be, #c97aeb, #a661c2) !important;
            border-radius: 15px 15px 0 0 !important;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15) !important;
            color: #fff !important;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .card.card-outline .card-header .card-title {
            margin: 0;
            display: flex;
            align-items: center;
        }

        /* Tombol custom */
        .btn-outline-purple {
            color: #A374FF;
            border: 1px solid #A374FF;
            background-color: #fff;
        }

        .btn-outline-purple:hover {
            color: #fff;
            background-color: #A374FF;
            border-color: #A374FF;
        }

        .btn-purple {
            color: #fff;
            background-color: #A374FF;
        }

        .btn-purple:hover {
            background-color: #9364f2;
        }

        #customer_list {
            max-height: 200px;
            overflow-y: auto;
            background-color: white;
            border: 1px solid #ced4da;
            z-index: 9999;
        }
    </style>
@endpush

@push('js')
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Use event delegation for all dynamically loaded content
        $(function() {
            $("#tanggal_chat").datepicker({
                dateFormat: "dd-mm-yy",
                changeMonth: true,
                changeYear: true,
                maxDate: 0 // opsional: agar tidak bisa pilih tanggal di masa depan
            });

            // Tombol "Hari Ini"
            $(document).on('click', '#btn-today', function() {
                let today = $.datepicker.formatDate('dd-mm-yy', new Date());
                $('#tanggal_chat').val(today);
            });

            // Tombol "Kemarin"
            $(document).on('click', '#btn-yesterday', function() {
                let d = new Date();
                d.setDate(d.getDate() - 1);
                let yesterday = $.datepicker.formatDate('dd-mm-yy', d);
                $('#tanggal_chat').val(yesterday);
            });
        });

        $(document).on('input', '#customer_nama', function() {
            let keyword = $(this).val();
            let customerList = $('#customer_list');
            if (keyword.length >= 2) {
                $.get('{{ route('kebutuhan.searchCustomer') }}', {
                    keyword: keyword
                }, function(data) {
                    let list = '';
                    if (data.length > 0) {
                        data.forEach(customer => {
                            list += `<a href="#" class="list-group-item list-group-item-action" 
                                     data-id="${customer.customer_id}" 
                                     data-nama="${customer.customer_nama}"
                                     data-kode="${customer.customer_kode}"
                                     data-nohp="${customer.customer_nohp}" 
                                     data-alamat="${customer.customer_alamat}" 
                                     data-media="${customer.informasi_media}">
                                     ${customer.customer_nama} - ${customer.customer_nohp}
                                 </a>`;
                        });
                    } else {
                        $('#customer_id').val('');
                        list =
                            `<a href="#" class="list-group-item list-group-item-action disabled">Tidak ditemukan</a>`;
                    }
                    customerList.html(list).show();
                });
            } else {
                customerList.hide();
            }
        });

        $(document).on('click', '#customer_list .list-group-item', function(e) {
            e.preventDefault();
            if ($(this).hasClass('disabled')) return;

            $('#customer_id').val($(this).data('id'));
            $('#customer_nama').val($(this).data('nama'));
            $('#customer_kode').val($(this).data('kode'));

            let nohp = $(this).data('nohp');
            if (nohp !== null && nohp !== undefined) {
                nohp = nohp.toString().replace(/\D/g, '');
                if (nohp.startsWith('0')) {
                    nohp = nohp.substring(1);
                }
                $('#customer_nohp').val(nohp);
            } else {
                $('#customer_nohp').val('');
            }

            $('#customer_alamat').val($(this).data('alamat'));
            $('#informasi_media').val($(this).data('media')).trigger('change');
            $('#customer_list').hide();
        });

        // Handle form submission using event delegation
        $(document).on('submit', '#formKebutuhan', function(e) {
            e.preventDefault();
            let tglInput = $('#tanggal_chat').val();
            if (tglInput) {
                let parts = tglInput.split('-');
                if (parts.length === 3) {
                    $('#tanggal_chat').val(`${parts[2]}-${parts[1]}-${parts[0]}`);
                }
            }
            $.ajax({
                url: this.action,
                type: this.method,
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            title: 'Customer berhasil tersimpan',
                            text: "Lanjut isi data interaksi?",
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: 'Ya',
                            cancelButtonText: 'Tidak',
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // If yes, open a new modal for the next step
                                // Assumes you have a function to open the modal
                                $('#myModal .modal-content').load("{{ url('/rekap') }}/" +
                                    response.interaksi_id + "/show_ajax");
                                $('#myModal').modal('show');

                            } else {
                                // If not, redirect back to the index page
                                window.location.href = "{{ route('rekap.index') }}";
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = "Terjadi kesalahan. Pastikan semua data terisi dengan benar.";
                    if (errors) {
                        errorMessage = Object.values(errors).join('<br>');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: errorMessage
                    });
                }
            });
        });

        // You need to initialize Select2 and other plugins on the modal's `shown` event.
        // This part is crucial and needs to be added to your modal's JavaScript.
        $('#yourModalId').on('shown.bs.modal', function() {
            $('#produk_id').select2({
                placeholder: 'Pilih produk yang dibutuhkan',
                allowClear: true,
                dropdownParent: $('#yourModalId') // Important for Select2 inside a modal
            });
        });

        // Format phone number input using event delegation
        $(document).on('input', '#customer_nohp', function() {
            let val = $(this).val().replace(/\D/g, '');
            if (val.startsWith('0')) {
                val = val.substring(1);
            }
            $(this).val(val);
        });

        // Hide autosuggest list when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#customer_nama, #customer_list').length) {
                $('#customer_list').hide();
            }
        });
    </script>
@endpush
