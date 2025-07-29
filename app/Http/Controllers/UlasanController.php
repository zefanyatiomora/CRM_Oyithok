<?php

namespace App\Http\Controllers;

use App\Models\UlasanModel;
use App\Models\InteraksiModel;
use App\Models\CustomerModel;
use App\Models\ProdukModel;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class UlasanController extends Controller
{
    /**
     * Tampilkan halaman index ulasan dengan DataTables.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = UlasanModel::with(['interaksi', 'customer', 'produk'])->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('customer_nama', function ($row) {
                    return $row->customer->customer_nama ?? '-';
                })
                ->addColumn('produk_nama', function ($row) {
                    return $row->produk->produk_nama ?? '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('ulasan.edit', $row->ulasan_id) . '" class="btn btn-sm btn-warning">Edit</a>
                        <form action="' . route('ulasan.destroy', $row->ulasan_id) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin hapus?\')">Hapus</button>
                        </form>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('ulasan.index'); // Pastikan kamu punya file resources/views/ulasan/index.blade.php
    }

    /**
     * Simpan ulasan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'interaksi_id' => 'required|exists:interaksi,interaksi_id',
            'customer_id' => 'required|exists:customers,customer_id',
            'produk_id' => 'required|exists:produk,produk_id',
            'kerapian' => 'nullable|integer',
            'kecepatan' => 'nullable|integer',
            'kualitas_material' => 'nullable|integer',
            'profesionalisme' => 'nullable|integer',
            'tepat_waktu' => 'nullable|integer',
            'kebersihan' => 'nullable|integer',
            'kesesuaian_desain' => 'nullable|integer',
            'kepuasan_keseluruhan' => 'nullable|integer',
        ]);

        UlasanModel::create($validated);
        return redirect()->back()->with('success', 'Ulasan berhasil ditambahkan');
    }

    /**
     * Tampilkan data untuk diedit.
     */
    public function edit($id)
    {
        $ulasan = UlasanModel::findOrFail($id);
        return view('ulasan.edit', compact('ulasan'));
    }

    /**
     * Update data ulasan.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'interaksi_id' => 'required|exists:interaksi,interaksi_id',
            'customer_id' => 'required|exists:customers,customer_id',
            'produk_id' => 'required|exists:produk,produk_id',
            'kerapian' => 'nullable|integer',
            'kecepatan' => 'nullable|integer',
            'kualitas_material' => 'nullable|integer',
            'profesionalisme' => 'nullable|integer',
            'tepat_waktu' => 'nullable|integer',
            'kebersihan' => 'nullable|integer',
            'kesesuaian_desain' => 'nullable|integer',
            'kepuasan_keseluruhan' => 'nullable|integer',
        ]);

        $ulasan = UlasanModel::findOrFail($id);
        $ulasan->update($validated);

        return redirect()->route('ulasan.index')->with('success', 'Ulasan berhasil diperbarui');
    }

    /**
     * Hapus data ulasan.
     */
    public function destroy($id)
    {
        $ulasan = UlasanModel::findOrFail($id);
        $ulasan->delete();

        return redirect()->back()->with('success', 'Ulasan berhasil dihapus');
    }
}
