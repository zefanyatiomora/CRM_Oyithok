<?php

namespace App\Http\Controllers;

use App\Models\ProdukModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class ProdukController extends Controller
{
    // Menampilkan semua produk
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'List Produk',
            'list' => ['Home', 'Produk']
        ];
        $page = (object) [
            'title' => 'List Produk dalam sistem'
        ];
        $activeMenu = 'produk'; // Set menu yang sedang aktif

        return view('produk.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }
    public function list(Request $request)
    {
        $levels = ProdukModel::select('produk_id', 'produk_kode', 'produk_nama', 'produk_kategori');

        return DataTables::of($levels)
            ->addIndexColumn()
            ->addColumn('aksi', function ($produk) { // menambahkan kolom aksi 
                // $btn  = '<a href="'.url('/produk/' . $produk->produk_id).'" class="btn btn-info btn-sm">Detail</a> '; 
                // $btn .= '<a href="'.url('/produk/' . $produk->produk_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> '; 
                // $btn .= '<form class="d-inline-block" method="POST" action="'.url('/produk/'.$produk->produk_id).'">'.csrf_field().method_field('DELETE') .'<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';      
                $btn  = '<button onclick="modalAction(\'' . url('/produk/' . $produk->produk_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/produk/' . $produk->produk_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/produk/' . $produk->produk_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html 
            ->make(true);
    }

    public function create_ajax()
    {
        $produk = ProdukModel::select('produk_id', 'produk_nama', 'produk_kategori', 'produk_kode')->get();
        return view('produk.create_ajax')->with('produk', $produk);
    }

    // Simpan produk baru
    public function store(Request $request)
    {
        $request->validate([
            'produk_kode' => 'required|string|max:100|unique:produks',
            'produk_nama' => 'required|string|max:255',
            'produk_kategori' => 'nullable|string|max:255',
        ]);

        ProdukModel::create($request->all());

        return redirect()->route('produks.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    // Tampilkan detail produk
    public function show($id)
    {
        $produk = ProdukModel::findOrFail($id);
        return view('produks.show', compact('produk'));
    }

    // Tampilkan form edit
    public function edit($id)
    {
        $produk = ProdukModel::findOrFail($id);
        return view('produks.edit', compact('produk'));
    }

    // Simpan perubahan produk
    public function update(Request $request, $id)
    {
        $request->validate([
            'produk_kode' => 'required|string|max:100|unique:produks,produk_kode,' . $id . ',produk_id',
            'produk_nama' => 'required|string|max:255',
            'produk_kategori' => 'nullable|string|max:255',
        ]);

        $produk = ProdukModel::findOrFail($id);
        $produk->update($request->all());

        return redirect()->route('produks.index')->with('success', 'Produk berhasil diperbarui.');
    }

    // (Opsional) Hapus produk
    public function destroy($id)
    {
        $produk = ProdukModel::findOrFail($id);
        $produk->delete();

        return redirect()->route('produks.index')->with('success', 'Produk berhasil dihapus.');
    }
}
