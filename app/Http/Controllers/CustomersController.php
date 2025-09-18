<?php

namespace App\Http\Controllers;

use App\Models\CustomersModel;
use App\Models\InteraksiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CustomersController extends Controller
{
    // Tampilkan semua customer
    public function index()
    {
        $activeMenu = 'customers';

        $breadcrumb = (object)[
            'title' => 'Data Customer',
            'list' => ['Master Data', 'Customer']
        ];

        return view('customers.index', compact('activeMenu', 'breadcrumb'));
    }

    // Load data untuk DataTables
    public function data(Request $request)
    {
        $customer = CustomersModel::select([
            'customer_id',
            'customer_kode',
            'customer_nama',
            'customer_alamat',
            'customer_nohp',
            'informasi_media',
            'loyalty_point'
        ]);

        return DataTables::of($customer)
            ->addIndexColumn()
            ->addColumn('aksi', function ($customer) {
                $btn  = '<button onclick="modalAction(\'' . url('/customers/' . $customer->customer_id . '/edit') . '\')" class="btn btn-warning btn-sm">Edit</i></button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/customers/' . $customer->customer_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    // Tampilkan form tambah customer
    public function create()
    {
        return view('customers.create');
    }

    // Proses simpan customer baru
    // Proses simpan customer baru
    public function store(Request $request)
    {
        $request->validate([
            'customer_nama' => 'required|string|max:255',
            'customer_kode' => 'required|string|max:100',
            'customer_nohp' => 'required|string|max:20',
            'customer_alamat' => 'required|string',
            'informasi_media' => 'nullable|string|max:100',
            'tanggal_chat' => 'required|date',
            'produk_id' => 'required|integer',
            'identifikasi_kebutuhan' => 'required|string',
            'media' => 'nullable|string'
        ]);

        // Simpan atau update customer
        if ($request->customer_id) {
            $customer = CustomersModel::find($request->customer_id);
            if ($customer) {
                $customer->update([
                    'customer_nama' => $request->customer_nama,
                    'customer_kode' => $request->customer_kode,
                    'customer_nohp' => '+62' . ltrim($request->customer_nohp, '0'),
                    'customer_alamat' => $request->customer_alamat,
                    'informasi_media' => $request->informasi_media
                ]);
            }
        } else {
            $customer = CustomersModel::create([
                'customer_nama' => $request->customer_nama,
                'customer_kode' => $request->customer_kode,
                'customer_nohp' => '+62' . ltrim($request->customer_nohp, '0'),
                'customer_alamat' => $request->customer_alamat,
                'informasi_media' => $request->informasi_media,
                'loyalty_point' => 0
            ]);
        }

        // Simpan kebutuhan
        InteraksiModel::create([
            'customer_id' => $customer->customer_id ?? $request->customer_id,
            'tanggal_chat' => $request->tanggal_chat,
            'produk_id' => $request->produk_id,
            'identifikasi_kebutuhan' => $request->identifikasi_kebutuhan,
            'media' => $request->media
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Data kebutuhan dan customer berhasil disimpan.'
        ]);
    }

    public function edit($id)
    {
        $customer = CustomersModel::find($id);

        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'Data tidak ditemukan']);
        }

        return view('customers.edit_ajax', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'customer_kode' => 'required|string|max:20|unique:customers,customer_kode,' . $id . ',customer_id',
                'customer_nama' => 'required|string|max:100',
                'customer_alamat' => 'nullable|string|max:255',
                'customer_nohp' => 'nullable|string|max:20',
                'informasi_media' => 'nullable|string|max:100',
                'loyalty_point' => 'nullable|numeric',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors(),
                ]);
            }

            // Format nomor HP ke +62
            if (!empty($request->customer_nohp)) {
                $nohp = preg_replace('/\D/', '', $request->customer_nohp); // hanya angka
                if (substr($nohp, 0, 1) === '0') {
                    $nohp = '+62' . substr($nohp, 1);
                } elseif (substr($nohp, 0, 3) !== '+62') {
                    $nohp = '+62' . $nohp;
                }
                $request->merge(['customer_nohp' => $nohp]);
            }

            $customer = CustomersModel::find($id);

            if ($customer) {
                $customer->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data customer berhasil diperbarui',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data customer tidak ditemukan',
                ]);
            }
        }

        return redirect('/');
    }

    // Hapus customer
    public function destroy($id)
    {
        $customer = CustomersModel::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
    }

    // Tampilkan detail customer (jika perlu)
    public function show_ajax(Request $request, string $id)
    {
        $customer = CustomersModel::find($id);

        if (!$customer) {
            return response()->json(['status' => false, 'message' => 'customer not found'], 404);
        }
        return view('customers.show_ajax', compact('customer'));
    }
    public function confirm($id)
    {
        $customer = CustomersModel::find($id);

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer tidak ditemukan.'
            ]);
        }

        return view('customers.confirm', compact('customer'));
    }
    // App\Models\CustomersModel.php
    public function refreshLoyalty()
    {
        // eager load relasi
        $this->loadMissing('interaksi.pasang.invoiceDetail.invoice');

        // hitung total transaksi closing
        $totalTransaction = $this->interaksi
            ->where('status', 'closing')
            ->count();

        // hitung total cash spent
        $totalCashSpent = $this->interaksi
            ->where('status', 'closing')
            ->sum(function ($interaksi) {
                if (
                    $interaksi->pasangkirim &&
                    $interaksi->pasangkirim->invoiceDetail &&
                    $interaksi->pasangkirim->invoiceDetail->invoice
                ) {
                    return $interaksi->pasangkirim->invoiceDetail->invoice->total_akhir;
                }
                return 0;
            });

        // update field di tabel
        $this->update([
            'total_transaction' => $totalTransaction,
            'total_cash_spent'  => $totalCashSpent,
        ]);
    }
}
