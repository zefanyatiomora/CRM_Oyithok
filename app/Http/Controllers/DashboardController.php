<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\InteraksiModel;
use App\Models\PasangKirimModel;
use App\Models\RincianModel;
use App\Models\KategoriModel;
use App\Models\InteraksiAwalModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
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

        $queryBase = InteraksiModel::whereYear('tanggal_chat', $tahun);
        if ($bulan) {
            $queryBase->whereMonth('tanggal_chat', $bulan);
        }

        // === Jumlah berdasarkan tahapan (sudah ada) ===
        $jumlahInteraksi = (clone $queryBase)->count();
        $prosesSurvey    = (clone $queryBase)->where('tahapan', 'survey')->count();
        $prosesPasang    = (clone $queryBase)->where('tahapan', 'pasang')->count();
        $prosesOrder     = (clone $queryBase)->where('tahapan', 'order')->count();

        // === Tambahan: Jumlah berdasarkan STATUS ===
        $jumlahAsk      = (clone $queryBase)->where('status', 'ask')->count();
        $jumlahFollowUp = (clone $queryBase)->where('status', 'follow up')->count();
        $jumlahHold     = (clone $queryBase)->where('status', 'hold')->count();
        $jumlahClosing  = (clone $queryBase)->where('status', 'closing')->count();

        $availableYears = InteraksiModel::selectRaw('YEAR(tanggal_chat) as year')
            ->distinct()->orderBy('year', 'desc')->pluck('year');

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
            '12' => 'Desember'
        ];

        // === Logika Chart (biarkan tetap seperti punyamu) ===
        $chartLabels   = [];
        $dataLeadsBaru = [];
        $dataLeadsLama = [];
        if ($bulan) {
            $jumlahHari = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;
            for ($hari = 1; $hari <= $jumlahHari; $hari++) {
                $chartLabels[] = $hari;
                $queryPerHari = InteraksiModel::whereYear('tanggal_chat', $tahun)
                    ->whereMonth('tanggal_chat', $bulan)
                    ->whereDay('tanggal_chat', $hari);

                $totalInteraksiHariIni = (clone $queryPerHari)->count();
                $leadsBaruHariIni = (clone $queryPerHari)->whereHas('customer', function ($q) use ($tahun, $bulan) {
                    $q->whereYear('created_at', $tahun)->whereMonth('created_at', $bulan);
                })->count();

                $dataLeadsBaru[] = $leadsBaruHariIni;
                $dataLeadsLama[] = $totalInteraksiHariIni - $leadsBaruHariIni;
            }
        } else {
            foreach ($bulanList as $key => $namaBulan) {
                $chartLabels[] = $namaBulan;
                $queryPerBulan = InteraksiModel::whereYear('tanggal_chat', $tahun)
                    ->whereMonth('tanggal_chat', $key);

                $totalInteraksiBulanIni = (clone $queryPerBulan)->count();
                $leadsBaruBulanIni = (clone $queryPerBulan)->whereHas('customer', function ($q) use ($tahun, $key) {
                    $q->whereYear('created_at', $tahun)->whereMonth('created_at', $key);
                })->count();

                $dataLeadsBaru[] = $leadsBaruBulanIni;
                $dataLeadsLama[] = $totalInteraksiBulanIni - $leadsBaruBulanIni;
            }
        }
        // Ambil semua kategori produk
        $kategoriLabels = KategoriModel::pluck('kategori_nama');

        // ASK (dari interaksi_realtime)
        $askKategori = InteraksiAwalModel::with('kategori')
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_chat', $tahun);
                if ($bulan) $q->whereMonth('tanggal_chat', $bulan);
            })
            ->get()
            ->groupBy(fn($item) => $item->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

        // HOLD (dari rincian)
        $holdKategori = RincianModel::with('produk.kategori', 'interaksi')
            ->where('status', 'hold')
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_chat', $tahun);
                if ($bulan) $q->whereMonth('tanggal_chat', $bulan);
            })
            ->get()
            ->groupBy(fn($item) => $item->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

        // CLOSING (dari rincian + pasang)
        $closingRincian = RincianModel::with('produk.kategori', 'interaksi')
            ->where('status', 'closing')
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_chat', $tahun);
                if ($bulan) $q->whereMonth('tanggal_chat', $bulan);
            })
            ->get()
            ->groupBy(fn($item) => $item->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

        $closingPasang = PasangKirimModel::with('produk.kategori', 'interaksi')
            ->whereIn('status', ['closing produk', 'closing pasang'])
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_chat', $tahun);
                if ($bulan) $q->whereMonth('tanggal_chat', $bulan);
            })
            ->get()
            ->groupBy(fn($item) => $item->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

        // Gabungkan closing
        $closingKategori = [];
        foreach ($kategoriLabels as $kategori) {
            $closingKategori[$kategori] =
                ($closingRincian[$kategori] ?? 0) + ($closingPasang[$kategori] ?? 0);
        }

        // === Bentuk array final untuk Chart ===
        $dataAsk = [];
        $dataHold = [];
        $dataClosing = [];
        foreach ($kategoriLabels as $kategori) {
            $dataAsk[] = $askKategori[$kategori] ?? 0;
            $dataHold[] = $holdKategori[$kategori] ?? 0;
            $dataClosing[] = $closingKategori[$kategori] ?? 0;
        }
        // === Hitung total produk per status dari hasil grouping kategori ===
        $jumlahProdukAsk     = array_sum($dataAsk);
        $jumlahProdukHold    = array_sum($dataHold);
        $jumlahProdukClosing = array_sum($dataClosing);
        // Logging hasilnya
        Log::info('Data kategori Ask:', $dataAsk);
        Log::info('Data kategori Hold:', $dataHold);
        Log::info('Data kategori Closing:', $dataClosing);

        Log::info('AskKategori detail:', $askKategori->toArray());
        Log::info('HoldKategori detail:', $holdKategori->toArray());
        Log::info('ClosingRincian detail:', $closingRincian->toArray());
        Log::info('ClosingPasang detail:', $closingPasang->toArray());
        Log::info('ClosingKategori detail:', $closingKategori);

        return view('dashboard.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,

            'tahun' => $tahun,
            'bulan' => $bulan,
            'availableYears' => $availableYears,
            'bulanList' => $bulanList,

            'jumlahInteraksi' => $jumlahInteraksi,
            'prosesSurvey' => $prosesSurvey,
            'prosesPasang' => $prosesPasang,
            'prosesOrder' => $prosesOrder,

            // kirim ke view
            'jumlahAsk' => $jumlahAsk,
            'jumlahFollowUp' => $jumlahFollowUp,
            'jumlahHold' => $jumlahHold,
            'jumlahClosing' => $jumlahClosing,
            'jumlahProdukAsk' => $jumlahProdukAsk,
            'jumlahProdukHold' => $jumlahProdukHold,
            'jumlahProdukClosing' => $jumlahProdukClosing,

            'chartLabels' => $chartLabels,
            'dataLeadsLama' => $dataLeadsLama,
            'dataLeadsBaru' => $dataLeadsBaru,

            'kategoriLabels' => $kategoriLabels,
            'dataAsk' => $dataAsk,
            'dataHold' => $dataHold,
            'dataClosing' => $dataClosing,
        ]);
    }


    public function ask(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        $query = InteraksiModel::with('customer')
            ->where('status', 'ask')
            ->whereYear('tanggal_chat', $tahun);

        if ($bulan) {
            $query->whereMonth('tanggal_chat', $bulan);
        }

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_kode', fn($row) => $row->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->customer->customer_nama ?? '-')
                ->addColumn('aksi', function ($row) {
                    $url = route('rekap.show_ajax', $row->interaksi_id);
                    return '<button onclick="modalAction(\'' . $url . '\')" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Detail
                        </button>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        $activeMenu = 'dashboard';
        return view('dashboard.ask', compact('tahun', 'bulan', 'activeMenu'));
    }
    // RekapController.php

    public function followup(Request $request)
    {
        $activeMenu = 'followup';
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        $query = InteraksiModel::with('customer')
            ->where('status', 'follow up')
            ->whereYear('tanggal_chat', $tahun);

        if ($bulan) {
            $query->whereMonth('tanggal_chat', $bulan);
        }

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_kode', fn($row) => $row->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->customer->customer_nama ?? '-')
                ->addColumn('aksi', function ($row) {
                    $url = route('rekap.show_ajax', $row->interaksi_id);
                    return '<button onclick="modalAction(\'' . $url . '\')" 
                            class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Detail
                        </button>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        return view('dashboard.followup', compact('activeMenu', 'tahun', 'bulan'));
    }
    public function hold(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        $query = InteraksiModel::with('customer')
            ->where('status', 'hold')
            ->whereYear('tanggal_chat', $tahun);

        if ($bulan) {
            $query->whereMonth('tanggal_chat', $bulan);
        }

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_kode', fn($row) => $row->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->customer->customer_nama ?? '-')
                ->addColumn('aksi', function ($row) {
                    $url = route('rekap.show_ajax', $row->interaksi_id);
                    return '<button onclick="modalAction(\'' . $url . '\')" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Detail
                        </button>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        $activeMenu = 'dashboard';
        return view('dashboard.hold', compact('tahun', 'bulan', 'activeMenu'));
    }
    public function closing(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        $query = InteraksiModel::with('customer')
            ->where('status', 'closing')
            ->whereYear('tanggal_chat', $tahun);

        if ($bulan) {
            $query->whereMonth('tanggal_chat', $bulan);
        }

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_kode', fn($row) => $row->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->customer->customer_nama ?? '-')
                ->addColumn('aksi', function ($row) {
                    $url = route('rekap.show_ajax', $row->interaksi_id);
                    return '<button onclick="modalAction(\'' . $url . '\')" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Detail
                        </button>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        $activeMenu = 'dashboard';
        return view('dashboard.closing', compact('tahun', 'bulan', 'activeMenu'));
    }
}
