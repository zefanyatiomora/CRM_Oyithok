<?php

namespace App\Http\Controllers;

use App\Models\CustomersModel;
use Illuminate\Http\Request;
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
                $btn  = '<button onclick="modalAction(\'' . url('/customers/' . $customer->customer_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn  = '<button onclick="modalAction(\'' . url('/customers/' . $customer->customer_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';

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

    // Tampilkan form edit customer
    public function edit($id)
    {
        $customer = CustomersModel::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    // Proses update data customer
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_kode' => 'required|string|max:100|unique:customers,customer_kode,' . $id . ',customer_id',
            'customer_nama' => 'required|string|max:255',
            'customer_alamat' => 'nullable|string',
            'customer_nohp' => 'nullable|string|max:20',
            'informasi_media' => 'nullable|in:google,medsos,offline',
            'loyalty_point' => 'nullable|integer|min:0',
        ]);

        $customer = CustomersModel::findOrFail($id);
        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
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
}
