<?php

namespace App\Http\Controllers;

use App\Models\CustomerModel;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    // Menampilkan semua data customer
    public function index()
    {
        $customers = CustomerModel::all();
        return view('customers.index', compact('customers'));
    }

    // Menampilkan form tambah customer
    public function create()
    {
        return view('customers.create');
    }

    // Proses simpan customer baru
    public function store(Request $request)
    {
        $request->validate([
            'customer_nama' => 'required|string|max:255',
            'customer_kode' => 'required|string|max:100|unique:customers',
            'customer_alamat' => 'nullable|string',
            'customer_nohp' => 'nullable|string|max:20',
        ]);

        CustomerModel::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil ditambahkan.');
    }

    // Menampilkan detail customer
    public function show($id)
    {
        $customer = CustomerModel::findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    // Menampilkan form edit customer
    public function edit($id)
    {
        $customer = CustomerModel::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    // Proses update customer
    public function update(Request $request, $id)
    {
        $request->validate([
            'customer_nama' => 'required|string|max:255',
            'customer_kode' => 'required|string|max:100|unique:customers,customer_kode,' . $id . ',customer_id',
            'customer_alamat' => 'nullable|string',
            'customer_nohp' => 'nullable|string|max:20',
        ]);

        $customer = CustomerModel::findOrFail($id);
        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer berhasil diperbarui.');
    }

    // Menghapus customer
    public function destroy($id)
    {
        $customer = CustomerModel::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer berhasil dihapus.');
    }
}
