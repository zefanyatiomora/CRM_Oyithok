<?php

namespace App\Http\Controllers;

use App\Models\InvoiceKeteranganModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class InvoiceKeteranganController extends Controller
{
    /**
     * Menampilkan halaman utama keterangan invoice.
     */
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Keterangan Invoice',
            'list' => ['Home', 'Keterangan Invoice']
        ];

        $page = (object) [
            'title' => 'Daftar Keterangan Invoice'
        ];

        $activeMenu = 'keterangan_invoice';

        // ✅ Ambil semua data dari tabel invoice_keterangan
        $keterangans = InvoiceKeteranganModel::orderBy('created_at', 'desc')->get();

        return view('keterangan_invoice.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'keterangans' => $keterangans // ✅ kirim data ke view
        ]);
    }

    /**
     * Menampilkan data keterangan untuk DataTables (jika dipakai AJAX).
     */
    public function list(Request $request)
    {
        $keterangan = InvoiceKeteranganModel::select('keterangan_id', 'keterangan', 'created_at');

        return DataTables::of($keterangan)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                return '
                    <button onclick="modalAction(\'' . url('/keterangan-invoice/' . $row->keterangan_id . '/edit_ajax') . '\')" 
                        class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </button>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Menampilkan form edit keterangan invoice.
     */
    public function edit_ajax($id)
    {
        $keterangan = InvoiceKeteranganModel::findOrFail($id);
        return view('keterangan_invoice.edit', compact('keterangan'));
    }

    /**
     * Memproses update data keterangan invoice.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'keterangan' => 'required|string',
        ], [
            'keterangan.required' => 'Kolom keterangan wajib diisi.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
        ]);

        $keterangan = InvoiceKeteranganModel::findOrFail($id);
        $keterangan->update([
            'keterangan' => $request->keterangan,
        ]);

        return redirect()
            ->route('keterangan_invoice.index')
            ->with('success', 'Keterangan invoice berhasil diperbarui!');
    }
}
