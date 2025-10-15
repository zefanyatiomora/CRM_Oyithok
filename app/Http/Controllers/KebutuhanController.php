<?php

namespace App\Http\Controllers;

use App\Models\CustomersModel;
use App\Models\InteraksiModel;
use App\Models\ProdukModel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
        Log::info('Mencoba membuat interaksi baru.', ['request_data' => $request->all()]);

        $customerId = $request->input('customer_id');

        // SCENARIO 1: CUSTOMER BARU (customer_id tidak ada atau kosong)
        if (empty($customerId)) {
            // Validasi data untuk customer baru
            $validatedCustomer = $request->validate([
                'customer_nama'   => 'required|string|max:255',
                'customer_kode'   => 'required|string|unique:customers,customer_kode', // Pastikan kode unik
                'customer_nohp'   => 'required|string',
                'customer_alamat' => 'nullable|string',
                'informasi_media' => 'nullable|string',
            ]);

            // Buat customer baru di tabel 'customers'
            $newCustomer = CustomersModel::create($validatedCustomer);

            // Ambil ID dari customer yang baru saja dibuat
            $customerId = $newCustomer->customer_id;

            Log::info('Customer baru berhasil dibuat.', ['customer_id' => $customerId]);
        }
        // SCENARIO 2: CUSTOMER LAMA (customer_id sudah ada)
        else {
            // Validasi bahwa customer_id yang dikirim memang ada di database
            $request->validate([
                'customer_id' => 'required|integer|exists:customers,customer_id',
            ]);
            Log::info('Menggunakan customer yang sudah ada.', ['customer_id' => $customerId]);
        }

        // -- BAGIAN INI BERJALAN UNTUK KEDUA SCENARIO --
        $tanggalChat = $request->input('tanggal_chat', now()->format('Y-m-d'));
        // Buat interaksi menggunakan customerId yang sudah didapat
        $interaksi = InteraksiModel::create([
            'customer_id' => $customerId,
            'produk_id' => 11,
            'tanggal_chat' => $tanggalChat,
            'original_step' => 0,
            'status' => 'Follow Up',
            'tahapan' => 'identifikasi',
        ]);

        Log::info('Interaksi berhasil disimpan.', ['interaksi_id' => $interaksi->interaksi_id]);

        return response()->json([
            'status'  => true,
            'message' => 'Interaksi berhasil disimpan untuk Customer ID: ',
            'interaksi_id' => $interaksi->interaksi_id
        ]);
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
