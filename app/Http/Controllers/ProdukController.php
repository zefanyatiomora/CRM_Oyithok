<?php

namespace App\Http\Controllers;

use App\Models\ProdukModel;
use App\Models\KategoriModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;



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
        $kategori = KategoriModel::all();

        $activeMenu = 'produk'; // Set menu yang sedang aktif

        return view('produk.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }
    public function list(Request $request)
    {
        $levels = ProdukModel::with('kategori')->select('produk_id', 'produk_kode', 'produk_nama', 'kategori_id');

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
        $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
        return view('produk.create_ajax')->with('kategori', $kategori);
    }

    // Simpan produk baru
    public function store(Request $request)
    {
        $request->validate([]);

        ProdukModel::create($request->all());

        return redirect('/produk')->with('success', 'Data produk berhasil disimpan');
    }
    public function store_ajax(Request $request)
    {
        // Check if the request is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'produk_kode' => 'required|string|max:100|unique:produks',
                'produk_nama' => 'required|string|max:255',
                'kategori_id' => 'required|exists:kategoris,kategori_id',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            ProdukModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data produk berhasil disimpan'
            ]);
        }
        return redirect('/');
    }

    // Tampilkan detail produk
    public function show($id)
    {
        $produk = ProdukModel::findOrFail($id);
        return view('produks.show', compact('produk'));
    }

    public function edit_ajax(string $id)
    {
        $produk = ProdukModel::find($id);
        return view('produk.edit_ajax', ['produk' => $produk]);
    }

    // Simpan perubahan produk
    public function update(Request $request, $id)
    {
        $request->validate([
            'produk_kode' => 'required|string|max:100|unique:produks,produk_kode,' . $id . ',produk_id',
            'produk_nama' => 'required|string|max:255',
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
    public function confirm_ajax(string $id)
    {
        $produk = ProdukModel::find($id);
        return view('produk.confirm_ajax', ['produk' => $produk]);
    }
    public function delete_ajax(Request $request, $id)
    {
        // Check if the request is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            $produk = ProdukModel::find($id);
            if ($produk) {
                $produk->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }
}
