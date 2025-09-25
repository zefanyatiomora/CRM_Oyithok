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
                        {{ $invoice->batas_pelunasan == 'H-1 sebelum kirim' ? 'selected' : '' }}>H-1 sebelum kirim</option>
                </select>
            </div>
        </div>

        <!-- Table Pasang/Kirim -->
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="tablePasang">
                <thead>
                    <tr>
                        <th>Pasang/Kirim</th>
                        <th>Kuantitas</th>
                        <th>Harga Satuan</th>
                        <th>Total</th>
                        <th>Diskon (%)</th>
                        <th>Grand Total</th>
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
                                {{ $produk->produk_nama ?? '-' }} — {{ $p->jadwal_pasang_kirim ?? '-' }}
                                <input type="hidden" name="pasangkirim_id[]" value="{{ $detail->pasangkirim_id }}">
                            </td>
                            <td>
                                <input type="number" name="kuantitas[]" class="form-control qty" min="0"
                                    value="{{ $p->kuantitas ?? 1 }}">
                            </td>
                            <td>
                                <input type="text" class="form-control harga_display rupiah"
                                    value="{{ $detail->harga_satuan }}">
                                <input type="hidden" name="harga_satuan[]" class="harga"
                                    value="{{ $detail->harga_satuan }}">
                            </td>
                            <td>
                                <input type="text" class="form-control total_display rupiah" readonly
                                    value="{{ $detail->total }}">
                                <input type="hidden" name="total[]" class="total" value="{{ $detail->total }}">
                            </td>
                            <td>
                                <input type="number" name="diskon[]" class="form-control diskon" step="0.01"
                                    min="0" value="{{ $detail->diskon }}">
                            </td>
                            <td>
                                <input type="text" class="form-control grand_display rupiah" readonly
                                    value="{{ $detail->grand_total }}">
                                <input type="hidden" name="grand_total[]" class="grand_total"
                                    value="{{ $detail->grand_total }}">
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
                    value="{{ $invoice->potongan_harga }}">
                <input type="hidden" name="potongan_harga" id="potongan" value="{{ $invoice->potongan_harga }}">

                <label class="mt-2">Cashback</label>
                <input type="text" id="cashback_display" class="form-control rupiah"
                    value="{{ $invoice->cashback }}">
                <input type="hidden" name="cashback" id="cashback" value="{{ $invoice->cashback }}">

                <label class="mt-2">DP</label>
                <input type="text" id="dp_display" class="form-control rupiah" value="{{ $invoice->dp }}">
                <input type="hidden" name="dp" id="dp" value="{{ $invoice->dp }}">

                <label class="mt-2">Sisa Pelunasan</label>
                <input type="text" id="sisa_display" class="form-control rupiah" readonly
                    value="{{ $invoice->sisa_pelunasan }}">
                <input type="hidden" name="sisa_pelunasan" id="sisa" value="{{ $invoice->sisa_pelunasan }}">

                <label class="mt-2">Total Akhir</label>
                <input type="text" id="totalakhir_display" class="form-control rupiah" readonly
                    value="{{ $invoice->total_akhir }}">
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
    // formatting helpers
    function formatRupiah(num) {
        if (!num && num !== 0) return '';
        num = parseInt(num) || 0;
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function parseRupiah(str) {
        if (!str) return 0;
        return parseInt(String(str).replace(/[^0-9]/g, '')) || 0;
    }

    // Hitung row (qty, harga(hidden), diskon) => total, grand
    function hitungRow($row) {
        let qty = parseInt($row.find('.qty').val()) || 0;
        let harga = parseInt($row.find('.harga').val()) || 0; // hidden numeric
        let diskon = parseFloat($row.find('.diskon').val()) || 0;

        let total = qty * harga;
        let grand = Math.round(total - (total * (diskon / 100)));

        // set hidden + display
        $row.find('.total').val(total);
        $row.find('.total_display').val(formatRupiah(total));

        $row.find('.grand_total').val(grand);
        $row.find('.grand_display').val(formatRupiah(grand));

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

        let totalAkhir = grandSum - pot - cash;
        let sisa = totalAkhir - dpVal;

        // isi hidden + tampil
        $('#totalakhir').val(totalAkhir);
        $('#totalakhir_display').val(formatRupiah(totalAkhir));

        $('#sisa').val(sisa);
        $('#sisa_display').val(formatRupiah(sisa));

        // isi hidden pot/cash/dp
        $('#potongan').val(pot);
        $('#cashback').val(cash);
        $('#dp').val(dpVal);
    }


    // --- EVENTS ---

    // When user types in harga display -> update hidden .harga and recalc
    $(document).on('input', '.harga_display', function() {
        let $row = $(this).closest('tr');
        let v = parseRupiah($(this).val());
        // set hidden harga (if you use .harga hidden input)
        // But our markup uses hidden input .harga — ensure it's present
        if ($row.find('.harga').length) {
            $row.find('.harga').val(v);
        } else {
            // If using visible named harga_satuan[] directly, set that value:
            $row.find('input[name="harga_satuan[]"]').val(v);
        }
        // reformat display to Rupiah
        $(this).val(v ? formatRupiah(v) : '');
        hitungRow($row);
    });

    // qty or diskon change
    $(document).on('input', '.qty, .diskon', function() {
        let $row = $(this).closest('tr');
        hitungRow($row);
    });

    // manual fields potongan, cashback, dp: keep display + hidden synchronized
    $('#potongan_display').on('input', function() {
        let v = parseRupiah($(this).val());
        $('#potongan').val(v);
        $(this).val(v ? formatRupiah(v) : '');
        hitungSummary();
    });
    $('#cashback_display').on('input', function() {
        let v = parseRupiah($(this).val());
        $('#cashback').val(v);
        $(this).val(v ? formatRupiah(v) : '');
        hitungSummary();
    });
    $('#dp_display').on('input', function() {
        let v = parseRupiah($(this).val());
        $('#dp').val(v);
        $(this).val(v ? formatRupiah(v) : '');
        hitungSummary();
    });

    // on form submit: ensure all display inputs converted to hidden numeric values
    $('#form-create-invoice').on('submit', function(e) {
        // convert any remaining display rupiah inputs to numeric hidden before submit
        // for every row:
        $('#tablePasang tbody tr').each(function() {
            let $row = $(this);
            // harga: if display exists, ensure hidden exists or value assigned to name input
            let hargaVal = 0;
            if ($row.find('.harga_display').length) hargaVal = parseRupiah($row.find('.harga_display')
                .val());
            // set the hidden input that controller expects: input[name="harga_satuan[]"]
            if ($row.find('input[name="harga_satuan[]"]').length) {
                $row.find('input[name="harga_satuan[]"]').val(hargaVal);
            } else if ($row.find('.harga').length) {
                $row.find('.harga').val(hargaVal);
            }
            // totals & grand totals: ensure hidden present
            let totalVal = parseRupiah($row.find('.total_display').val() || $row.find('.total').val());
            if ($row.find('input[name="total[]"]').length) $row.find('input[name="total[]"]').val(
                totalVal);
            if ($row.find('input[name="grand_total[]"]').length) $row.find(
                'input[name="grand_total[]"]').val(parseRupiah($row.find('.grand_display').val()) ||
                $row.find('.grand_total').val());
        });

        // potongan/cash/dp already have hidden ids set by events; ensure fallback:
        if (!$('#potongan').val()) $('#potongan').val(parseRupiah($('#potongan_display').val()));
        if (!$('#cashback').val()) $('#cashback').val(parseRupiah($('#cashback_display').val()));
        if (!$('#dp').val()) $('#dp').val(parseRupiah($('#dp_display').val()));

        // allow submit to continue
    });

    // initialize: format existing blanks and calc
    $(document).ready(function() {
        $('#tablePasang tbody tr').each(function() {
            let $r = $(this);
            // if there is an existing harga input with value (unformatted), format it
            if ($r.find('input[name="harga_satuan[]"]').length && $r.find(
                    'input[name="harga_satuan[]"]').val()) {
                let hv = parseRupiah($r.find('input[name="harga_satuan[]"]').val());
                $r.find('.harga_display').val(hv ? formatRupiah(hv) : '');
                $r.find('.harga').val(hv);
            }
            // trigger initial calc
            hitungRow($r);
        });
        hitungSummary();
        $(document).ready(function() {
            $("#form-edit-invoice").on("submit", function(e) {
                e.preventDefault();

                let form = $(this);
                let formData = new FormData(this);
                formData.append('_method', 'PUT'); // method spoofing

                $.ajax({
                    url: form.attr("action"),
                    type: "POST", // HARUS POST, bukan PUT
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        toastr.success("Invoice berhasil disimpan");
                        tableRekap.ajax.reload(null, false);
                        let interaksiId = $("#interaksi_id").val();
                        $("#myModal").load("{{ url('rekap') }}/" + interaksiId +
                            "/show_ajax");
                        $("#crudModal").modal("hide");
                    },
                    error: function(xhr) {
                        Swal.fire("Gagal", "Terjadi kesalahan server.", "error");
                        console.error("Server Error:", xhr.responseText);
                    }
                });
            });
        });
    });
</script>
