<?php

namespace App\Http\Controllers;

use App\Models\CustomersModel;
use App\Models\InteraksiModel;
use App\Models\ProdukModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Tambahkan di paling atas

class KebutuhanController extends Controller
{
    public function index(Request $request)
{
    $activeMenu = 'dashboard';

    // Ambil interaksi pertama atau sesuai kebutuhan
    $interaksi = InteraksiModel::first();  
    // Kalau pakai ID tertentu:
    // $interaksi = InteraksiModel::find($request->id);

    return view('formkebutuhan.create', compact('activeMenu', 'interaksi'));
}
    public function create()
    {
        $produks = ProdukModel::all(); // harus 'produks', jamak
        return view('formkebutuhan.create', [
            'produks' => $produks,
            'activeMenu' => 'interaksis'
        ]);
    }

   public function store(Request $request)
    {
        // Validasi hanya customer_id
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,customer_id',
        ]);

        // Simpan interaksi hanya dengan customer_id
        $interaksi = InteraksiModel::create([
            'customer_id' => $validated['customer_id']
        ]);

        return redirect()->route('kebutuhan.index')
            ->with('success', 'Customer berhasil ditambahkan ke tabel interaksi. ID: ' . $interaksi->interaksi_id);
    }

    public function searchCustomer(Request $request)
    {
        $keyword = $request->get('keyword');

        $customers = CustomersModel::where('customer_nama', 'like', "%$keyword%")
            ->orWhere('customer_nohp', 'like', "%$keyword%")
            ->get();

        return response()->json($customers);
    }
    public function getCustomer($id)
    {
        $customer = CustomersModel::findOrFail($id);
        return response()->json($customer);
    }
}
