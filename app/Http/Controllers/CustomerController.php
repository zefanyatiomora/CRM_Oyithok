<?php

namespace App\Http\Controllers;

use App\Models\CustomerModel;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Tampilkan semua customer
    public function index()
    {
        $customers = CustomerModel::all();
        return view('customers.index', compact('customers'));
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

        CustomerModel::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    // Tampilkan detail customer
    public function show($id)
    {
        $customer = CustomerModel::findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    // Tampilkan form edit
    public function edit($id)
    {
        $customer = CustomerModel::findOrFail($id);
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

        $customer = CustomerModel::findOrFail($id);
        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    // Hapus customer (jika ingin pakai delete juga)
    public function destroy($id)
    {
        $customer = CustomerModel::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
    }
}
