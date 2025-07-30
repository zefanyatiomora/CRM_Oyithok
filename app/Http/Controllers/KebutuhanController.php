<?php

namespace App\Http\Controllers;

use App\Models\InteraksiModel;
use App\Models\CustomersModel;
use App\Models\ProdukModel;
use Illuminate\Http\Request;

class KebutuhanController extends Controller
{
    // Menampilkan semua data interaksi
     public function index()
    {
        $data = InteraksiModel::all();
        return view('formkebutuhan.index', [
            'activeMenu' => 'formkebutuhan',
            'data' => $data
        ]);
    }
    // Menampilkan form tambah interaksi
    public function create()
    {
        $customers = CustomersModel::all();
        $produks = ProdukModel::all();
        return view('interaksi.create', compact('customers', 'produks'));
    }

    // Simpan data interaksi baru
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'produk_id' => 'required|exists:produks,produk_id',
            'produk_kode' => 'nullable|string',
            'tanggal_chat' => 'required|date',
            'identifikasi_kebutuhan' => 'nullable|string',
            'media' => 'nullable|string|max:100',
        ]);

        InteraksiModel::create($request->all());

        return redirect()->route('interaksi.index')->with('success', 'Data interaksi berhasil ditambahkan.');
    }

    // Tampilkan detail interaksi
    public function show($id)
    {
        $interaksi = InteraksiModel::with('customer', 'produk')->findOrFail($id);
        return view('interaksi.show', compact('interaksi'));
    }

    // Menampilkan form edit
    public function edit($id)
    {
        $interaksi = InteraksiModel::findOrFail($id);
        $customers = CustomersModel::all();
        $produks = ProdukModel::all();
        return view('interaksi.edit', compact('interaksi', 'customers', 'produks'));
    }

    // Update data interaksi
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'produk_id' => 'required|exists:produks,produk_id',
            'produk_kode' => 'nullable|string',
            'tanggal_chat' => 'required|date',
            'identifikasi_kebutuhan' => 'nullable|string',
            'media' => 'nullable|string|max:100',
        ]);

        $interaksi = InteraksiModel::findOrFail($id);
        $interaksi->update($request->all());

        return redirect()->route('interaksi.index')->with('success', 'Data interaksi berhasil diperbarui.');
    }

    // Hapus interaksi
    public function destroy($id)
    {
        $interaksi = InteraksiModel::findOrFail($id);
        $interaksi->delete();

        return redirect()->route('interaksi.index')->with('success', 'Data interaksi berhasil dihapus.');
    }
}
