<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RincianModel;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class ClosingController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'dashboard';
        $breadcrumb = (object) [
            'title' => 'Data Produk CLOSING',
            'list' => ['Home', 'Dashboard', 'Closing Produk']
        ];
        $page = (object) [
            'title' => 'Daftar Produk Closing'
        ];

        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        // Ambil semua kategori produk
        $kategoriLabels = KategoriModel::pluck('kategori_nama');

        // Data CLOSING berdasarkan kategori
        $closingKategori = RincianModel::with(['produk.kategori', 'interaksi'])
            ->where('status', 'closing')
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_chat', $tahun);
                if ($bulan) {
                    $q->whereMonth('tanggal_chat', $bulan);
                }
            })
            ->get()
            ->groupBy(fn($item) => $item->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

        // Bentuk array final biar urut sesuai master kategori
        $dataClosing = [];
        foreach ($kategoriLabels as $kategori) {
            $dataClosing[$kategori] = $closingKategori[$kategori] ?? 0;
        }

        // Jumlah total produk CLOSING
        $jumlahProdukClosing = array_sum($dataClosing);

        return view('dashboardproduk.closing.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,

            'tahun' => $tahun,
            'bulan' => $bulan,
            'kategoriLabels' => $kategoriLabels,
            'dataClosing' => $dataClosing,
            'jumlahProdukClosing' => $jumlahProdukClosing,
        ]);
    }

    public function list(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        Log::info('ClosingController list params', [
            'tahun' => $tahun,
            'bulan' => $bulan
        ]);

        $query = RincianModel::with(['interaksi.customer', 'produk.kategori'])
            ->where('status', 'closing')
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                if ($tahun) {
                    $q->whereYear('tanggal_chat', $tahun);
                }
                if ($bulan) {
                    $q->whereMonth('tanggal_chat', $bulan);
                }
            });

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('tanggal_chat', function ($row) {
                return $row->interaksi->tanggal_chat ?? '-';
            })
            ->addColumn('customer.customer_kode', function ($row) {
                return $row->interaksi->customer->customer_kode ?? '-';
            })
            ->addColumn('customer.customer_nama', function ($row) {
                return $row->interaksi->customer->customer_nama ?? '-';
            })
            ->addColumn('kategori', function ($row) {
                return $row->produk->kategori->kategori_nama ?? 'Tanpa Kategori';
            })
            ->addColumn('aksi', function ($row) {
                $url = route('closing.show_ajax', $row->rincian_id);
                return '<a href="javascript:void(0)" onclick="modalAction(\'' . $url . '\')" 
                        class="btn btn-info btn-sm">Detail</a>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
