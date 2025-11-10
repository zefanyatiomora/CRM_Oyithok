<!-- resources/views/rekap/edit_invoice.blade.php -->
<div class="modal-header bg-wallpaper-gradient text-white">
    <h5 class="modal-title">Edit Invoice</h5>
</div>
<form id="form-edit-invoice" action="{{ route('invoice.update', $invoice->invoice_id) }}" method="POST">
    @csrf
    @method('PUT')

    <input type="hidden" id="editing_invoice_id" value="{{ $invoice->invoice_id }}">

    <div class="modal-body">
        <!-- Nomor & Customer -->
        <div class="row mb-2">
            <div class="col-md-6">
                <label>Nomor Invoice</label>
                <input type="text" id="nomor_invoice" name="nomor_invoice" class="form-control"
                    value="{{ $invoice->nomor_invoice }}">
                <div id="nomor_suggestion" class="suggestion-bar" role="button" tabindex="0" aria-hidden="true">
                    <small>Terakhir: <span id="nomor_suggestion_text"></span></small>
                </div>
                <div id="nomor_inline_warning" class="inline-warning">Nomor ini sama dengan invoice yang sudah ada.
                    Mohon ubah nomor agar tidak duplikat.</div>
            </div>

            <div class="col-md-6">
                <label>Customer ID</label>
                <input type="text" id="customer_invoice" name="customer_invoice" class="form-control"
                    value="{{ $invoice->customer_invoice ?? ($interaksi->customer->customer_nama ?? '') }}">
                <div id="customer_suggestion" class="suggestion-bar" role="button" tabindex="0" aria-hidden="true">
                    <small>Terakhir: <span id="customer_suggestion_text"></span></small>
                </div>
                <div id="customer_inline_warning" class="inline-warning">Customer ini sama dengan customer pada invoice
                    lain. Silakan ubah.</div>
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
                                <input type="number" name="kuantitas[]" class="form-control qty no-arrow text-center"
                                    readonly min="0" value="{{ $p->kuantitas ?? 1 }}">
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
                                <input type="number" name="diskon[]" class="form-control diskon no-arrow text-center"
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

                <!-- FOOTER: total produk (jumlah grand_total semua produk) -->
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right font-weight-bold">Jumlah grand total semua produk</td>
                        <td>
                            <div class="input-group">
                                <input type="text" id="total_produk_display" class="form-control rupiah" readonly
                                    value="{{ isset($invoice->total_produk) ? number_format((int) $invoice->total_produk, 0, ',', '.') : '' }}">
                                <input type="hidden" name="total_produk" id="total_produk"
                                    value="{{ $invoice->total_produk ?? 0 }}">
                            </div>
                        </td>
                    </tr>
                </tfoot>
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
                <!-- PPN: persen + nominal display + hidden nominal -->
                <label>PPN</label>
                <div class="input-group mb-2">
                    <input type="number" id="ppn" name="ppn" class="form-control no-arrow text-center"
                        min="0" step="0.01"
                        value="{{ isset($invoice->ppn) ? (intval($invoice->ppn) == $invoice->ppn ? intval($invoice->ppn) : $invoice->ppn) : '' }}">
                    <div class="input-group-append"><span class="input-group-text">%</span></div>

                    <input type="text" id="ppn_nominal_display" class="form-control rupiah ml-2"
                        placeholder="Nominal PPN"
                        value="{{ isset($invoice->nominal_ppn) ? number_format($invoice->nominal_ppn, 0, ',', '.') : '' }}">
                    <input type="hidden" name="nominal_ppn" id="ppn_nominal"
                        value="{{ isset($invoice->nominal_ppn) ? (int) $invoice->nominal_ppn : 0 }}">
                </div>

                <label>Potongan Harga</label>
                <input type="text" id="potongan_display" class="form-control rupiah"
                    value="{{ number_format($invoice->potongan_harga ?? 0, 0, ',', '.') }}">
                <input type="hidden" name="potongan_harga" id="potongan"
                    value="{{ $invoice->potongan_harga ?? 0 }}">

                <label class="mt-2">Cashback</label>
                <input type="text" id="cashback_display" class="form-control rupiah"
                    value="{{ number_format($invoice->cashback ?? 0, 0, ',', '.') }}">
                <input type="hidden" name="cashback" id="cashback" value="{{ $invoice->cashback ?? 0 }}">

                <label class="mt-2">DP</label>
                <input type="text" id="dp_display" class="form-control rupiah"
                    value="{{ number_format($invoice->dp ?? 0, 0, ',', '.') }}">
                <input type="hidden" name="dp" id="dp" value="{{ $invoice->dp ?? 0 }}">

                <label class="mt-2">Sisa Pelunasan</label>
                <input type="text" id="sisa_display" class="form-control rupiah" readonly
                    value="{{ number_format($invoice->sisa_pelunasan ?? 0, 0, ',', '.') }}">
                <input type="hidden" name="sisa_pelunasan" id="sisa"
                    value="{{ $invoice->sisa_pelunasan ?? 0 }}">

                <label class="mt-2">Total Akhir</label>
                <input type="text" id="totalakhir_display" class="form-control rupiah" readonly
                    value="{{ number_format($invoice->total_akhir ?? 0, 0, ',', '.') }}">
                <input type="hidden" name="total_akhir" id="totalakhir" value="{{ $invoice->total_akhir ?? 0 }}">
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
        </div>
