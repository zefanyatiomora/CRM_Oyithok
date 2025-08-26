<?php

namespace App\Http\Controllers;

use App\Models\InteraksiModel;
use App\Models\ProdukModel;
use App\Models\CustomersModel;
use Illuminate\Http\Request;

class TambahKebutuhanController extends Controller
{
    // Menampilkan daftar kebutuhan customer
    public function index()
    {
        $customer = CustomersModel::first();
        $produks = ProdukModel::all();
        $interaksis = $customer 
            ? InteraksiModel::where('customer_id', $customer->customer_id)
                ->orderBy('tanggal_chat','desc')
                ->get()
            : [];

        return view('rekap.index_realtime', compact('customer','interaksis'));
    }

    // Form tambah kebutuhan baru
    public function create($customer_id)
    {
        $customer = CustomersModel::findOrFail($customer_id);
        $produks = ProdukModel::all();
        return view('rekap.tambah_kebutuhan', compact('customer','produks'));
    }

    // Simpan kebutuhan baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'produk_id'   => 'required|exists:produks,produk_id',
        ]);

        $produk = ProdukModel::findOrFail($validated['produk_id']);

        InteraksiModel::create([
            'customer_id' => $validated['customer_id'],
            'produk_id'   => $produk->produk_id,
            'produk_kode' => $produk->produk_kode,
            'produk_nama' => $produk->produk_nama,
        ]);

        return response()->json([
            'success' => 'Kebutuhan berhasil ditambahkan!'
        ]);
    }

    // Form edit identifikasi kebutuhan
    public function edit($interaksi_id)
    {
        $interaksi = InteraksiModel::findOrFail($interaksi_id);
        return view('rekap.tambah_kebutuhan', compact('interaksi'));
    }

    // Update identifikasi kebutuhan
    public function update(Request $request, $interaksi_id)
    {
        $validated = $request->validate([
            'identifikasi_kebutuhan' => 'required|string',
            'tanggal_chat' => 'required|date',
        ]);

        $interaksi = InteraksiModel::findOrFail($interaksi_id);
        $interaksi->identifikasi_kebutuhan = $validated['identifikasi_kebutuhan'];
        $interaksi->tanggal_chat = $validated['tanggal_chat'];
        $interaksi->save();

        return redirect()->route('tambahkebutuhan.index')
                         ->with('success','Identifikasi kebutuhan berhasil diperbarui!');
    }
}
