<!-- resources/views/rekap/create_invoice.blade.php -->
<div class="modal-header">
    <h5 class="modal-title">Tambah Invoice</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="form-create-invoice" action="{{ route('invoice.store') }}" method="POST">
    @csrf

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

        /* suggestion kecil (sebagai bar klikable di bawah input) */
        .suggestion-bar {
            background: #f8f9fa;
            border: 1px solid #e2e6ea;
            padding: 8px;
            border-radius: 6px;
            margin-top: 6px;
            display: none;
            /* tampilkan saat fokus */
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
        <!-- Nomor & Customer (awal kosong, suggestion muncul sbg bar di bawah input saat fokus) -->
        <div class="row mb-2">
            <div class="col-md-6">
                <label>Nomor Invoice</label>
                <input type="text" id="nomor_invoice" name="nomor_invoice" class="form-control" value="">

                <!-- suggestion bar (klik untuk mengisi) -->
                <div id="nomor_suggestion" class="suggestion-bar" role="button" tabindex="0" aria-hidden="true">
                    <small>Terakhir: <span id="nomor_suggestion_text"></span></small>
                </div>

                <!-- inline warning di bawah kolom -->
                <div id="nomor_inline_warning" class="inline-warning">
                    Nomor ini sama dengan invoice terakhir yang tersimpan. Mohon ubah nomor agar tidak duplikat.
                </div>
            </div>

            <div class="col-md-6">
                <label>Customer Invoice</label>
                <input type="text" id="customer_invoice" name="customer_invoice" class="form-control" value="">

                <!-- suggestion bar untuk customer -->
                <div id="customer_suggestion" class="suggestion-bar" role="button" tabindex="0" aria-hidden="true">
                    <small>Terakhir: <span id="customer_suggestion_text"></span></small>
                </div>

                <!-- inline warning untuk customer -->
                <div id="customer_inline_warning" class="inline-warning">
                    Customer ini sama dengan customer pada invoice terakhir. Pastikan data sudah benar.
                </div>
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

                            <td>
                                <input type="number" name="kuantitas[]" class="form-control qty no-arrow text-center"
                                    min="0" value="{{ $kuantitas }}">
                            </td>

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

                            <td>
                                <input type="number" name="diskon[]" class="form-control diskon no-arrow text-center"
                                    step="0.01" min="0">
                            </td>

                            <td>
                                <input type="text" class="form-control grand_display rupiah" readonly
                                    value="{{ old('grand_total.' . $loop->index) ?? '' }}">
                                <input type="hidden" name="grand_total[]" class="grand_total"
                                    value="{{ old('grand_total.' . $loop->index) ?? '' }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr>

        <div class="row">
            <!-- Kiri -->
            <div class="col-md-6">
                <label>Tanggal DP</label>
                <input type="date" name="tanggal_dp" class="form-control">

                <label class="mt-2">Tanggal Pelunasan</label>
                <input type="date" name="tanggal_pelunasan" class="form-control">
            </div>

            <!-- Kanan -->
            <div class="col-md-6">
                <label>PPN</label>
                <div class="input-group mb-2">
                    <!-- persen PPN (boleh dikosongi) -->
                    <input type="number" id="ppn" name="ppn" class="form-control no-arrow text-center"
                        min="0" step="0.01">
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>

                    <!-- converter ke rupiah (display) -->
                    <input type="text" id="ppn_nominal_display" class="form-control rupiah ml-2" readonly
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

        <!-- Catatan -->
        <div class="row mt-3">
            <div class="col-12">
                <label>Catatan</label>
                <textarea name="catatan" class="form-control" rows="2"></textarea>
            </div>
        </div>

        <!-- hidden untuk reload modal after submit (jika ada), sediakan interaksi id -->
        <input type="hidden" id="interaksi_id" value="{{ $interaksi->interaksi_id ?? '' }}">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
    </div>
</form>

{{-- inject lastInvoice ke javascript --}}
<script>
    // lastInvoice berasal dari controller; null jika tidak ada
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

    /* === PPN === */
    function calculatePpnNominal(subtotalBefore) {
        let percentRaw = $('#ppn').val();
        if (percentRaw === '' || percentRaw === null || typeof percentRaw === 'undefined') return 0;
        let percent = parseFloat(percentRaw);
        if (isNaN(percent) || percent <= 0) return 0;
        return Math.round(subtotalBefore * (percent / 100));
    }

    /* === Row & Summary === */
    function hitungRow($row) {
        let qty = parseInt($row.find('.qty').val()) || 0;
        let harga = parseInt($row.find('.harga').val()) || 0;
        // if diskon empty -> treat as 0 (so server receives 0 if you want)
        let diskonRaw = $row.find('.diskon').val();
        let diskon = (diskonRaw === '' || typeof diskonRaw === 'undefined') ? 0 : parseFloat(diskonRaw) || 0;

        let total = qty * harga;
        // diskon percent applied to total
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

        let pot = parseRupiah($('#potongan_display').val() || '') || 0;
        let cash = parseRupiah($('#cashback_display').val() || '') || 0;
        let dpVal = parseRupiah($('#dp_display').val() || '') || 0;

        // PPN dihitung dari gross (grandSum) — tidak dikurangi potongan/cashback
        let nominalPpn = calculatePpnNominal(grandSum);

        // total akhir: gross + ppn - pot - cash
        let totalAkhir = (grandSum + nominalPpn) - pot - cash;
        if (totalAkhir < 0) totalAkhir = 0;

        // sisa pelunasan = total akhir - dp
        let sisa = totalAkhir - dpVal;

        // tulis ke field
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

    $('#potongan_display, #cashback_display, #dp_display').on('input', function() {
        let id = $(this).attr('id');
        let v = parseRupiah($(this).val());
        $(this).val(v ? formatRupiah(v) : '');
        if (id === 'potongan_display') $('#potongan').val(v);
        if (id === 'cashback_display') $('#cashback').val(v);
        if (id === 'dp_display') $('#dp').val(v);
        hitungSummary();
    });

    $('#ppn').on('input', function() {
        hitungSummary();
    });

    /* === Init === */
    $(document).ready(function() {
        // populate harga_display dari hidden jika ada
        $('#tablePasang tbody tr').each(function() {
            let $r = $(this);
            if ($r.find('input[name="harga_satuan[]"]').length && $r.find(
                    'input[name="harga_satuan[]"]').val()) {
                let hv = parseRupiah($r.find('input[name="harga_satuan[]"]').val());
                $r.find('.harga_display').val(hv ? formatRupiah(hv) : '');
                $r.find('.harga').val(hv);
            }
            // jika diskon kosong treat as 0 ketika menghitung
            hitungRow($r);
        });

        // 1x kalkulasi summary
        hitungSummary();

        // suggestion behavior (sama seperti sebelumnya)
        $('#nomor_invoice').on('focus', function() {
            if (lastInvoice && lastInvoice.nomor_invoice) {
                $('#nomor_suggestion_text').text(lastInvoice.nomor_invoice);
                $('#nomor_suggestion').show().attr('aria-hidden', 'false');
            }
        }).on('blur', function() {
            setTimeout(function() {
                $('#nomor_suggestion').hide().attr('aria-hidden', 'true');
            }, 200);
        });
        $('#nomor_suggestion').on('click keypress', function(e) {
            if (e.type === 'click' || (e.type === 'keypress' && (e.key === 'Enter' || e.key === ' '))) {
                if (!lastInvoice) return;
                $('#nomor_invoice').val(lastInvoice.nomor_invoice).trigger('input').focus();
                $('#nomor_suggestion').hide().attr('aria-hidden', 'true');
            }
        });

        $('#customer_invoice').on('focus', function() {
            if (lastInvoice && lastInvoice.customer_invoice) {
                $('#customer_suggestion_text').text(lastInvoice.customer_invoice);
                $('#customer_suggestion').show().attr('aria-hidden', 'false');
            }
        }).on('blur', function() {
            setTimeout(function() {
                $('#customer_suggestion').hide().attr('aria-hidden', 'true');
            }, 200);
        });
        $('#customer_suggestion').on('click keypress', function(e) {
            if (e.type === 'click' || (e.type === 'keypress' && (e.key === 'Enter' || e.key === ' '))) {
                if (!lastInvoice) return;
                $('#customer_invoice').val(lastInvoice.customer_invoice).trigger('input').focus();
                $('#customer_suggestion').hide().attr('aria-hidden', 'true');
            }
        });

        /* === SUBMIT AJAX === */
        $("#form-create-invoice").on("submit", function(e) {
            e.preventDefault();

            // disable tombol submit untuk menghindari double-post
            let $submitBtn = $(this).find('button[type="submit"]');
            $submitBtn.prop('disabled', true);

            // Pastikan semua hidden numeric terisi sebelum FormData dibuat
            $('#tablePasang tbody tr').each(function() {
                let $row = $(this);

                // harga
                let hargaVal = 0;
                if ($row.find('.harga_display').length) hargaVal = parseRupiah($row.find(
                    '.harga_display').val());
                if ($row.find('input[name="harga_satuan[]"]').length) {
                    $row.find('input[name="harga_satuan[]"]').val(hargaVal);
                } else if ($row.find('.harga').length) {
                    $row.find('.harga').val(hargaVal);
                }

                // make sure total & grand exist
                let totalVal = parseInt($row.find('.total').val()) || 0;
                let grandVal = parseInt($row.find('.grand_total').val()) || 0;
                if ($row.find('input[name="total[]"]').length) $row.find(
                    'input[name="total[]"]').val(totalVal);
                if ($row.find('input[name="grand_total[]"]').length) $row.find(
                    'input[name="grand_total[]"]').val(grandVal);

                // jika diskon kosong -> set 0 (ubah kalau mau biarkan kosong)
                if ($row.find('.diskon').val() === '') $row.find('.diskon').val(0);
            });

            // pastikan pot/cash/dp terisi
            if (!$('#potongan').val()) $('#potongan').val(parseRupiah($('#potongan_display').val()));
            if (!$('#cashback').val()) $('#cashback').val(parseRupiah($('#cashback_display').val()));
            if (!$('#dp').val()) $('#dp').val(parseRupiah($('#dp_display').val()));

            // pastikan PPN sinkron
            hitungSummary();

            // buat FormData dan pastikan ppn fields ikut ter-append
            let form = $(this)[0];
            let formData = new FormData(form);
            if (!formData.has('ppn')) formData.append('ppn', $('#ppn').val() || '');
            if (!formData.has('nominal_ppn')) formData.append('nominal_ppn', $('#ppn_nominal').val() ||
                0);

            // AJAX
            $.ajax({
                url: $(this).attr("action"),
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (typeof toastr !== 'undefined') toastr.success(
                        "Invoice berhasil disimpan");
                    else if (typeof Swal !== 'undefined') Swal.fire("Sukses",
                        "Invoice berhasil disimpan", "success");

                    // reload datatable rekap jika ada
                    try {
                        if (typeof tableRekap !== 'undefined' && tableRekap.ajax) tableRekap
                            .ajax.reload(null, false);
                    } catch (err) {
                        console.warn(err);
                    }

                    // reload detail interaksi
                    let interaksiId = $("#interaksi_id").val();
                    if (interaksiId) {
                        try {
                            $("#myModal").load("{{ url('rekap') }}/" + interaksiId +
                                "/show_ajax");
                        } catch (err) {
                            console.warn(err);
                        }
                    }

                    // tutup modal
                    try {
                        $("#crudModal").modal("hide");
                    } catch (err) {}
                },
                error: function(xhr) {
                    // enable kembali tombol
                    $submitBtn.prop('disabled', false);

                    // coba tampilkan pesan validasi dari server jika ada
                    if (xhr.responseJSON) {
                        let json = xhr.responseJSON;
                        // Laravel validation biasanya di bawah json.errors
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
                                });
                            } else if (typeof toastr !== 'undefined') {
                                toastr.error(msg);
                            } else {
                                alert(msg.replace(/<br>/g, '\n'));
                            }
                            return;
                        }
                        // jika ada message umum
                        if (json.message) {
                            if (typeof Swal !== 'undefined') Swal.fire('Error', json
                                .message, 'error');
                            else alert(json.message);
                            return;
                        }
                    }

                    // fallback generic
                    if (typeof Swal !== 'undefined') Swal.fire("Gagal",
                        "Terjadi kesalahan server.", "error");
                    else if (typeof toastr !== 'undefined') toastr.error(
                        "Terjadi kesalahan server.");
                    else alert("Terjadi kesalahan server.");

                    console.error("Server Error:", xhr.responseText);
                }
            });
        });
    });
</script>