</form>
<style>
/* ========================
   MODAL EDIT INVOICE STYLE
======================== */

/* Ukuran & posisi modal agar pas */
#crudModal .modal-dialog {
    max-width: 850px; /* tidak terlalu lebar */
    margin: 1.75rem auto;
}

.modal-content {
    border: none;
    border-radius: 0.6rem;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 6px 25px rgba(0,0,0,0.15);
}

/* Header gradient */
.modal-header.bg-wallpaper-gradient {
    background: linear-gradient(90deg, #7b2ff7, #f107a3);
    color: white;
    padding: 12px 20px;
    border-bottom: none;
}

/* Judul & tombol close */
.modal-header .modal-title {
    font-size: 1.05rem;
    font-weight: 600;
}
.modal-header .close {
    color: white;
    opacity: 0.9;
    font-size: 1.3rem;
}

/* Body padding rapi */
.modal-body {
    padding: 20px 25px;
}

/* Footer rapi */
.modal-footer {
    border-top: 1px solid #eee;
    padding: 12px 25px;
}

/* Form elemen spacing */
#crudModal .form-group,
#crudModal .row.mb-2 {
    margin-bottom: 15px;
}

/* Table responsif rapi */
#crudModal table.table {
    margin-bottom: 10px;
    font-size: 0.92rem;
}

/* Input lebih ringkas */
#crudModal input.form-control,
#crudModal select.form-control {
    height: 35px;
    font-size: 0.9rem;
}

/* Inline warning kecil dan rapi */
.inline-warning {
    font-size: 0.8rem;
    color: #e74c3c;
    margin-top: 2px;
    display: none;
}
#crudModal .modal-dialog {
    max-width: 850px;
    margin: 1.75rem auto;
}
#crudModal .modal-content {
    border: none;
    border-radius: 0.6rem;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 6px 25px rgba(0,0,0,0.15);
}
#crudModal .modal-body {
    padding: 20px 25px;
}

/* Judul di modal utama */
#crudModal .modal-header.bg-wallpaper-gradient {
    background: linear-gradient(90deg, #7b2ff7, #f107a3);
    color: #fff;
    padding: 12px 20px;
    border-bottom: none;
}
</style>
{{-- inject lastInvoice ke javascript (dipakai untuk suggestion/quick client-side check) --}}
<script>
    // lastInvoice berasal dari controller; null jika tidak ada
    let lastInvoice = {!! json_encode($lastInvoice ?? null) !!} || null;
</script>

