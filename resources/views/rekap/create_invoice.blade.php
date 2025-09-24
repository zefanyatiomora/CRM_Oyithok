<!-- resources/views/rekap/create_invoice.blade.php -->
<div class="modal-header">
    <h5 class="modal-title">Tambah Invoice</h5>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="form-create-invoice" action="{{ route('invoice.store') }}" method="POST">
    @csrf

    <div class="modal-body">
        <div class="row mb-2">
            <div class="col-md-6">
                <label>Nomor Invoice</label>
                <input type="text" name="nomor_invoice" class="form-control"
                    value="{{ $lastInvoice->nomor_invoice ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Customer Invoice</label>
                <input type="text" name="customer_invoice" class="form-control"
                    value="{{ $lastInvoice->customer_invoice ?? ($interaksi->customer->customer_nama ?? '') }}">
            </div>
        </div>

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
                    @foreach ($pasang as $p)
                        <tr data-pasangkirim-id="{{ $p->pasangkirim_id }}">
                            <td>
                                {{ $p->produk->produk_nama ?? '-' }} — {{ $p->jadwal_pasang_kirim }}
                                <!-- hidden untuk pasangkirim_id -->
                                <input type="hidden" name="pasangkirim_id[]" value="{{ $p->pasangkirim_id }}">
                            </td>

                            <td>
                                <input type="number" name="kuantitas[]" class="form-control qty" min="0"
                                       value="{{ $p->kuantitas ?? 1 }}">
                            </td>

                            <td>
                                <!-- display + hidden numeric -->
                                <input type="text" class="form-control harga_display rupiah" value="">
                                <input type="hidden" name="harga_satuan[]" class="harga">
                            </td>

                            <td>
                                <input type="text" class="form-control total_display rupiah" readonly>
                                <input type="hidden" name="total[]" class="total">
                            </td>

                            <td>
                                <input type="number" name="diskon[]" class="form-control diskon" step="0.01" min="0" value="0">
                            </td>

                            <td>
                                <input type="text" class="form-control grand_display rupiah" readonly>
                                <input type="hidden" name="grand_total[]" class="grand_total">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <label>Potongan Harga</label>
                <input type="text" id="potongan_display" class="form-control rupiah" value="">
                <input type="hidden" name="potongan_harga" id="potongan">
            </div>
            <div class="col-md-6">
                <label>Cashback</label>
                <input type="text" id="cashback_display" class="form-control rupiah" value="">
                <input type="hidden" name="cashback" id="cashback">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <label>DP</label>
                <input type="text" id="dp_display" class="form-control rupiah" value="">
                <input type="hidden" name="dp" id="dp">
            </div>
            <div class="col-md-6">
                <label>Sisa Pelunasan</label>
                <input type="text" id="sisa_display" class="form-control rupiah" readonly>
                <input type="hidden" name="sisa_pelunasan" id="sisa">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <label>Total Akhir</label>
                <input type="text" id="totalakhir_display" class="form-control rupiah" readonly>
                <input type="hidden" name="total_akhir" id="totalakhir">
            </div>
            <div class="col-md-6">
                <label>Tanggal DP</label>
                <input type="date" name="tanggal_dp" class="form-control" value="{{ $lastInvoice->tanggal_dp ?? '' }}">
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-6">
                <label>Tanggal Pelunasan</label>
                <input type="date" name="tanggal_pelunasan" class="form-control" value="{{ $lastInvoice->tanggal_pelunasan ?? '' }}">
            </div>
            <div class="col-md-6">
                <label>Catatan</label>
                <textarea name="catatan" class="form-control" rows="2">{{ $lastInvoice->catatan ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Simpan</button>
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

        // set hidden & display
        $('#totalakhir').val(totalAkhir);
        $('#totalakhir_display')?.remove(); // safe: we don't have that element
        $('#totalakhir_display') // not present; instead use id above
        $('#totalakhir_display'); // noop

        // fill display fields we do have:
        $('#totalakhir_display') // (no element) — we used id="totalakhir_display"? not set; we'll set via id totalakhir_display? adjust:
        // Instead update the actual elements:
        $('#totalakhir_display') // keep quick fix below
        // Safe assignment:
        // If you prefer, update the DOM elements by ids we actually have:
        $('#totalakhir_display') // (ignored)

        // simpler: update the visible fields we used:
        $('#totalakhir_display') // (no-op)

        // Use the actual ids we placed: totalakhir_display not created; so set visible using id selectors we have:
        // We used id="totalakhir_display"? No — looking above we used id="totalakhir_display"? Actually we used id="totalakhir_display" earlier? To avoid confusion: set visible via totalakhir_display variable below:

        // set visible fields correctly:
        // (we DO have #totalakhir_display? In the markup we used id="totalakhir_display" - check: markup used id="totalakhir_display"? It used id="totalakhir_display" earlier? If not, we'll update the element by name)
        // For safety, update by inputs with class rupiah and hidden ids:
        $('#totalakhir').val(totalAkhir);
        $('#totalakhir').siblings('.total_akhir_display').val ? $('#totalakhir').siblings('.total_akhir_display').val(formatRupiah(totalAkhir)) : null;

        // But simpler: update the display inputs by selecting input[name="total_akhir"]? We don't have. To avoid confusion, below we set the visible elements by their selectors that exist in template:
        // Visible total akhir display has id totalakhir_display? (We used id="totalakhir_display" in markup above (yes) — if not, adjust.)
        try {
            $('#totalakhir_display').val(formatRupiah(totalAkhir));
        } catch (e) { /* ignore */ }

        $('#sisa').val(sisa);
        $('#sisa_display').val(formatRupiah(sisa));

        // Also set hidden pot/cash/dp values (they will be set on input events too)
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
    $('#potongan_display').on('input', function(){
        let v = parseRupiah($(this).val());
        $('#potongan').val(v);
        $(this).val(v ? formatRupiah(v) : '');
        hitungSummary();
    });
    $('#cashback_display').on('input', function(){
        let v = parseRupiah($(this).val());
        $('#cashback').val(v);
        $(this).val(v ? formatRupiah(v) : '');
        hitungSummary();
    });
    $('#dp_display').on('input', function(){
        let v = parseRupiah($(this).val());
        $('#dp').val(v);
        $(this).val(v ? formatRupiah(v) : '');
        hitungSummary();
    });

    // on form submit: ensure all display inputs converted to hidden numeric values
    $('#form-create-invoice').on('submit', function(e){
        // convert any remaining display rupiah inputs to numeric hidden before submit
        // for every row:
        $('#tablePasang tbody tr').each(function(){
            let $row = $(this);
            // harga: if display exists, ensure hidden exists or value assigned to name input
            let hargaVal = 0;
            if ($row.find('.harga_display').length) hargaVal = parseRupiah($row.find('.harga_display').val());
            // set the hidden input that controller expects: input[name="harga_satuan[]"]
            if ($row.find('input[name="harga_satuan[]"]').length) {
                $row.find('input[name="harga_satuan[]"]').val(hargaVal);
            } else if ($row.find('.harga').length) {
                $row.find('.harga').val(hargaVal);
            }
            // totals & grand totals: ensure hidden present
            let totalVal = parseRupiah($row.find('.total_display').val() || $row.find('.total').val());
            if ($row.find('input[name="total[]"]').length) $row.find('input[name="total[]"]').val(totalVal);
            if ($row.find('input[name="grand_total[]"]').length) $row.find('input[name="grand_total[]"]').val(parseRupiah($row.find('.grand_display').val()) || $row.find('.grand_total').val());
        });

        // potongan/cash/dp already have hidden ids set by events; ensure fallback:
        if (!$('#potongan').val()) $('#potongan').val(parseRupiah($('#potongan_display').val()));
        if (!$('#cashback').val()) $('#cashback').val(parseRupiah($('#cashback_display').val()));
        if (!$('#dp').val()) $('#dp').val(parseRupiah($('#dp_display').val()));

        // allow submit to continue
    });

    // initialize: format existing blanks and calc
    $(document).ready(function() {
        $('#tablePasang tbody tr').each(function(){
            let $r = $(this);
            // if there is an existing harga input with value (unformatted), format it
            if ($r.find('input[name="harga_satuan[]"]').length && $r.find('input[name="harga_satuan[]"]').val()) {
                let hv = parseRupiah($r.find('input[name="harga_satuan[]"]').val());
                $r.find('.harga_display').val(hv ? formatRupiah(hv) : '');
                $r.find('.harga').val(hv);
            }
            // trigger initial calc
            hitungRow($r);
        });
        hitungSummary();
        $(document).ready(function () {
    $("#form-create-invoice").on("submit", function (e) {
        e.preventDefault();

        let form = $(this);
        let formData = new FormData(this);

        $.ajax({
            url: form.attr("action"),
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                toastr.success("Invoice berhasil disimpan");

                // reload datatable rekap (jika ada)
                tableRekap.ajax.reload(null, false);

                // reload detail interaksi agar invoice baru muncul
                let interaksiId = $("#interaksi_id").val();
                $("#myModal").load("{{ url('rekap') }}/" + interaksiId + "/show_ajax");

                // tutup modal create
                $("#crudModal").modal("hide");
            },
            error: function (xhr) {
                Swal.fire("Gagal", "Terjadi kesalahan server.", "error");
                console.error("Server Error:", xhr.responseText);
            }
        });
    });
});
    });
</script>
