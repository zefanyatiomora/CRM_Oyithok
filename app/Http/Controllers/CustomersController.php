<?php

namespace App\Http\Controllers;

use App\Models\CustomersModel;
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
                $btn  = '<button onclick="modalAction(\''.url('/customers/' . $customer->customer_id . '/edit').'\')" class="btn btn-warning btn-sm">Edit</i></button> ';
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
    public function store(Request $request)
    {
        $request->validate([
            'customer_kode' => 'required|string|max:100|unique:customers',
            'customer_nama' => 'required|string|max:255',
            'customer_alamat' => 'nullable|string',
            'customer_nohp' => 'nullable|string|max:20',
            'informasi_media' => 'nullable|in:google,medsos,offline',
            'loyalty_point' => 'nullable|integer|min:0',
        ]);

        CustomersModel::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
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
}
