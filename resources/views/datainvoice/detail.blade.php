<div class="row">
    <div class="col-md-6">
        <p><strong>No Invoice:</strong> {{ $invoice->nomor_invoice }}</p>
        <p><strong>Customer:</strong> {{ $invoice->customer->customer_nama ?? '-' }}</p>
        <p><strong>PIC:</strong> {{ $invoice->pic->nama ?? '-' }}</p>
    </div>
    <div class="col-md-6">
        <p><strong>Status:</strong> 
            @if($invoice->status == 'paid')
                <span class="badge badge-success">Lunas</span>
            @elseif($invoice->status == 'unpaid')
                <span class="badge badge-warning">Belum Lunas</span>
            @else
                <span class="badge badge-secondary">{{ ucfirst($invoice->status) }}</span>
            @endif
        </p>
        <p><strong>Total:</strong> Rp {{ number_format($invoice->grand_total, 0, ',', '.') }}</p>
        <p><strong>Tanggal:</strong> {{ $invoice->created_at->format('d M Y') }}</p>
    </div>
</div>

<hr>

<h6>Detail Item</h6>
<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead class="thead-light">
            <tr class="text-center">
                <th>No</th>
                <th>Nama Item</th>
                <th>Harga Satuan</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->details as $key => $detail)
<tr>
    <td class="text-center">{{ $key + 1 }}</td>
    <td>{{ $detail->pasang->produk->nama_produk ?? '-' }}</td> <!-- ambil dari relasi produk -->
    <td class="text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
    <td class="text-center">{{ $detail->pasang->kuantitas ?? 1 }}</td> <!-- ambil dari pasang -->
    <td class="text-right">Rp {{ number_format($detail->total, 0, ',', '.') }}</td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center text-muted">Belum ada detail item</td>
</tr>
@endforelse

        </tbody>
    </table>
</div>
