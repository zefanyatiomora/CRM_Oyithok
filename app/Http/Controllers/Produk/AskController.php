<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InteraksiAwalModel;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;


class AskController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'dashboard';
        $breadcrumb = (object) [
            'title' => 'SELAMAT DATANG WALLPAPER ID',
            'list' => ['Home', 'Dashboard']
        ];
        $page = (object) [
            'title' => 'SELAMAT DATANG WALLPAPER ID'
        ];
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        // Ambil semua kategori produk
        $kategoriLabels = KategoriModel::pluck('kategori_nama');

        // Data ASK berdasarkan kategori
        $askKategori = InteraksiAwalModel::with('kategori')
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_chat', $tahun);
                if ($bulan) {
                    $q->whereMonth('tanggal_chat', $bulan);
                }
            })
            ->get()
            ->groupBy(fn($item) => $item->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

        // Bentuk array final biar urut sesuai master kategori
        $dataAsk = [];
        foreach ($kategoriLabels as $kategori) {
            $dataAsk[$kategori] = $askKategori[$kategori] ?? 0;
        }

        // Jumlah total produk ASK
        $jumlahProdukAsk = array_sum($dataAsk);

        return view('dashboardproduk.ask.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,

            'tahun' => $tahun,
            'bulan' => $bulan,
            'kategoriLabels' => $kategoriLabels,
            'dataAsk' => $dataAsk,
            'jumlahProdukAsk' => $jumlahProdukAsk,
        ]);
    }
    public function list(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        Log::info('AskController list params', [
            'tahun' => $tahun,
            'bulan' => $bulan
        ]);

        $query = InteraksiAwalModel::with(['interaksi.customer', 'kategori'])
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
                return $row->kategori->kategori_nama ?? 'Tanpa Kategori';
            })
            ->addColumn('aksi', function ($row) {
                $url = route('ask.show_ajax', $row->awal_id);
                return '<a href="javascript:void(0)" onclick="modalAction(\'' . $url . '\')" 
                        class="btn btn-info btn-sm">Detail</a>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
}
