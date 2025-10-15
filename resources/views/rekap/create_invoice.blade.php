<!-- resources/views/rekap/create_invoice.blade.php -->
<div class="modal-header bg-wallpaper-gradient text-white">
    <h5 class="modal-title">Tambah Invoice</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true" class="text-white">&times;</span>
    </button>
</div>
<form id="form-create-invoice" action="{{ route('invoice.store') }}" method="POST">
    @csrf
    <input type="hidden" id="interaksi_id" name="interaksi_id" value="{{ $interaksi->interaksi_id ?? '' }}">

    <style>
        input.form-control.no-arrow::-webkit-outer-spin-button,
        input.form-control.no-arrow::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input.form-control.no-arrow[type=number] {
            -moz-appearance: textfield;
            appearance: textfield;
        }

        .table .form-control.no-arrow {
            padding-right: .5rem;
        }

        .suggestion-bar {
            background: #f8f9fa;
            border: 1px solid #e2e6ea;
            padding: 8px;
            border-radius: 6px;
            margin-top: 6px;
            display: none;
            cursor: pointer;
        }

        .suggestion-bar small {
            margin: 0;
        }

        .suggestion-bar:hover {
            background: #e9f0ff;
        }

        .inline-warning {
            color: #856404;
            background: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 6px 8px;
            border-radius: 4px;
            margin-top: 6px;
            display: none;
        }
    </style>

    <div class="modal-body">
        <!-- Nomor & Customer -->
        <div class="row mb-2">
            <div class="col-md-6">
                <label>Nomor Invoice</label>
                <input type="text" id="nomor_invoice" name="nomor_invoice" class="form-control" value="">
                <div id="nomor_suggestion" class="suggestion-bar" role="button" tabindex="0" aria-hidden="true">
                    <small>Terakhir: <span id="nomor_suggestion_text"></span></small>
                </div>
                <div id="nomor_inline_warning" class="inline-warning">Nomor ini sama dengan invoice terakhir. Mohon ubah
                    nomor agar tidak duplikat.</div>
            </div>

            <div class="col-md-6">
                <label>Customer ID</label>
                <input type="text" id="customer_invoice" name="customer_invoice" class="form-control" value="">
                <div id="customer_suggestion" class="suggestion-bar" role="button" tabindex="0" aria-hidden="true">
                    <small>Terakhir: <span id="customer_suggestion_text"></span></small>
                </div>
                <!-- Customer duplicate warning dihapus / tidak digunakan karena duplikat boleh -->
                <!-- <div id="customer_inline_warning" class="inline-warning">Customer ini sama dengan customer pada invoice
                    terakhir. Pastikan data sudah benar.</div> -->
            </div>
        </div>

        <!-- Pesanan masuk & Batas pelunasan -->
        <div class="row mb-2">
            <div class="col-md-6">
                <label>Pesanan Masuk</label>
                <input type="date" name="pesanan_masuk" class="form-control">
            </div>
            <div class="col-md-6">
                <label>Batas Pelunasan</label>
                <select name="batas_pelunasan" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="H+1 setelah pasang">H+1 setelah pasang</option>
                    <option value="H-1 sebelum kirim">H-1 sebelum kirim</option>
                </select>
            </div>
        </div>

        <!-- Table Pasang/Kirim -->
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="tablePasang">
                <thead>
                    <tr>
                        <th style="width:15%;">Produk</th>
                        <th style="width:5%;">Kuantitas</th>
                        <th style="width:14%;">Harga Satuan</th>
                        <th style="width:20%;">Total</th>
                        <th style="width:5%;">Diskon (%)</th>
                        <th style="width:20%;">Grand Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pasang as $p)
                        @php
                            $produk = $p->produk ?? null;
                            $kuantitas = $p->kuantitas ?? 1;
                        @endphp
                        <tr data-pasangkirim-id="{{ $p->pasangkirim_id }}">
                            <td>
                                {{ ($produk->kategori->kategori_nama ?? '-') . ' — ' . ($produk->produk_nama ?? '-') }}
                                <input type="hidden" name="pasangkirim_id[]" value="{{ $p->pasangkirim_id }}">
                            </td>
                            <td><input type="number" name="kuantitas[]" class="form-control qty no-arrow text-center"
                                    min="0" value="{{ $kuantitas }}"></td>
                            <td>
                                <input type="text" class="form-control harga_display rupiah"
                                    value="{{ old('harga_satuan.' . $loop->index) ?? '' }}">
                                <input type="hidden" name="harga_satuan[]" class="harga"
                                    value="{{ old('harga_satuan.' . $loop->index) ?? '' }}">
                            </td>
                            <td>
                                <input type="text" class="form-control total_display rupiah" readonly
                                    value="{{ old('total.' . $loop->index) ?? '' }}">
                                <input type="hidden" name="total[]" class="total"
                                    value="{{ old('total.' . $loop->index) ?? '' }}">
                            </td>
                            <td><input type="number" name="diskon[]" class="form-control diskon no-arrow text-center"
                                    step="0.01" min="0"></td>
                            <td>
                                <input type="text" class="form-control grand_display rupiah" readonly
                                    value="{{ old('grand_total.' . $loop->index) ?? '' }}">
                                <input type="hidden" name="grand_total[]" class="grand_total"
                                    value="{{ old('grand_total.' . $loop->index) ?? '' }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right font-weight-bold">Jumlah grand total semua produk</td>
                        <td>
                            <div class="input-group">
                                <input type="text" id="total_produk_display" class="form-control rupiah" readonly>
                                <input type="hidden" name="total_produk" id="total_produk" value="0">
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <label>Tanggal DP</label>
                <input type="date" name="tanggal_dp" class="form-control">
                <label class="mt-2">Tanggal Pelunasan</label>
                <input type="date" name="tanggal_pelunasan" class="form-control">
            </div>
            <div class="col-md-6">
                <!-- PPN -->
                <label>PPN</label>
                <div class="input-group mb-2">
                    <input type="number" id="ppn" name="ppn" class="form-control no-arrow text-center"
                        min="0" step="0.01">
                    <div class="input-group-append"><span class="input-group-text">%</span></div>

                    <input type="text" id="ppn_nominal_display" class="form-control rupiah ml-2"
                        placeholder="Nominal PPN">
                    <input type="hidden" name="nominal_ppn" id="ppn_nominal" value="0">
                </div>

                <label>Potongan Harga</label>
                <input type="text" id="potongan_display" class="form-control rupiah" value="">
                <input type="hidden" name="potongan_harga" id="potongan">

                <label class="mt-2">Cashback</label>
                <input type="text" id="cashback_display" class="form-control rupiah" value="">
                <input type="hidden" name="cashback" id="cashback">

                <label class="mt-2">DP</label>
                <input type="text" id="dp_display" class="form-control rupiah" value="">
                <input type="hidden" name="dp" id="dp">

                <label class="mt-2">Sisa Pelunasan</label>
                <input type="text" id="sisa_display" class="form-control rupiah" readonly>
                <input type="hidden" name="sisa_pelunasan" id="sisa">

                <label class="mt-2">Total Akhir</label>
                <input type="text" id="totalakhir_display" class="form-control rupiah" readonly>
                <input type="hidden" name="total_akhir" id="totalakhir">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <label>Catatan</label>
                <textarea name="catatan" class="form-control" rows="2"></textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
    </div>
