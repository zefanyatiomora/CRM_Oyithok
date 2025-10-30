@if($interaksi->pasang && $interaksi->pasang->count() > 0)
    @if($invoices)
        {{-- Jika invoice sudah ada: PDF aktif, Invoice disabled --}}
        <button class="btn btn-sm btn-primary" disabled>
            <i class="fas fa-plus fa-sm"></i> Buat Invoice
        </button>
        <a href="{{ route('invoice.export_pdf', $invoices->invoice_id) }}"
           class="btn btn-sm btn-danger" target="_blank">
           <i class="fas fa-file-pdf fa-sm"></i> PDF
        </a>
    @else
        {{-- Jika belum ada invoice --}}
        <a href="javascript:void(0);"
           onclick="openModal('{{ route('invoice.create', $interaksi->interaksi_id) }}')"
           class="btn btn-sm btn-primary">
            <i class="fas fa-plus fa-sm"></i> Buat Invoice
        </a>
        <button class="btn btn-sm btn-danger" disabled>
            <i class="fas fa-file-pdf fa-sm"></i> PDF
        </button>
    @endif
@else
    {{-- Jika belum ada pemasangan --}}
    <button class="btn btn-sm btn-primary" disabled>
        <i class="fas fa-plus fa-sm"></i> Buat Invoice
    </button>
    <button class="btn btn-sm btn-danger" disabled>
        <i class="fas fa-file-pdf fa-sm"></i> PDF
    </button>
@endif
