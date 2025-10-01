<?php

namespace App\Http\Controllers;

use App\Models\PICModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PICController extends Controller
{
    /**
     * Halaman index
     */
    public function index()
    {
        $activeMenu = 'pic'; // supaya sidebar aktif

        $breadcrumb = (object) [
            'title' => 'Data PIC',
            'list'  => ['Data PIC']
        ];

        return view('pic.index', compact('activeMenu', 'breadcrumb'));
    }

    /**
     * DataTables JSON untuk AJAX
     */
    public function getData(Request $request)
    {
        if ($request->ajax()) {
            $data = PICModel::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn  = '<button class="btn btn-sm btn-primary editPic" data-id="'.$row->pic_id.'">Edit</button> ';
                    $btn .= '<button class="btn btn-sm btn-danger deletePic" data-id="'.$row->pic_id.'">Hapus</button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Simpan PIC baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'pic_nama' => 'required|string|max:255',
        ]);

        $pic = PICModel::create([
            'pic_nama' => $request->pic_nama,
        ]);

        return response()->json(['success' => true, 'message' => 'PIC berhasil ditambahkan', 'data' => $pic]);
    }

    /**
     * Ambil data PIC untuk Edit
     */
    public function edit($id)
    {
        $pic = PICModel::findOrFail($id);
        return response()->json($pic);
    }

    /**
     * Update data PIC
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'pic_nama' => 'required|string|max:255',
        ]);

        $pic = PICModel::findOrFail($id);
        $pic->update([
            'pic_nama' => $request->pic_nama,
        ]);

        return response()->json(['success' => true, 'message' => 'PIC berhasil diperbarui']);
    }

    /**
     * Hapus PIC
     */
    public function destroy($id)
    {
        $pic = PICModel::findOrFail($id);
        $pic->delete();

        return response()->json(['success' => true, 'message' => 'PIC berhasil dihapus']);
    }
}
