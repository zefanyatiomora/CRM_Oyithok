<?php

namespace App\Http\Controllers;

use App\Models\InteraksiModel;
use App\Models\CustomersModel;
use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\Report\Html\CustomCssFile;
use Illuminate\Support\Facades\Log; // Tambahkan di paling atas
use Yajra\DataTables\Facades\DataTables;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'dashboard';
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan'); // nullable

        $bulanList = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $customer = CustomersModel::all();
        // Judul dinamis
        $judulRekap = $bulan
            ? "Rekap Bulan " . $bulanList[$bulan] . " $tahun"
            : "Rekap Tahun $tahun";

        $breadcrumb = (object) [
            'title' => $judulRekap,
            'list' => ['Home', $judulRekap]
        ];
        $page = (object) [
            'title' => $judulRekap
        ];

        return view('rekap.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu, 'tahun' => $tahun, 'bulan' => $bulan, 'bulanList' => $bulanList, 'customer' => $customer]);
    }

    public function list(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        Log::info('RekapController@list: Filter Tahun = ' . $tahun . ', Bulan = ' . $bulan);

        $query = InteraksiModel::with('customer');
        $interaksi = InteraksiModel::with('customer')->select('interaksi_id', 'customer_id', 'produk_id', 'produk_nama', 'tanggal_chat', 'identifikasi_kebutuhan');

        Log::info('Query Preview:', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);

        if ($tahun) {
            $query->whereYear('tanggal_chat', $tahun);
        }

        if ($bulan) {
            $query->whereMonth('tanggal_chat', $bulan);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('produk_nama', function ($row) {
                return is_array($row->produk_id) ? implode(', ', $row->produk_id) : $row->produk_id;
            })
            ->make(true);
    }
}
