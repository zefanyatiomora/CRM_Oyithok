<?php

namespace App\Http\Controllers;

use App\Models\ProdukModel;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    // Menampilkan semua produk
    public function index()
    {
        $produks = ProdukModel::all();
        return view('produks.index', compact('produks'));
    }

    // Tampilkan form tambah produk
    public function create()
    {
        return view('produks.create');
    }

    // Simpan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'produk_kode' => 'required|string|max:100|unique:produks',
            'produk_nama' => 'required|string|max:255',
            'produk_kategori' => 'nullable|string|max:255',
        ]);

        ProdukModel::create($request->all());

        return redirect()->route('produks.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    // Tampilkan detail produk
    public function show($id)
    {
        $produk = ProdukModel::findOrFail($id);
        return view('produks.show', compact('produk'));
    }

    // Tampilkan form edit
    public function edit($id)
    {
        $produk = ProdukModel::findOrFail($id);
        return view('produks.edit', compact('produk'));
    }

    // Simpan perubahan produk
    public function update(Request $request, $id)
    {
        $request->validate([
            'produk_kode' => 'required|string|max:100|unique:produks,produk_kode,' . $id . ',produk_id',
            'produk_nama' => 'required|string|max:255',
            'produk_kategori' => 'nullable|string|max:255',
        ]);

        $produk = ProdukModel::findOrFail($id);
        $produk->update($request->all());

        return redirect()->route('produks.index')->with('success', 'Produk berhasil diperbarui.');
    }

    // (Opsional) Hapus produk
    public function destroy($id)
    {
        $produk = ProdukModel::findOrFail($id);
        $produk->delete();

        return redirect()->route('produks.index')->with('success', 'Produk berhasil dihapus.');
    }
}
