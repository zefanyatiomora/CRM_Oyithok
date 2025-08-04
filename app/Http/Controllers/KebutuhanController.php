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
    public function index()
    {
        $interaksis = InteraksiModel::with(['customers'])
            ->orderByDesc('tanggal_chat')
            ->get()
            ->map(function ($item) {
                $produkIds = json_decode($item->produk_id, true) ?? [];
                $produkNames = ProdukModel::whereIn('produk_id', $produkIds)->pluck('produk_nama')->toArray();
                $item->produk_nama = implode(', ', $produkNames);
                return $item;
            });

        $produks = ProdukModel::all();

        return view('formkebutuhan.create', [
            'produks' => $produks,
            'activeMenu' => 'interaksis',
            'breadcrumb' => (object)[
                'title' => 'Form Kebutuhan',
                'list' => [
                    'Dashboard' => route('dashboard'),
                    'Form Kebutuhan' => ''
                ]
            ]
        ]);
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
    $validated = $request->validate([
        'customer_kode' => 'required|unique:customers,customer_kode',
        'customer_nama' => 'required',
        'customer_nohp' => 'required',
        'customer_alamat' => 'required',
        'informasi_media' => 'required',
        'tanggal_chat' => 'required|date',
        'identifikasi_kebutuhan' => 'required',
        'media' => 'nullable|string',
        'produk_id' => 'required|exists:produks,produk_id'
    ]);

    DB::beginTransaction();

    try {
        if ($request->filled('customer_id')) {
            $customer_id = $request->input('customer_id');
        } else {
            $customer = CustomersModel::create([
                'customer_kode' => $request->customer_kode,
                'customer_nama' => $request->customer_nama,
                'customer_nohp' => $request->customer_nohp,
                'customer_alamat' => $request->customer_alamat,
                'informasi_media' => $request->informasi_media,
            ]);
            $customer_id = $customer->customer_id;
        }

        $produkId = $request->produk_id;
        $produkNama = ProdukModel::find($produkId)->produk_nama;

        InteraksiModel::create([
            'customer_id' => $customer_id,
            'tanggal_chat' => $request->tanggal_chat,
            'produk_id' => $produkId,
            'produk_nama' => $produkNama,
            'identifikasi_kebutuhan' => $request->identifikasi_kebutuhan,
            'media' => $request->media,
        ]);

        DB::commit();
        return redirect()->route('kebutuhan.create')->with('success', 'Data berhasil disimpan.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Gagal menyimpan data interaksi/customer', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
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