<script>
    /* === Helpers === */
    function formatRupiah(num) {
        if (num === null || num === undefined || num === '') return '';
        num = parseInt(num) || 0;
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function parseRupiah(str) {
        if (!str && str !== 0) return 0;
        return parseInt(String(str).replace(/[^0-9]/g, '')) || 0;
    }

    /* === Row & Summary (with PPN + total_produk) === */
    function hitungRow($row) {
        let qty = parseInt($row.find('input[name="kuantitas[]"]').val()) || 0;
        let harga = parseInt($row.find('input[name="harga_satuan[]"]').val()) || 0;
        let diskonRaw = $row.find('.diskon').val();
        let diskon = (diskonRaw === '' || typeof diskonRaw === 'undefined') ? 0 : parseFloat(diskonRaw) || 0;

        let total = qty * harga;
        let grand = Math.round(total - Math.round(total * (diskon / 100)));

        // write to hidden + display (ensure selectors exist)
        if ($row.find('input[name="total[]"]').length) $row.find('input[name="total[]"]').val(total);
        if ($row.find('.total_display').length) $row.find('.total_display').val(total ? formatRupiah(total) : '');

        if ($row.find('input[name="grand_total[]"]').length) $row.find('input[name="grand_total[]"]').val(grand);
        if ($row.find('.grand_display').length) $row.find('.grand_display').val(grand ? formatRupiah(grand) : '');

        hitungSummary();
    }

    function hitungSummary() {
        let grandSum = 0;
        $('#tablePasang tbody tr').each(function() {
            grandSum += parseInt($(this).find('input[name="grand_total[]"]').val()) || 0;
        });

        // total_produk = jumlah semua grand_total
        $('#total_produk').val(grandSum);
        $('#total_produk_display').val(grandSum ? formatRupiah(grandSum) : '');

        let pot = parseRupiah($('#potongan_display').val() || '') || 0;
        let cash = parseRupiah($('#cashback_display').val() || '') || 0;
        let dpVal = parseRupiah($('#dp_display').val() || '') || 0;

        // total akhir: gross + ppn - pot - cash
        let totalAkhir = (grandSum + nominalPpn) - pot - cash;
        if (totalAkhir < 0) totalAkhir = 0;

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
    // harga input
    $(document).off('input', '.harga_display').on('input', '.harga_display', function() {
        let $row = $(this).closest('tr');
        let v = parseRupiah($(this).val());
        if ($row.find('input[name="harga_satuan[]"]').length) {
            $row.find('input[name="harga_satuan[]"]').val(v);
        } else if ($row.find('.harga').length) {
            $row.find('.harga').val(v);
        }
        $(this).val(v ? formatRupiah(v) : '');
        hitungRow($row);
    });

    // qty / diskon
    $(document).off('input', '.diskon').on('input', '.diskon', function() {
        let $row = $(this).closest('tr');
        hitungRow($row);
    });

    // potongan/cashback/dp sync
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

    // ppn percent change
    $(document).off('input', '#ppn').on('input', '#ppn', function() {
        hitungSummary();
    });

    // suggestion & inline warning (like create)
    $(document).ready(function() {
        // init rows
        $('#tablePasang tbody tr').each(function() {
            let $r = $(this);
            if ($r.find('input[name="harga_satuan[]"]').length && $r.find(
                    'input[name="harga_satuan[]"]').val()) {
                let hv = parseRupiah($r.find('input[name="harga_satuan[]"]').val());
                $r.find('.harga_display').val(hv ? formatRupiah(hv) : '');
                $r.find('input[name="harga_satuan[]"]').val(hv);
            }

            // ensure class presence for totals
            if (!$r.find('input[name="total[]"]').hasClass('total')) $r.find('input[name="total[]"]')
                .addClass('total');
            if (!$r.find('input[name="grand_total[]"]').hasClass('grand_total')) $r.find(
                'input[name="grand_total[]"]').addClass('grand_total');

            hitungRow($r);
        });

        // init pot/cash/dp/ppn displays from server values
        let pot = parseRupiah($('#potongan').val() || $('#potongan_display').val());
        $('#potongan_display').val(pot ? formatRupiah(pot) : '');

        let cash = parseRupiah($('#cashback').val() || $('#cashback_display').val());
        $('#cashback_display').val(cash ? formatRupiah(cash) : '');

        let dpVal = parseRupiah($('#dp').val() || $('#dp_display').val());
        $('#dp_display').val(dpVal ? formatRupiah(dpVal) : '');

        // init total_produk display from server value (if provided)
        let totalProdukInit = parseInt($('#total_produk').val() || 0);
        $('#total_produk_display').val(totalProdukInit ? formatRupiah(totalProdukInit) : '');

        // ppn init: if server provided nominal_ppn, format
        let ppnNominalInit = parseInt($('#ppn_nominal').val() || 0);
        $('#ppn_nominal_display').val(ppnNominalInit ? formatRupiah(ppnNominalInit) : '');

        hitungSummary();

        // suggestion behavior + inline warning for nomor/customer
        $('#nomor_invoice').on('focus input', function() {
            let v = ($(this).val() || '').trim();
            if (lastInvoice && lastInvoice.nomor_invoice) {
                $('#nomor_suggestion_text').text(lastInvoice.nomor_invoice);
                $('#nomor_suggestion').show().attr('aria-hidden', 'false');
                if (v !== '' && v === String(lastInvoice.nomor_invoice)) $('#nomor_inline_warning')
                    .show();
                else $('#nomor_inline_warning').hide();
            }
        }).on('blur', function() {
            setTimeout(function() {
                $('#nomor_suggestion').hide().attr('aria-hidden', 'true');
            }, 200);
            setTimeout(function() {
                $('#nomor_inline_warning').hide();
            }, 200);
        });
        $('#nomor_suggestion').on('click keypress', function(e) {
            if (e.type === 'click' || (e.type === 'keypress' && (e.key === 'Enter' || e.key === ' '))) {
                if (!lastInvoice) return;
                $('#nomor_invoice').val(lastInvoice.nomor_invoice).trigger('input').focus();
                $('#nomor_suggestion').hide().attr('aria-hidden', 'true');
            }
        });

        $('#customer_invoice').on('focus input', function() {
            let v = ($(this).val() || '').trim();
            if (lastInvoice && lastInvoice.customer_invoice) {
                $('#customer_suggestion_text').text(lastInvoice.customer_invoice);
                $('#customer_suggestion').show().attr('aria-hidden', 'false');
                if (v !== '' && v === String(lastInvoice.customer_invoice)) $(
                    '#customer_inline_warning').show();
                else $('#customer_inline_warning').hide();
            }
        }).on('blur', function() {
            setTimeout(function() {
                $('#customer_suggestion').hide().attr('aria-hidden', 'true');
            }, 200);
            setTimeout(function() {
                $('#customer_inline_warning').hide();
            }, 200);
        });
        $('#customer_suggestion').on('click keypress', function(e) {
            if (e.type === 'click' || (e.type === 'keypress' && (e.key === 'Enter' || e.key === ' '))) {
                if (!lastInvoice) return;
                $('#customer_invoice').val(lastInvoice.customer_invoice).trigger('input').focus();
                $('#customer_suggestion').hide().attr('aria-hidden', 'true');
            }
        });
    });

    // Convert display fields to hidden numeric values before submit
    function syncDisplayToHidden() {
        $('#tablePasang tbody tr').each(function() {
            let $row = $(this);
            let hargaVal = 0;
            if ($row.find('.harga_display').length) hargaVal = parseRupiah($row.find('.harga_display').val());
            if ($row.find('input[name="harga_satuan[]"]').length) $row.find('input[name="harga_satuan[]"]').val(
                hargaVal);
            else if ($row.find('.harga').length) $row.find('.harga').val(hargaVal);

            let totalVal = parseInt($row.find('input[name="total[]"]').val()) || 0;
            if ($row.find('input[name="total[]"]').length) $row.find('input[name="total[]"]').val(totalVal);

            let grandVal = parseInt($row.find('input[name="grand_total[]"]').val()) || 0;
            if ($row.find('input[name="grand_total[]"]').length) $row.find('input[name="grand_total[]"]').val(
                grandVal);
        });

        // ensure pot/cash/dp values synced
        if (!$('#potongan').val()) $('#potongan').val(parseRupiah($('#potongan_display').val()));
        if (!$('#cashback').val()) $('#cashback').val(parseRupiah($('#cashback_display').val()));
        if (!$('#dp').val()) $('#dp').val(parseRupiah($('#dp_display').val()));

        // ensure ppn hidden present
        if (!$('#ppn_nominal').val()) $('#ppn_nominal').val(parseRupiah($('#ppn_nominal_display').val()));

        // ensure total_produk is synced (recalculate as fallback)
        if (!$('#total_produk').val() || $('#total_produk').val() == '0') {
            let computed = 0;
            $('#tablePasang tbody tr').each(function() {
                computed += parseInt($(this).find('input[name="grand_total[]"]').val()) || 0;
            });
            $('#total_produk').val(computed);
        }
    }

    // submit handler for edit form (AJAX)
    $(document).off('submit', '#form-edit-invoice').on('submit', '#form-edit-invoice', function(e) {
        e.preventDefault();

        // quick client-side check against lastInvoice: if equals lastInvoice, block and ask to change
        let nomorVal = ($('#nomor_invoice').val() || '').trim();
        let customerVal = ($('#customer_invoice').val() || '').trim();
        if (lastInvoice) {
            // allow same as current invoice being edited (we will exclude server-side by invoice id)
            let editingId = $('#editing_invoice_id').val();
            // if lastInvoice refers to another invoice (different id) and matches -> block
            if (lastInvoice.invoice_id && String(lastInvoice.invoice_id) !== String(editingId)) {
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
                        alert('Nomor invoice sama dengan invoice terakhir. Silakan ubah nomor invoice.');
                        $('#nomor_invoice').focus().select();
                    }
                    return;
                }
                if (customerVal !== '' && lastInvoice.customer_invoice && customerVal === String(lastInvoice
                        .customer_invoice)) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Duplikat Customer Invoice',
                            html: 'Customer invoice sama dengan invoice terakhir. Silakan ubah customer invoice.',
                            icon: 'warning',
                            confirmButtonText: 'Ubah Customer'
                        }).then(() => {
                            $('#customer_invoice').focus().select();
                        });
                    } else {
                        alert('Customer invoice sama dengan invoice terakhir. Silakan ubah customer invoice.');
                        $('#customer_invoice').focus().select();
                    }
                    return;
                }
            }
        }

        // sync values and send
        syncDisplayToHidden();

        let form = $(this);
        let formData = new FormData(this);
        formData.append('_method', 'PUT');

        // ensure total_produk and nominal_ppn/ppn included
        if (!formData.has('total_produk')) formData.append('total_produk', $('#total_produk').val() || 0);
        if (!formData.has('nominal_ppn')) formData.append('nominal_ppn', $('#ppn_nominal').val() || 0);
        if (!formData.has('ppn')) formData.append('ppn', $('#ppn').val() || '');

        $.ajax({
            url: form.attr('action'),
            type: 'POST', // method spoof PUT via _method
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                try {
                    if (typeof toastr !== 'undefined') toastr.success(res.message ||
                        'Invoice berhasil disimpan');
                } catch (e) {}
                $('#crudModal').modal('hide');
                setTimeout(function() {
                    window.location.href = "{{ route('datainvoice.index') }}";
                }, 300);
            },
            error: function(xhr) {
                let handled = false;
                if (xhr.responseJSON) {
                    let json = xhr.responseJSON;
                    if (json.errors) {
                        // show validation messages and focus first field
                        let messages = [];
                        for (let k in json.errors) {
                            if (json.errors.hasOwnProperty(k)) messages.push(json.errors[k].join(
                                ', '));
                        }
                        let msg = messages.join('<br>');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi',
                                html: msg
                            }).then(() => {
                                if (json.errors.nomor_invoice) $('#nomor_invoice').focus()
                                    .select();
                                else if (json.errors.customer_invoice) $(
                                    '#customer_invoice').focus().select();
                            });
                        } else {
                            alert(msg.replace(/<br>/g, '\n'));
                            if (json.errors.nomor_invoice) $('#nomor_invoice').focus().select();
                            else if (json.errors.customer_invoice) $('#customer_invoice').focus()
                                .select();
                        }
                        handled = true;
                    } else if (json.message) {
                        if (typeof Swal !== 'undefined') Swal.fire('Error', json.message, 'error');
                        else alert(json.message);
                        handled = true;
                    }
                }
                if (!handled) {
                    if (typeof Swal !== 'undefined') Swal.fire('Gagal', 'Terjadi kesalahan server.',
                        'error');
                    else alert('Terjadi kesalahan server.');
                }
                console.error('Server Error:', xhr.responseText);
            }
        });
    });
</script>
