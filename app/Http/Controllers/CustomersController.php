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
        $customers = CustomersModel::all();
        return view('customers.index', compact('customers'));
    }

    public function data(Request $request)
{
    $data = CustomersModel::select([
        'customer_id',
        'customer_nama',
        'customer_kode',
        'customer_alamat',
        'customer_nohp',
        'informasi_media',
        'loyalty_point'
    ]);

    return DataTables::of($data)
        ->addColumn('aksi', function($row) {
            return '
                <button onclick="modalAction(\''.route('customers.edit', $row->customer_id).'\')" class="btn btn-sm btn-warning">Edit</button>
                <form action="'.route('customers.destroy', $row->customer_id).'" method="POST" style="display:inline;">
                    '.csrf_field().method_field('DELETE').'
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin ingin menghapus?\')">Hapus</button>
                </form>
            ';
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

    // Tampilkan detail customer
    public function show($id)
    {
        $customer = CustomersModel::findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    // Tampilkan form edit
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
}