</form>

{{-- inject lastInvoice ke javascript --}}
<script>
    let lastInvoice = {!! json_encode($lastInvoice) !!} || null;
</script>

<script>
    /* === Helpers === */
    function formatRupiah(num) {
        if (num === '' || num === null || typeof num === 'undefined') return '';
        num = parseInt(num) || 0;
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function parseRupiah(str) {
        if (str === '' || str === null || typeof str === 'undefined') return 0;
        return parseInt(String(str).replace(/[^0-9]/g, '')) || 0;
    }

    function hitungRow($row) {
        let qty = parseInt($row.find('.qty').val()) || 0;
        let harga = parseInt($row.find('.harga').val()) || 0;
        let diskonRaw = $row.find('.diskon').val();
        let diskon = (diskonRaw === '' || typeof diskonRaw === 'undefined') ? 0 : parseFloat(diskonRaw) || 0;

        let total = qty * harga;
        let grand = Math.round(total - Math.round(total * (diskon / 100)));

        $row.find('.total').val(total);
        $row.find('.total_display').val(total ? formatRupiah(total) : '');
        $row.find('.grand_total').val(grand);
        $row.find('.grand_display').val(grand ? formatRupiah(grand) : '');

        hitungSummary();
    }

    function hitungSummary() {
        let grandSum = 0;
        $('#tablePasang tbody tr').each(function() {
            grandSum += parseInt($(this).find('.grand_total').val()) || 0;
        });

        $('#total_produk').val(grandSum);
        $('#total_produk_display').val(grandSum ? formatRupiah(grandSum) : '');

        // Ambil nominal PPN dari input manual (ppn_nominal_display / ppn_nominal)
        let nominalPpn = parseRupiah($('#ppn_nominal_display').val() || '') || 0;

        let pot = parseRupiah($('#potongan_display').val() || '') || 0;
        let cash = parseRupiah($('#cashback_display').val() || '') || 0;
        let dpVal = parseRupiah($('#dp_display').val() || '') || 0;

        let totalAkhir = (grandSum + nominalPpn) - pot - cash;
        if (totalAkhir < 0) totalAkhir = 0;

        let sisa = totalAkhir - dpVal;

        $('#ppn_nominal').val(nominalPpn);
        $('#ppn_nominal_display').val(nominalPpn ? formatRupiah(nominalPpn) : '');

        $('#totalakhir').val(totalAkhir);
        $('#totalakhir_display').val(totalAkhir ? formatRupiah(totalAkhir) : '');

        $('#sisa').val(sisa);
        $('#sisa_display').val(sisa ? formatRupiah(sisa) : '');

        $('#potongan').val(pot);
        $('#cashback').val(cash);
        $('#dp').val(dpVal);
    }

    /* === Events === */
    $(document).on('input', '.harga_display', function() {
        let $row = $(this).closest('tr');
        let v = parseRupiah($(this).val());
        if ($row.find('.harga').length) {
            $row.find('.harga').val(v);
        } else {
            $row.find('input[name="harga_satuan[]"]').val(v);
        }
        $(this).val(v ? formatRupiah(v) : '');
        hitungRow($row);
    });

    $(document).on('input', '.qty, .diskon', function() {
        let $row = $(this).closest('tr');
        hitungRow($row);
    });

    // potongan, cashback, dp input handling (rupiah formatting)
    $('#potongan_display, #cashback_display, #dp_display').on('input', function() {
        let id = $(this).attr('id');
        let v = parseRupiah($(this).val());
        $(this).val(v ? formatRupiah(v) : '');
        if (id === 'potongan_display') $('#potongan').val(v);
        if (id === 'cashback_display') $('#cashback').val(v);
        if (id === 'dp_display') $('#dp').val(v);
        hitungSummary();
    });

    // PPN nominal input (manual, rupiah formatting)
    $('#ppn_nominal_display').on('input', function() {
        let v = parseRupiah($(this).val());
        $(this).val(v ? formatRupiah(v) : '');
        $('#ppn_nominal').val(v);
        hitungSummary();
    });

    // PPN persen input (tetap numeric, tidak memengaruhi nominal otomatis)
    $('#ppn').on('input', function() {
        // hanya pastikan nilai valid numerik (boleh desimal)
        let raw = $(this).val();
        let v = parseFloat(raw);
        if (isNaN(v)) v = '';
        $(this).val(v === '' ? '' : v);
        // tidak memicu perubahan nominal_ppn secara otomatis
    });

    $(document).ready(function() {
        $('#tablePasang tbody tr').each(function() {
            let $r = $(this);
            if ($r.find('input[name="harga_satuan[]"]').length && $r.find(
                    'input[name="harga_satuan[]"]').val()) {
                let hv = parseRupiah($r.find('input[name="harga_satuan[]"]').val());
                $r.find('.harga_display').val(hv ? formatRupiah(hv) : '');
                $r.find('.harga').val(hv);
            }
            hitungRow($r);
        });

        hitungSummary();

        // suggestion behavior + inline warning saat mengetik
        let lastInvoice = {!! json_encode($lastInvoice ?? null) !!} || null;
        console.log('[create_invoice] lastInvoice =', lastInvoice);

        function safeTriggerInput($el) {
            try {
                $el.trigger('input');
            } catch (e) {}
        }

        if (lastInvoice) {
            if (lastInvoice.nomor_invoice) $('#nomor_suggestion_text').text(lastInvoice.nomor_invoice);
            if (lastInvoice.customer_invoice) $('#customer_suggestion_text').text(lastInvoice.customer_invoice);
        }

        // Nomor Invoice (tetap ada peringatan duplikat karena nomor memang sebaiknya unik)
        $(document).on('focus input', '#nomor_invoice', function() {
            let v = ($(this).val() || '').trim();
            if (lastInvoice && lastInvoice.nomor_invoice) {
                $('#nomor_suggestion_text').text(lastInvoice.nomor_invoice);
                $('#nomor_suggestion').show().attr('aria-hidden', 'false');
                if (v !== '' && v === String(lastInvoice.nomor_invoice)) {
                    $('#nomor_inline_warning').show();
                } else {
                    $('#nomor_inline_warning').hide();
                }
            }
        });

        $(document).on('blur', '#nomor_invoice', function() {
            setTimeout(function() {
                $('#nomor_suggestion').hide().attr('aria-hidden', 'true');
            }, 250);
            setTimeout(function() {
                $('#nomor_inline_warning').hide();
            }, 300);
        });

        $(document).on('mousedown keypress', '#nomor_suggestion', function(e) {
            if (e.type === 'mousedown' || (e.type === 'keypress' && (e.key === 'Enter' || e
                    .key === ' '))) {
                if (!lastInvoice || !lastInvoice.nomor_invoice) return;
                e.preventDefault();
                $('#nomor_invoice').val(lastInvoice.nomor_invoice);
                safeTriggerInput($('#nomor_invoice'));
                $('#nomor_inline_warning').show();
                setTimeout(function() {
                    $('#nomor_suggestion').hide().attr('aria-hidden', 'true');
                }, 120);
                console.log('[create_invoice] nomor suggestion applied:', lastInvoice.nomor_invoice);
            }
        });

        // Customer Invoice: suggestion tetap ada, TAPI tidak menampilkan warning duplikat dan tidak memblokir submit
        $(document).on('focus input', '#customer_invoice', function() {
            let v = ($(this).val() || '').trim();
            if (lastInvoice && lastInvoice.customer_invoice) {
                $('#customer_suggestion_text').text(lastInvoice.customer_invoice);
                $('#customer_suggestion').show().attr('aria-hidden', 'false');
                // tidak menampilkan customer_inline_warning (duplikat diperbolehkan)
            }
        });

        $(document).on('blur', '#customer_invoice', function() {
            setTimeout(function() {
                $('#customer_suggestion').hide().attr('aria-hidden', 'true');
            }, 250);
            // tidak ada inline warning hide karena tidak digunakan
        });

        $(document).on('mousedown keypress', '#customer_suggestion', function(e) {
            if (e.type === 'mousedown' || (e.type === 'keypress' && (e.key === 'Enter' || e
                    .key === ' '))) {
                if (!lastInvoice || !lastInvoice.customer_invoice) return;
                e.preventDefault();
                $('#customer_invoice').val(lastInvoice.customer_invoice);
                safeTriggerInput($('#customer_invoice'));
                // tidak menampilkan warning karena duplikat customer boleh
                setTimeout(function() {
                    $('#customer_suggestion').hide().attr('aria-hidden', 'true');
                }, 120);
                console.log('[create_invoice] customer suggestion applied:', lastInvoice
                    .customer_invoice);
            }
        });
        /* === END suggestion === */

        /* === SUBMIT AJAX === */
        $("#form-create-invoice").on("submit", function(e) {
            e.preventDefault();

            // ambil nilai input yang relevan
            let nomorVal = ($('#nomor_invoice').val() || '').trim();
            // customer duplicate tidak lagi menjadi alasan untuk menolak submit
            let customerVal = ($('#customer_invoice').val() || '').trim();

            // cek cepat client-side terhadap lastInvoice untuk nomor (masih wajib ubah jika duplikat)
            if (lastInvoice) {
                if (nomorVal !== '' && lastInvoice.nomor_invoice && nomorVal === String(lastInvoice
                        .nomor_invoice)) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Duplikat Nomor Invoice',
                            html: 'Nomor invoice sama dengan invoice terakhir. Silakan ubah nomor invoice.',
                            icon: 'warning',
                            confirmButtonText: 'Ubah Nomor'
                        }).then(() => {
                            $('#nomor_invoice').focus().select();
                        });
                    } else {
                        alert(
                            'Nomor invoice sama dengan invoice terakhir. Silakan ubah nomor invoice.'
                        );
                        $('#nomor_invoice').focus().select();
                    }
                    return;
                }
                // NOTE: tidak memblokir jika customer sama dengan lastInvoice.customer_invoice
            }

            // jika tidak duplikat local -> kirim ke server (server-side juga akan cek sistem-wide)
            proceedSubmit($("#form-create-invoice"));
        });

        function proceedSubmit($form) {
            let $submitBtn = $form.find('button[type="submit"]');
            $submitBtn.prop('disabled', true);

            // Pastikan semua hidden numeric terisi
            $('#tablePasang tbody tr').each(function() {
                let $row = $(this);
                let hargaVal = 0;
                if ($row.find('.harga_display').length) hargaVal = parseRupiah($row.find(
                    '.harga_display').val());
                if ($row.find('input[name="harga_satuan[]"]').length) {
                    $row.find('input[name="harga_satuan[]"]').val(hargaVal);
                } else if ($row.find('.harga').length) {
                    $row.find('.harga').val(hargaVal);
                }

                let totalVal = parseInt($row.find('.total').val()) || 0;
                let grandVal = parseInt($row.find('.grand_total').val()) || 0;
                if ($row.find('input[name="total[]"]').length) $row.find('input[name="total[]"]').val(
                    totalVal);
                if ($row.find('input[name="grand_total[]"]').length) $row.find(
                    'input[name="grand_total[]"]').val(grandVal);

                if ($row.find('.diskon').val() === '') $row.find('.diskon').val(0);
            });

            if (!$('#potongan').val()) $('#potongan').val(parseRupiah($('#potongan_display').val()));
            if (!$('#cashback').val()) $('#cashback').val(parseRupiah($('#cashback_display').val()));
            if (!$('#dp').val()) $('#dp').val(parseRupiah($('#dp_display').val()));

            // pastikan total_produk & nominal PPN sinkron sebelum submit
            hitungSummary();

            let form = $form[0];
            let formData = new FormData(form);
            // pastikan nominal_ppn & ppn (persen) disertakan
            if (!formData.has('nominal_ppn')) formData.append('nominal_ppn', $('#ppn_nominal').val() || 0);
            if (!formData.has('ppn')) formData.append('ppn', $('#ppn').val() || '');

            if (!formData.has('total_produk')) formData.append('total_produk', $('#total_produk').val() || 0);

            $.ajax({
                url: $form.attr("action"),
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    $submitBtn.prop('disabled', false);

                    if (typeof toastr !== 'undefined') toastr.success(res.message ||
                        "Invoice berhasil disimpan");
                    else if (typeof Swal !== 'undefined') Swal.fire("Sukses", res.message ||
                        "Invoice berhasil disimpan", "success");

                    if (res && (res.nomor_invoice || res.customer_invoice)) {
                        lastInvoice = {
                            nomor_invoice: res.nomor_invoice || (res.invoice && res.invoice
                                .nomor_invoice) || null,
                            customer_invoice: res.customer_invoice || (res.invoice && res
                                .invoice.customer_invoice) || null,
                            invoice_id: res.invoice_id || (res.invoice && res.invoice
                                .invoice_id) || null
                        };
                        if (lastInvoice.nomor_invoice) {
                            $('#nomor_suggestion_text').text(lastInvoice.nomor_invoice);
                        }
                        if (lastInvoice.customer_invoice) {
                            $('#customer_suggestion_text').text(lastInvoice.customer_invoice);
                        }
                    }

                    try {
                        if (typeof tableRekap !== 'undefined' && tableRekap.ajax) tableRekap.ajax
                            .reload(null, false);
                    } catch (err) {
                        console.warn(err);
                    }

                    let interaksiId = $("#interaksi_id").val();
                    if (interaksiId) {
                        try {
                            $("#myModal").load("{{ url('rekap') }}/" + interaksiId +
                                "/show_ajax");
                        } catch (err) {
                            console.warn(err);
                        }
                    }

                    try {
                        $("#crudModal").modal("hide");
                    } catch (err) {}
                },
                error: function(xhr) {
                    $submitBtn.prop('disabled', false);

                    if (xhr.responseJSON) {
                        let json = xhr.responseJSON;
                        if (json.errors) {
                            let messages = [];
                            for (let k in json.errors) {
                                if (json.errors.hasOwnProperty(k)) {
                                    messages.push(json.errors[k].join(', '));
                                }
                            }
                            let msg = messages.join('<br>');
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validasi',
                                    html: msg
                                }).then(() => {
                                    if (json.errors.nomor_invoice) {
                                        $('#nomor_invoice').focus().select();
                                    } else if (json.errors.customer_invoice) {
                                        $('#customer_invoice').focus().select();
                                    }
                                });
                            } else {
                                alert(msg.replace(/<br>/g, '\n'));
                                if (json.errors.nomor_invoice) {
                                    $('#nomor_invoice').focus().select();
                                } else if (json.errors.customer_invoice) {
                                    $('#customer_invoice').focus().select();
                                }
                            }
                            return;
                        }
                        if (json.message) {
                            if (typeof Swal !== 'undefined') Swal.fire('Error', json.message,
                                'error');
                            else alert(json.message);
                            return;
                        }
                    }

                    if (typeof Swal !== 'undefined') Swal.fire("Gagal", "Terjadi kesalahan server.",
                        "error");
                    else alert("Terjadi kesalahan server.");

                    console.error("Server Error:", xhr.responseText);
                }
            });
        }
    });
</script>
