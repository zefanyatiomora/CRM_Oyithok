<!-- resources/views/rekap/edit_invoice.blade.php -->
<div class="modal-header">
    <h5 class="modal-title">Edit Invoice</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="form-edit-invoice" action="{{ route('invoice.update', $invoice->invoice_id) }}" method="POST">
    @csrf
    @method('PUT')

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
    </style>

    <div class="modal-body">
        <!-- Nomor & Customer -->
        <div class="row mb-2">
            <div class="col-md-6">
                <label>Nomor Invoice</label>
                <input type="text" name="nomor_invoice" class="form-control" value="{{ $invoice->nomor_invoice }}">
            </div>
            <div class="col-md-6">
                <label>Customer Invoice</label>
                <input type="text" name="customer_invoice" class="form-control"
                    value="{{ $invoice->customer_invoice ?? ($interaksi->customer->customer_nama ?? '') }}">
            </div>
        </div>

        <!-- Pesanan masuk & Batas pelunasan -->
        <div class="row mb-2">
            <div class="col-md-6">
                <label>Pesanan Masuk</label>
                <input type="date" name="pesanan_masuk" class="form-control" value="{{ $invoice->pesanan_masuk }}">
            </div>
            <div class="col-md-6">
                <label>Batas Pelunasan</label>
                <select name="batas_pelunasan" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="H+1 setelah pasang"
                        {{ $invoice->batas_pelunasan == 'H+1 setelah pasang' ? 'selected' : '' }}>H+1 setelah pasang
                    </option>
                    <option value="H-1 sebelum kirim"
                        {{ $invoice->batas_pelunasan == 'H-1 sebelum kirim' ? 'selected' : '' }}>H-1 sebelum kirim
                    </option>
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
                    @foreach ($invoice->details as $detail)
                        @php
                            $p = $detail->pasang;
                            $produk = $p->produk ?? null;
                        @endphp
                        <tr data-pasangkirim-id="{{ $detail->pasangkirim_id }}">
                            <td>
                                {{ ($produk->kategori->kategori_nama ?? '-') . ' — ' . ($produk->produk_nama ?? '-') }}
                                <input type="hidden" name="pasangkirim_id[]" value="{{ $detail->pasangkirim_id }}">
                            </td>
                            <td>
                                <input type="number" name="kuantitas[]" class="form-control qty no-arrow" readonly
                                    min="0" value="{{ $p->kuantitas ?? 1 }}">
                            </td>
                            <td>
                                <input type="text" class="form-control harga_display rupiah"
                                    value="{{ number_format((int) $detail->harga_satuan, 0, ',', '.') }}">
                                <input type="hidden" name="harga_satuan[]" class="harga"
                                    value="{{ (int) $detail->harga_satuan }}">
                            </td>
                            <td>
                                <input type="text" class="form-control total_display rupiah" readonly
                                    value="{{ number_format((int) $detail->total, 0, ',', '.') }}">
                                <input type="hidden" name="total[]" class="total" value="{{ (int) $detail->total }}">
                            </td>
                            <td>
                                <input type="number" name="diskon[]" class="form-control diskon no-arrow"
                                    step="0.01" min="0"
                                    value="{{ fmod($detail->diskon, 1) == 0 ? intval($detail->diskon) : $detail->diskon }}">
                            </td>
                            <td>
                                <input type="text" class="form-control grand_display rupiah" readonly
                                    value="{{ number_format((int) $detail->grand_total, 0, ',', '.') }}">
                                <input type="hidden" name="grand_total[]" class="grand_total"
                                    value="{{ (int) $detail->grand_total }}">
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
                <input type="date" name="tanggal_dp" class="form-control" value="{{ $invoice->tanggal_dp }}">

                <label class="mt-2">Tanggal Pelunasan</label>
                <input type="date" name="tanggal_pelunasan" class="form-control"
                    value="{{ $invoice->tanggal_pelunasan }}">
            </div>

            <!-- Kanan -->
            <div class="col-md-6">
                <label>Potongan Harga</label>
                <input type="text" id="potongan_display" class="form-control rupiah"
                    value="{{ number_format($invoice->potongan_harga, 0, ',', '.') }}">
                <input type="hidden" name="potongan_harga" id="potongan" value="{{ $invoice->potongan_harga }}">

                <label class="mt-2">Cashback</label>
                <input type="text" id="cashback_display" class="form-control rupiah"
                    value="{{ number_format($invoice->cashback, 0, ',', '.') }}">
                <input type="hidden" name="cashback" id="cashback" value="{{ $invoice->cashback }}">

                <label class="mt-2">DP</label>
                <input type="text" id="dp_display" class="form-control rupiah"
                    value="{{ number_format($invoice->dp, 0, ',', '.') }}">
                <input type="hidden" name="dp" id="dp" value="{{ $invoice->dp }}">

                <label class="mt-2">Sisa Pelunasan</label>
                <input type="text" id="sisa_display" class="form-control rupiah" readonly
                    value="{{ number_format($invoice->sisa_pelunasan, 0, ',', '.') }}">
                <input type="hidden" name="sisa_pelunasan" id="sisa" value="{{ $invoice->sisa_pelunasan }}">

                <label class="mt-2">Total Akhir</label>
                <input type="text" id="totalakhir_display" class="form-control rupiah" readonly
                    value="{{ number_format($invoice->total_akhir, 0, ',', '.') }}">
                <input type="hidden" name="total_akhir" id="totalakhir" value="{{ $invoice->total_akhir }}">

            </div>
        </div>

        <!-- Catatan -->
        <div class="row mt-3">
            <div class="col-12">
                <label>Catatan</label>
                <textarea name="catatan" class="form-control" rows="2">{{ $invoice->catatan }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
</form>

<script>
    // --- formatting helpers ---
    function formatRupiah(num) {
        if (num === null || num === undefined || num === '') return '';
        // pastikan integer
        num = parseInt(num) || 0;
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function parseRupiah(str) {
        if (!str && str !== 0) return 0;
        return parseInt(String(str).replace(/[^0-9]/g, '')) || 0;
    }

    // --- per-row calculations ---
    function hitungRow($row) {
        let qty = parseInt($row.find('input[name="kuantitas[]"]').val()) || 0;
        let harga = parseInt($row.find('input[name="harga_satuan[]"]').val()) || 0;
        let diskon = parseFloat($row.find('.diskon').val()) || 0;

        let total = qty * harga;
        let grand = Math.round(total - (total * (diskon / 100)));

        // set hidden + display
        $row.find('input.total').val(total);
        $row.find('.total_display').val(total ? formatRupiah(total) : '');

        $row.find('input.grand_total').val(grand);
        $row.find('.grand_display').val(grand ? formatRupiah(grand) : '');

        hitungSummary();
    }

    function hitungSummary() {
        let grandSum = 0;
        $('#tablePasang tbody tr').each(function() {
            grandSum += parseInt($(this).find('input.grand_total').val()) || 0;
        });

        let pot = parseRupiah($('#potongan_display').val() || '') || 0;
        let cash = parseRupiah($('#cashback_display').val() || '') || 0;
        let dpVal = parseRupiah($('#dp_display').val() || '') || 0;

        let totalAkhir = grandSum - pot - cash;
        let sisa = totalAkhir - dpVal;

        $('#totalakhir').val(totalAkhir);
        $('#totalakhir_display').val(formatRupiah(totalAkhir));

        $('#sisa').val(sisa);
        $('#sisa_display').val(formatRupiah(sisa));

        $('#potongan').val(pot);
        $('#cashback').val(cash);
        $('#dp').val(dpVal);
    }

    // --- events (use off() to avoid double-binding) ---
    $(document).off('input', '.harga_display').on('input', '.harga_display', function() {
        let $row = $(this).closest('tr');
        let v = parseRupiah($(this).val());
        // set hidden harga_satuan[]
        if ($row.find('input[name="harga_satuan[]"]').length) {
            $row.find('input[name="harga_satuan[]"]').val(v);
        } else if ($row.find('.harga').length) {
            $row.find('.harga').val(v);
        }
        // reformat display
        $(this).val(v ? formatRupiah(v) : '');
        hitungRow($row);
    });

    $(document).off('input', '.diskon').on('input', '.diskon', function() {
        // diskon change recalculates only
        let $row = $(this).closest('tr');
        hitungRow($row);
    });

    // potongan/cashback/dp display sync
    $(document).off('input', '#potongan_display').on('input', '#potongan_display', function() {
        let v = parseRupiah($(this).val());
        $('#potongan').val(v);
        $(this).val(v ? formatRupiah(v) : '');
        hitungSummary();
    });
    $(document).off('input', '#cashback_display').on('input', '#cashback_display', function() {
        let v = parseRupiah($(this).val());
        $('#cashback').val(v);
        $(this).val(v ? formatRupiah(v) : '');
        hitungSummary();
    });
    $(document).off('input', '#dp_display').on('input', '#dp_display', function() {
        let v = parseRupiah($(this).val());
        $('#dp').val(v);
        $(this).val(v ? formatRupiah(v) : '');
        hitungSummary();
    });

    // Convert display fields to hidden numeric values before submit
    function syncDisplayToHidden() {
        $('#tablePasang tbody tr').each(function() {
            let $row = $(this);

            // harga: ensure harga_satuan[] hidden has numeric
            let hargaVal = 0;
            if ($row.find('.harga_display').length) hargaVal = parseRupiah($row.find('.harga_display').val());
            if ($row.find('input[name="harga_satuan[]"]').length) {
                $row.find('input[name="harga_satuan[]"]').val(hargaVal);
            } else if ($row.find('.harga').length) {
                $row.find('.harga').val(hargaVal);
            }

            // totals & grand totals
            let totalVal = parseRupiah($row.find('.total_display').val() || $row.find('.total').val());
            if ($row.find('input[name="total[]"]').length) $row.find('input[name="total[]"]').val(totalVal);
            let grandVal = parseRupiah($row.find('.grand_display').val() || $row.find('.grand_total').val());
            if ($row.find('input[name="grand_total[]"]').length) $row.find('input[name="grand_total[]"]').val(
                grandVal);
        });

        // fallback for pot/cash/dp
        if (!$('#potongan').val()) $('#potongan').val(parseRupiah($('#potongan_display').val()));
        if (!$('#cashback').val()) $('#cashback').val(parseRupiah($('#cashback_display').val()));
        if (!$('#dp').val()) $('#dp').val(parseRupiah($('#dp_display').val()));
    }

    // initialize formatting + initial calculations
    // initialize formatting + initial calculations
    $(function() {
        $('#tablePasang tbody tr').each(function() {
            let $r = $(this);

            // if harga_satuan[] exists and unformatted, format display
            if ($r.find('input[name="harga_satuan[]"]').length && $r.find(
                    'input[name="harga_satuan[]"]').val()) {
                let hv = parseRupiah($r.find('input[name="harga_satuan[]"]').val());
                $r.find('.harga_display').val(hv ? formatRupiah(hv) : '');
                $r.find('input[name="harga_satuan[]"]').val(hv);
            }

            // ensure hidden inputs have classes for script to find
            if (!$r.find('input.total').length) $r.find('input[name="total[]"]').addClass('total');
            if (!$r.find('input.grand_total').length) $r.find('input[name="grand_total[]"]').addClass(
                'grand_total');

            hitungRow($r);
        });

        // ⬇️ Tambahkan bagian ini untuk potongan/cashback/dp
        let pot = parseRupiah($('#potongan_display').val());
        $('#potongan_display').val(formatRupiah(pot));

        let cash = parseRupiah($('#cashback_display').val());
        $('#cashback_display').val(formatRupiah(cash));

        let dpVal = parseRupiah($('#dp_display').val());
        $('#dp_display').val(formatRupiah(dpVal));

        let sisaVal = parseRupiah($('#sisa_display').val());
        $('#sisa_display').val(formatRupiah(sisaVal));

        let totalVal = parseRupiah($('#totalakhir_display').val());
        $('#totalakhir_display').val(formatRupiah(totalVal));

        hitungSummary();
    });

    // --- single submit handler for edit form ---
    $(document).off('submit', '#form-edit-invoice').on('submit', '#form-edit-invoice', function(e) {
        e.preventDefault();

        // sync displays to hidden numeric
        syncDisplayToHidden();

        let form = $(this);
        let formData = new FormData(this);
        formData.append('_method', 'PUT'); // method spoofing

        $.ajax({
            url: form.attr("action"),
            type: "POST", // tetap POST karena kita pakai _method=PUT
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                // tampilkan notifikasi, tutup modal, lalu redirect ke index invoice list
                try {
                    toastr.success(res.message || "Invoice berhasil disimpan");
                } catch (err) {}
                $("#crudModal").modal("hide");

                // beri jeda kecil agar modal tertutup halus
                setTimeout(function() {
                    window.location.href = "{{ route('datainvoice.index') }}";
                }, 300);
            },
            error: function(xhr) {
                let msg = "Terjadi kesalahan server.";
                try {
                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON
                        .message;
                } catch (e) {}
                Swal.fire("Gagal", msg, "error");
                console.error("Server Error:", xhr.responseText);
            }
        });
    });
</script>
