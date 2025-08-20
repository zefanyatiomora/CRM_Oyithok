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
        // 1. Log data yang masuk dari request
        Log::info('Mencoba membuat interaksi baru.', ['request_data' => $request->all()]);

        // try {
        // 2. Lakukan validasi seperti biasa
        $validated = $request->validate([
            'customer_id' => 'required|integer|exists:customers,customer_id',
        ]);

        // Log data yang sudah lolos validasi
        Log::info('Validasi berhasil.', ['validated_data' => $validated]);

        // 3. Coba simpan interaksi
        $interaksi = InteraksiModel::create([
            'customer_id' => $validated['customer_id'],
            'produk_id' => 11,
            'tanggal_chat' => now(),
            // Kolom lain akan diisi NULL atau nilai default oleh database
        ]);

        // Jika berhasil, log informasinya
        Log::info('Interaksi berhasil disimpan.', ['interaksi_id' => $interaksi->interaksi_id]);

        return response()->json(
            [
                'status' => true,
                'message' => 'Interaksi berhasil disimpan'
            ]
        );
        // } catch (ValidationException $e) {
        //     // 4. Tangkap dan log jika validasi gagal
        //     Log::error('Validasi gagal.', [
        //         'errors' => $e->errors(),
        //         'request_data' => $request->all()
        //     ]);
        //     // Redirect kembali dengan error validasi
        //     return redirect()->back()->withErrors($e->errors())->withInput();
        // } catch (\Exception $e) {
        //     // 5. Tangkap SEMUA error lain (termasuk error database)
        //     Log::error('GAGAL MENYIMPAN INTERAKSI: Terjadi exception.', [
        //         'error_message' => $e->getMessage(), // Pesan error yang paling penting!
        //         'error_trace' => $e->getTraceAsString(), // Detail error untuk debug mendalam
        //         'request_data' => $request->all()
        //     ]);

        //     // Redirect kembali dengan pesan error umum
        //     return redirect()->back()
        //         ->with('error', 'Terjadi kesalahan pada server. Silakan coba lagi.');
        // }
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
