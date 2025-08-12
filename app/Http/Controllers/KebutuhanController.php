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
        $interaksis = InteraksiModel::with(['customer'])
            ->orderByDesc('tanggal_chat')
            ->get()
            ->map(function ($item) {
                $produks = ProdukModel::find($item->produk_id);
                $item->produk_nama = $produks?->produk_nama ?? '-';
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
            'customer_nama' => 'required',
            'customer_kode' => 'required',
            'customer_nohp' => 'required',
            'customer_alamat' => 'required',
            'informasi_media' => 'required',
            'tanggal_chat' => 'required|date',
            'identifikasi_kebutuhan' => 'required',
            'media' => 'nullable|string',
            'produk_id' => 'required|integer|exists:produks,produk_id',
        ]);

        DB::beginTransaction();

        try {
            // Cek pelanggan lama atau buat baru
            if ($request->filled('customer_id')) {
                $customer_id = $request->input('customer_id');
            } else {
                $customer = CustomersModel::create([
                    'customer_nama'   => $request->input('customer_nama'),
                    'customer_kode'   => $request->input('customer_kode'),
                    'customer_nohp'   => $request->input('customer_nohp'),
                    'customer_alamat' => $request->input('customer_alamat'),
                    'informasi_media' => $request->input('informasi_media')
                ]);
                $customer_id = $customer->customer_id;
            }

            // Ambil nama produk berdasarkan ID
            $produkId = $request->input('produk_id');
            $produks = ProdukModel::find($produkId);
            // Simpan ke tabel interaksi dan simpan hasilnya
            $interaksi = InteraksiModel::create([
                'customer_id'             => $customer_id,
                'tanggal_chat'            => $request->input('tanggal_chat'),
                'produk_id'               => $produks->produk_id,
                'produk_nama'             => $produks->produk_nama,
                'tahapan'                 => 'identifikasi',
                'identifikasi_kebutuhan'  => $request->input('identifikasi_kebutuhan'),
                'media'                   => $request->input('media'),
            ]);


            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data interaksi berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Simpan log error ke laravel.log
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
