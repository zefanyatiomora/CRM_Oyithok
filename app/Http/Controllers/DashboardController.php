<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\InteraksiModel;
use App\Models\PasangKirimModel;
use App\Models\RincianModel;
use App\Models\KategoriModel;
use App\Models\SurveyModel;
use App\Models\InteraksiAwalModel;
use App\Models\InteraksiRealtime;
use App\Helpers\FormatHelper;
use App\Models\CustomersModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'dashboard';
        $breadcrumb = (object) [
            'title' => 'Selamat Datang, Wallpaper Malang ID',
            'list'  => ['Home', 'Dashboard']
        ];
        $page = (object) ['title' => 'Selamat Datang, Wallpaper Indonesia ID!'];

        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        // -------------------------
        // 1) GLOBAL COUNTS (PAKAI FILTER)
        // -------------------------
// CARD STATUS TERFILTER
$jumlahGhost = InteraksiModel::where('status', 'ghost')
    ->when($tahun, fn($q) => $q->whereYear('tanggal_chat', $tahun))
    ->when($bulan, fn($q) => $q->whereMonth('tanggal_chat', $bulan))
    ->count();

$jumlahAsk = InteraksiModel::where('status', 'ask')
    ->when($tahun, fn($q) => $q->whereYear('tanggal_chat', $tahun))
    ->when($bulan, fn($q) => $q->whereMonth('tanggal_chat', $bulan))
    ->count();

$jumlahFollowUp = InteraksiModel::whereIn('status', ['followup', 'follow up'])
    ->when($tahun, fn($q) => $q->whereYear('tanggal_chat', $tahun))
    ->when($bulan, fn($q) => $q->whereMonth('tanggal_chat', $bulan))
    ->count();

$jumlahHold = InteraksiModel::where('status', 'hold')
    ->when($tahun, fn($q) => $q->whereYear('tanggal_chat', $tahun))
    ->when($bulan, fn($q) => $q->whereMonth('tanggal_chat', $bulan))
    ->count();

$jumlahClosing = InteraksiModel::where('status', 'closing')
    ->when($tahun, fn($q) => $q->whereYear('tanggal_chat', $tahun))
    ->when($bulan, fn($q) => $q->whereMonth('tanggal_chat', $bulan))
    ->count();

        // -------------------------
        // 2) QUERY BASE UNTUK DATA YANG MEMBUTUHKAN FILTER (YEAR/MONTH)
        // -------------------------
        $queryBase = InteraksiModel::whereYear('tanggal_chat', $tahun);
        if ($bulan) {
            $queryBase->whereMonth('tanggal_chat', $bulan);
        }

        // Jumlah interaksi mengikuti filter (jumlah interaksi di tahun/bulan yang dipilih)
        $jumlahInteraksi = (clone $queryBase)->count();

        // Doughnut (customer) — harus mengikuti filter
        $customerDoughnut = $this->getCustomerDoughnutData($tahun, $bulan);

        // Dropdown tahun yang tersedia
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

// === Leads Baru vs Lama (mengikuti filter) ===
$chartLabels   = [];
$dataLeadsBaru = [];
$dataLeadsLama = [];
$totalLeadsBaru = 0;
$totalLeadsLama = 0;

// Tracking customer yang sudah dihitung dalam bulan ini
$customerSudahDihitungBulanIni = [];

if (!$bulan) {

    $chartLabels = ['Tidak ada lead'];
    $dataLeadsBaru = [0];
    $dataLeadsLama = [0];

} else {

    $jumlahHari = Carbon::create($tahun, $bulan)->daysInMonth;

    for ($hari = 1; $hari <= $jumlahHari; $hari++) {

        $chartLabels[] = $hari;

        // Ambil customer unik pada hari ini
        $customerHariIni = DB::table('interaksi_realtime as ir')
            ->join('interaksi as i', 'ir.interaksi_id', '=', 'i.interaksi_id')
            ->whereYear('ir.tanggal', $tahun)
            ->whereMonth('ir.tanggal', $bulan)
            ->whereDay('ir.tanggal', $hari)
            ->pluck('i.customer_id')
            ->unique();

        $leadsBaruHariIni = 0;
        $leadsLamaHariIni = 0;

        foreach ($customerHariIni as $cid) {

            // Jika customer ini sudah dihitung di hari sebelumnya IG bulan yang sama → skip
            if (in_array($cid, $customerSudahDihitungBulanIni)) {
                continue;
            }

            // Ambil tanggal interaksi pertama sepanjang sejarah
            $firstInteraksi = DB::table('interaksi')
                ->where('customer_id', $cid)
                ->orderBy('tanggal_chat', 'asc')
                ->value('tanggal_chat');

            if ($firstInteraksi) {
                $firstDate = Carbon::parse($firstInteraksi);

                if ($firstDate->year == $tahun && $firstDate->month == $bulan) {
                    $leadsBaruHariIni++;
                } else {
                    $leadsLamaHariIni++;
                }
            }

            // Tambahkan ke daftar monthly tracking
            $customerSudahDihitungBulanIni[] = $cid;
        }

        $dataLeadsBaru[] = $leadsBaruHariIni;
        $dataLeadsLama[] = $leadsLamaHariIni;
    }

    $totalLeadsBaru = array_sum($dataLeadsBaru);
    $totalLeadsLama = array_sum($dataLeadsLama);
}

        // Produk chart (mengikuti filter)
        $produkChart = $this->getProdukChartData($tahun, $bulan);

        // Filter hanya kategori dengan closing > 0
        $penjualanData = collect($produkChart['closingKategori'])->filter(fn($v) => $v > 0);
        $doughnutLabels = $penjualanData->keys()->values()->all();
        $doughnutData   = $penjualanData->values()->all();

        $doughnutColors = [
            '#5C54AD',
            '#6690FF',
            '#A374FF',
            '#FF7373',
            '#A26360',
            '#D4A29C',
            '#E8B298',
            '#C6A0D4',
            '#BDE1B3',
            '#8DD6E2',
        ];

        $rateClosing = $this->getRateClosingData($tahun, $bulan ?? date('m'));
        $kategoriMingguan = $this->getKategoriMingguanData($tahun, $bulan);

        // -------------------------
        // KIRIM KE VIEW
        // - jumlahGhost / jumlahAsk / jumlahFollowUp / jumlahHold / jumlahClosing
        //   => sengaja kita kirim sebagai GLOBAL COUNTS (tidak terfilter) untuk card status
        // - customerDoughnut* => data yang terfilter untuk chart doughnut
        // -------------------------
        $viewData = [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,

            'tahun' => $tahun,
            'bulan' => $bulan,
            'availableYears' => $availableYears,
            'bulanList' => $bulanList,

            // jumlah interaksi mengikuti filter
            'jumlahInteraksi' => $jumlahInteraksi,

            // ------------------
            // CARD STATUS: GLOBAL (TANPA FILTER)
            // ------------------
            'jumlahGhost' => $jumlahGhost,
            'jumlahAsk' => $jumlahAsk,
            'jumlahFollowUp' => $jumlahFollowUp,
            'jumlahHold' => $jumlahHold,
            'jumlahClosing' => $jumlahClosing,

            // leads / charts (mengikuti filter)
            'chartLabels' => $chartLabels,
            'dataLeadsLama' => $dataLeadsLama,
            'dataLeadsBaru' => $dataLeadsBaru,
            'totalLeadsBaru' => $totalLeadsBaru,
            'totalLeadsLama' => $totalLeadsLama,

            'kategoriLabels' => $produkChart['kategoriLabels'],
            'dataAsk' => $produkChart['dataAsk'],
            'dataHold' => $produkChart['dataHold'],
            'dataClosing' => $produkChart['dataClosing'],
            'jumlahProdukAsk' => $produkChart['jumlahProdukAsk'],
            'jumlahProdukHold' => $produkChart['jumlahProdukHold'],
            'jumlahProdukClosing' => $produkChart['jumlahProdukClosing'],

            // Doughnut customer (FOLLOW FILTER)
            'customerDoughnutLabels' => $customerDoughnut['customerDoughnutLabels'],
            'customerDoughnutData'   => $customerDoughnut['customerDoughnutData'],
            'customerDoughnutColors' => $customerDoughnut['customerDoughnutColors'],

            // Produk doughnut
            'doughnutLabels' => $doughnutLabels,
            'doughnutData' => $doughnutData,
            'doughnutColors' => array_slice($doughnutColors, 0, count($doughnutLabels)),

            'rateClosingLabels' => $rateClosing['rateClosingLabels'],
            'rateClosingDatasets' => $rateClosing['rateClosingDatasets'],
            // TAMBAHKAN DATA BARU INI UNTUK VIEW
            'kategoriMingguanLabels' => $kategoriMingguan['labels'],
            'kategoriMingguanCounts' => $kategoriMingguan['counts'],
            'kategoriMingguanNames' => $kategoriMingguan['kategoriNames'],
            'kategoriMingguanColors' => $kategoriMingguan['kategoriColors'],
            // 'kategoriMingguanMaxY' => $kategoriMingguan['maxYAxis'],
        ];
        Log::info('DEBUG INDEX: Data final yang dikirim ke view', $viewData);
        // ================================================================

        return view('dashboard.index', $viewData);
    }

    // ===========================
    // Rate closing per minggu (menggunakan jadwal_pasang_kirim / jadwal_survey)
    // ===========================
    private function getRateClosingData($tahun, $bulan)
    {
        $startOfMonth = Carbon::create($tahun, $bulan, 1);
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $weeks = [];
        $current = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        while ($current <= $endOfMonth) {
            $weeks[] = [
                'start' => $current->copy(),
                'end'   => $current->copy()->endOfWeek(Carbon::SUNDAY),
            ];
            $current->addWeek();
        }

        $allData = [];
        $produkData = [];
        $surveyData = [];
        $pasangData = [];

        foreach ($weeks as $week) {
            $startDate = $week['start'];
            $endDate   = $week['end'];

            $allData[]    = PasangKirimModel::where('status', 'closing all')
                ->whereBetween('jadwal_pasang_kirim', [$startDate, $endDate])->count();
            $produkData[] = PasangKirimModel::where('status', 'closing produk')
                ->whereBetween('jadwal_pasang_kirim', [$startDate, $endDate])->count();
            $surveyData[] = SurveyModel::where('status', 'closing')
                ->whereBetween('jadwal_survey', [$startDate, $endDate])->count();
            $pasangData[] = PasangKirimModel::where('status', 'closing')
                ->whereBetween('jadwal_pasang_kirim', [$startDate, $endDate])->count();
        }

        $labels = ['All', 'Produk', 'Survey', 'Pasang'];
        $datasets = [];
        foreach ($weeks as $i => $week) {
            $datasets[] = [
                'label' => "Minggu " . ($i + 1),
                'data'  => [
                    $allData[$i] ?? 0,
                    $produkData[$i] ?? 0,
                    $surveyData[$i] ?? 0,
                    $pasangData[$i] ?? 0
                ],
                'borderColor' => ['#5C54AD', '#6690FF', '#A374FF', '#FF7373', '#A26360'][$i % 5],
                'backgroundColor' => 'rgba(0,0,0,0)',
                'tension' => 0.3,
                'fill' => false,
            ];
        }

        return ['rateClosingLabels' => $labels, 'rateClosingDatasets' => $datasets];
    }
    private function getProdukChartData($tahun, $bulan)
    {
        // --- DATA TERFILTER (Untuk Chart berdasarkan Tahun & Bulan) ---

        $kategoriLabels = KategoriModel::pluck('kategori_nama');

        $filterWaktu = function ($q) use ($tahun, $bulan) {
            $q->whereYear('tanggal_chat', $tahun);
            if ($bulan) {
                $q->whereMonth('tanggal_chat', $bulan);
            }
        };

        $askKategori = InteraksiAwalModel::with('kategori')
            ->whereHas('interaksi', $filterWaktu)
            ->get()
            ->groupBy(fn($item) => $item->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

        $holdKategori = RincianModel::with('produk.kategori', 'interaksi')
            ->where('status', 'hold')
            ->whereHas('interaksi', $filterWaktu)
            ->get()
            ->groupBy(fn($item) => $item->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

        $closingKategori = PasangKirimModel::with('produk.kategori', 'interaksi')
            ->whereIn('status', ['closing produk', 'closing pasang', 'closing all'])
            ->whereHas('interaksi', $filterWaktu)
            ->get()
            ->groupBy(fn($item) => $item->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

        $dataAsk = [];
        $dataHold = [];
        $dataClosing = [];
        foreach ($kategoriLabels as $kategori) {
            $dataAsk[] = $askKategori[$kategori] ?? 0;
            $dataHold[] = $holdKategori[$kategori] ?? 0;
            $dataClosing[] = $closingKategori[$kategori] ?? 0;
        }

        // --- DATA TOTAL TANPA FILTER (Untuk Summary Card) ---

        // ✅ MENGHITUNG JUMLAH INTERAKSI YANG MEMILIKI PERTANYAAN PRODUK
        $jumlahProdukAskTotal = InteraksiModel::whereHas('interaksi_awal')->count();

        // Menghitung jumlah total produk yang di-hold (tanpa filter waktu)
        $jumlahProdukHoldTotal = RincianModel::where('status', 'hold')->count();

        // Menghitung jumlah total produk yang closing (tanpa filter waktu)
        $jumlahProdukClosingTotal = PasangKirimModel::whereIn('status', ['closing produk', 'closing pasang', 'closing all'])->count();


        // --- RETURN SEMUA DATA ---
        return [
            // Data untuk chart bar (sudah terfilter)
            'kategoriLabels' => $kategoriLabels,
            'dataAsk' => $dataAsk,
            'dataHold' => $dataHold,
            'dataClosing' => $dataClosing,

            // Data untuk doughnut chart (sudah terfilter)
            'closingKategori' => $closingKategori,

            // Data untuk summary card (total tanpa filter)
            // 'jumlahProdukAsk' => $jumlahProdukAskTotal,
            // 'jumlahProdukHold' => $jumlahProdukHoldTotal,
            // 'jumlahProdukClosing' => $jumlahProdukClosingTotal,

            // Data untuk summary card (total di filter)
            'jumlahProdukAsk' => array_sum($dataAsk),
            'jumlahProdukHold' => array_sum($dataHold),
            'jumlahProdukClosing' => array_sum($dataClosing),
        ];
    }

    // ===========================
    // Customer doughnut (mengikuti filter tahun/bulan)
    // ===========================
    private function getCustomerDoughnutData($tahun, $bulan = null)
    {
        $queryBase = InteraksiModel::whereYear('tanggal_chat', $tahun);
        if ($bulan) {
            $queryBase->whereMonth('tanggal_chat', $bulan);
        }

        $jumlahGhost = (clone $queryBase)->where('status', 'ghost')->count();
        $jumlahAsk = (clone $queryBase)->where('status', 'ask')->count();
        $jumlahFollowUp = (clone $queryBase)->whereIn('status', ['followup', 'follow up'])->count();
        $jumlahHold = (clone $queryBase)->where('status', 'hold')->count();
        $jumlahClosing = (clone $queryBase)->where('status', 'closing')->count();

        return [
            'customerDoughnutLabels' => ['Ghost', 'Ask', 'Follow up', 'Hold', 'Closing'],
            'customerDoughnutData'   => [$jumlahGhost, $jumlahAsk, $jumlahFollowUp, $jumlahHold, $jumlahClosing],
            'customerDoughnutColors' => ['#9a9d9eff', '#87CEEB', '#A374FF', '#5C54AD', '#FF7373'],
        ];
    }


    private function getKategoriMingguanData($tahun, $bulan)
    {
        if (!$bulan) {
            return [
                'labels'        => [],
                'counts'        => [],
                'kategoriNames' => [],
                'kategoriColors' => [],
                'maxYAxis'      => 5 // <-- TAMBAHKAN INI
            ];
        }

        $startOfMonth = Carbon::create($tahun, $bulan, 1);
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $weeks = [];
        $current = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        while ($current <= $endOfMonth) {
            $weeks[] = [
                'start' => $current->copy(),
                'end'   => $current->copy()->endOfWeek(Carbon::SUNDAY),
            ];
            $current->addWeek();
        }

        $colorPalette = ['#5C54AD', '#6690FF', '#A374FF', '#FF7373', '#A26360', '#D4A29C', '#E8B298', '#C6A0D4', '#BDE1B3', '#8DD6E2'];
        $categoryColorMap = [];
        $colorIndex = 0;

        $topKategoriPerMinggu = [];

        foreach ($weeks as $week) {
            $startDate = $week['start'];
            $endDate   = $week['end'];

            // --- PERUBAHAN UTAMA ADA DI SINI ---
            $topKategori = InteraksiAwalModel::query()
                ->whereHas('interaksi', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('tanggal_chat', [$startDate, $endDate]);
                })
                // Mengganti DB::raw dengan selectRaw
                ->selectRaw('kategori_nama, COUNT(*) as total')
                ->groupBy('kategori_nama')
                // Menggunakan orderByDesc agar lebih ringkas
                ->orderByDesc('total')
                ->first();

            if ($topKategori) {
                $kategoriNama = $topKategori->kategori_nama;

                if (!isset($categoryColorMap[$kategoriNama])) {
                    $categoryColorMap[$kategoriNama] = $colorPalette[$colorIndex % count($colorPalette)];
                    $colorIndex++;
                }

                $topKategoriPerMinggu[] = [
                    'nama'  => $kategoriNama,
                    'total' => $topKategori->total,
                    'color' => $categoryColorMap[$kategoriNama]
                ];
            } else {
                $topKategoriPerMinggu[] = [
                    'nama'  => 'Tidak ada data',
                    'total' => 0,
                    'color' => '#E0E0E0'
                ];
            }
        }

        $chartLabels = [];
        $chartCounts = [];
        $chartKategoriNames = [];
        $chartKategoriColors = [];

        foreach ($topKategoriPerMinggu as $i => $data) {
            $chartLabels[] = "Minggu " . ($i + 1);
            $chartCounts[] = $data['total'];
            $chartKategoriNames[] = $data['nama'];
            $chartKategoriColors[] = $data['color'];
        }

        $maxCount = !empty($chartCounts) ? max($chartCounts) : 0;
        $maxYAxis = $maxCount < 5 ? 5 : $maxCount + ceil($maxCount * 0.2);

        $returnData = [
            'labels'         => $chartLabels,
            'counts'         => $chartCounts,
            'kategoriNames'  => $chartKategoriNames,
            'kategoriColors' => $chartKategoriColors,
            'maxYAxis'       => $maxYAxis,
        ];
        Log::info('DEBUG CHART: Data yang dikirim ke view', $returnData);

        return $returnData;
    }

    // ... (sisa method di controller Anda)
    public function askIndex(Request $request)
    {
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');
        $activeMenu = 'dashboard';

        return view('dashboardproduk.ask.index', compact('activeMenu', 'tahun', 'bulan'));
    }
    public function holdIndex(Request $request)
    {
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');
        $activeMenu = 'dashboard';

        return view('dashboardproduk.hold.index', compact('activeMenu', 'tahun', 'bulan'));
    }
    public function closingIndex(Request $request)
    {
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');
        $activeMenu = 'dashboard';

        return view('dashboardproduk.closing.index', compact('activeMenu', 'tahun', 'bulan'));
    }

    public function askProduk(Request $request)
    {
        Log::info('--- Method askProduk() Dipanggil ---', [
            'url' => $request->fullUrl(),
            'ajax' => $request->ajax()
        ]);

        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        Log::info('Parameter Filter Diterima (askProduk):', [
            'tahun_filter' => $tahun,
            'bulan_filter' => $bulan,
        ]);

        $queryBuilder = InteraksiAwalModel::with(['kategori', 'interaksi.customer']);

        $queryBuilder->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
            if ($tahun) $q->whereYear('tanggal_chat', $tahun);
            if ($bulan) $q->whereMonth('tanggal_chat', $bulan);
        });

        if ($request->ajax()) {
            $results = $queryBuilder->get();

            return DataTables::of($results)
                ->addIndexColumn()
                ->addColumn('customer_kode', fn($row) => $row->interaksi->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->interaksi->customer->customer_nama ?? '-')
                ->addColumn('kategori_nama', fn($row) => $row->kategori->kategori_nama ?? 'Tanpa Kategori')
                ->addColumn('aksi', function ($row) {
                    if (isset($row->interaksi_id)) {
                        $url = route('rekap.show_ajax', $row->interaksi_id);
                        return '<button onclick="modalAction(\'' . $url . '\')" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Detail
                    </button>';
                    }
                    return 'N/A';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }


    public function holdProduk(Request $request)
    {
        // Log 1: Memastikan method ini terpanggil.
        Log::info('--- Method holdProduk() Dipanggil ---', [
            'url' => $request->fullUrl(),
            'ajax' => $request->ajax()
        ]);

        // Ambil parameter filter dari request
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        // Query builder dasar
        $queryBuilder = RincianModel::with(['interaksi.customer', 'produk.kategori'])
            ->where('status', 'hold');

        // Terapkan filter waktu pada relasi 'interaksi'
        $queryBuilder->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
            if ($tahun) {
                $q->whereYear('tanggal_chat', $tahun);
            }
            if ($bulan) {
                $q->whereMonth('tanggal_chat', $bulan);
            }
        });

        // Proses AJAX untuk DataTables
        if ($request->ajax()) {
            $results = $queryBuilder->get();
            return DataTables::of($results)
                ->addIndexColumn()
                ->addColumn('customer_kode', fn($row) => $row->interaksi->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->interaksi->customer->customer_nama ?? '-')
                ->addColumn('produk_nama', fn($row) => $row->produk->produk_nama ?? 'Tanpa Produk')
                ->addColumn('kategori_nama', fn($row) => $row->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
                ->addColumn('aksi', function ($row) {
                    if (isset($row->interaksi->interaksi_id)) {
                        $url = route('rekap.show_ajax', $row->interaksi->interaksi_id);
                        return '<button onclick="modalAction(\'' . $url . '\')" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> Detail
                        </button>';
                    }
                    return 'N/A';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function closingProduk(Request $request)
    {
        // Log 1: Memastikan method ini terpanggil.
        Log::info('--- Method closingProduk() Dipanggil ---', [
            'url' => $request->fullUrl(),
            'ajax' => $request->ajax()
        ]);

        // Ambil parameter filter dari request
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');

        // Query builder dasar
        $queryBuilder = PasangKirimModel::with(['interaksi.customer', 'produk.kategori'])
            ->whereIn('status', ['closing produk', 'closing pasang', 'closing all']);

        // Terapkan filter waktu pada relasi 'interaksi'
        $queryBuilder->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
            if ($tahun) {
                $q->whereYear('tanggal_chat', $tahun);
            }
            if ($bulan) {
                $q->whereMonth('tanggal_chat', $bulan);
            }
        });

        // Proses AJAX untuk DataTables
        if ($request->ajax()) {
            $results = $queryBuilder->get();
            return DataTables::of($results)
                ->addIndexColumn()
                ->addColumn('customer_kode', fn($row) => $row->interaksi->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->interaksi->customer->customer_nama ?? '-')
                ->addColumn('produk_nama', fn($row) => $row->produk->produk_nama ?? 'Tanpa Produk')
                ->addColumn('kategori_nama', fn($row) => $row->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
                ->addColumn('aksi', function ($row) {
                    if (isset($row->interaksi->interaksi_id)) {
                        $url = route('rekap.show_ajax', $row->interaksi->interaksi_id);
                        return '<button onclick="modalAction(\'' . $url . '\')" class="btn btn-sm btn-success">
                            <i class="fas fa-eye"></i> Detail
                        </button>';
                    }
                    return 'N/A';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }
    // ===========================
    // Route endpoints untuk setiap status
    // (tidak di-filter di sini — DataTables akan menampilkan semua record dengan status tersebut)
    // ===========================
public function ghost(Request $request)
{
    $bulan = $request->input('bulan');
    $tahun = $request->input('tahun');

    Log::info('🎯 Filter diterima di ghost():', ['bulan' => $bulan, 'tahun' => $tahun]);

    // Query dasar
    $query = InteraksiModel::with('customer')
        ->where('status', 'ghost')
        ->when($bulan, function ($q) use ($bulan) {
            $q->whereMonth('tanggal_chat', $bulan);
        })
        ->when($tahun, function ($q) use ($tahun) {
            $q->whereYear('tanggal_chat', $tahun);
        })
        ->orderBy('tanggal_chat', 'desc');

    if ($request->ajax()) {
        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('customer_kode', fn($row) => $row->customer->customer_kode ?? '-')
            ->addColumn('customer_nama', fn($row) => $row->customer->customer_nama ?? '-')
            ->addColumn('aksi', function ($row) {
                $urlDetail = route('rekap.show_ajax', $row->interaksi_id);
                $urlBroadcast = route('ghost.broadcast.single', $row->customer->customer_kode ?? '');
                $nama = e($row->customer->customer_nama ?? '-');

                return '
                    <div class="btn-group" role="group">
                        <button onclick="modalAction(\'' . $urlDetail . '\')" 
                            class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        <button onclick="openBroadcastModal(\'' . $urlBroadcast . '\', \'' . $nama . '\')" 
                            class="btn btn-sm btn-dark">
                            <i class="fas fa-paper-plane"></i> Broadcast
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
  $tahunList = InteraksiModel::selectRaw('YEAR(tanggal_chat) as tahun')
        ->where('status', 'ghost')
        ->distinct()
        ->pluck('tahun');

    $bulanList = collect(range(1, 12))->mapWithKeys(function ($m) {
        return [$m => \Carbon\Carbon::create()->month($m)->translatedFormat('F')];
    });

   return view('dashboard.ghost', [
    'tahunList' => $tahunList,
    'bulanList' => $bulanList,
    'activeMenu' => 'ghost'
]);
}
    public function ask(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

    $query = InteraksiModel::with('customer')
        ->where('status', 'ask')
        ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
        ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun));

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_kode', fn($row) => $row->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->customer->customer_nama ?? '-')
                ->addColumn('aksi', function ($row) {
                    $urlDetail = route('rekap.show_ajax', $row->interaksi_id);
                    $broadcastUrl = route('ask.broadcastCustomer', $row->interaksi_id);
                    $nama = e($row->customer->customer_nama ?? '-');

                    return '
        <div class="btn-group" role="group">
            <button onclick="modalAction(\'' . $urlDetail . '\')" 
                class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> Detail
            </button>

            <button onclick="openBroadcastModal(\'' . $broadcastUrl . '\', \'' . $nama . '\')" 
                class="btn btn-sm btn-dark">
                <i class="fas fa-paper-plane"></i> Broadcast
            </button>
        </div>
    ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
        $activeMenu = 'dashboard';
        return view('dashboard.ask', compact('activeMenu'));
    }
    public function followup(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

    $query = InteraksiModel::with('customer')
        ->whereIn('status', ['followup', 'follow up'])
        ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
        ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun));

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_kode', fn($row) => $row->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->customer->customer_nama ?? '-')
                ->addColumn('aksi', function ($row) {
                    $urlDetail = route('rekap.show_ajax', $row->interaksi_id);
                    $broadcastUrl = route('followup.broadcastCustomer', $row->interaksi_id);
                    $nama = e($row->customer->customer_nama ?? '-');

                    return '
        <div class="btn-group" role="group">
            <button onclick="modalAction(\'' . $urlDetail . '\')" 
                class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> Detail
            </button>

            <button onclick="openBroadcastModal(\'' . $broadcastUrl . '\', \'' . $nama . '\')" 
                class="btn btn-sm btn-dark">
                <i class="fas fa-paper-plane"></i> Broadcast
            </button>
        </div>
    ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }
        $activeMenu = 'dashboard';
        return view('dashboard.followup', compact('activeMenu'));
    }
    public function hold(Request $request)
    {
 $bulan = $request->bulan;
    $tahun = $request->tahun;

    $query = InteraksiModel::with('customer')
        ->whereIn('status', ['hold'])
        ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
        ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun));

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_kode', fn($row) => $row->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->customer->customer_nama ?? '-')
                ->addColumn('aksi', function ($row) {
                    $urlDetail = route('rekap.show_ajax', $row->interaksi_id);
                    $urlBroadcast = route('hold.broadcast.single', $row->interaksi_id);
                    $nama = e($row->customer->customer_nama ?? '-');

                    return '
        <div class="btn-group" role="group">
            <button onclick="modalAction(\'' . $urlDetail . '\')" 
                class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> Detail
            </button>

            <button onclick="openBroadcastModal(\'' . $urlBroadcast . '\', \'' . $nama . '\')" 
                class="btn btn-sm btn-dark">
                <i class="fas fa-paper-plane"></i> Broadcast
            </button>
        </div>
    ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        $activeMenu = 'dashboard';
        return view('dashboard.hold', compact('activeMenu'));
    }

    public function closing(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;

    $query = InteraksiModel::with('customer')
        ->whereIn('status', ['closing'])
        ->when($bulan, fn($q) => $q->whereMonth('created_at', $bulan))
        ->when($tahun, fn($q) => $q->whereYear('created_at', $tahun));

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_kode', fn($row) => $row->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->customer->customer_nama ?? '-')
                ->addColumn('aksi', function ($row) {
                    $urlDetail = route('rekap.show_ajax', $row->interaksi_id);
                    $urlBroadcast = route('closing.broadcast.single', $row->interaksi_id);
                    $nama = e($row->customer->customer_nama ?? '-');

                    return '
        <div class="btn-group" role="group">
            <button onclick="modalAction(\'' . $urlDetail . '\')" 
                class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> Detail
            </button>

            <button onclick="openBroadcastModal(\'' . $urlBroadcast . '\', \'' . $nama . '\')" 
                class="btn btn-sm btn-dark">
                <i class="fas fa-paper-plane"></i> Broadcast
            </button>
        </div>
    ';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        $activeMenu = 'dashboard';
        return view('dashboard.closing', compact('activeMenu'));
    }
    public function ghostBroadcast()
    {
        return view('broadcast.ghost_broadcast');
    }
    public function sendGhostSingle(Request $request, $kode)
    {
        $token = env('FONNTE_TOKEN');

        $customer = CustomersModel::where('customer_kode', $kode)->first();

        if (!$customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer tidak ditemukan.'
            ]);
        }

        $nohp = preg_replace('/\D/', '', $customer->customer_nohp);
        if (substr($nohp, 0, 1) === '0') {
            $nohp = '62' . substr($nohp, 1);
        } elseif (substr($nohp, 0, 2) !== '62') {
            $nohp = '62' . $nohp;
        }
        $pesan = $request->input('pesan');

        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $nohp,
                'message' => $pesan,
            ]);

            $result = $response->json();

            if (isset($result['status']) && $result['status'] == true) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Pesan berhasil dikirim ke {$customer->customer_nama}"
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Gagal mengirim pesan ke {$customer->customer_nama}: " . ($result['reason'] ?? 'Tidak diketahui')
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ]);
        }
    }
    public function sendAskSingle(Request $request, $id)
    {
        $interaksi = InteraksiModel::with('customer')->find($id);
        if (!$interaksi || !$interaksi->customer) {
            return response()->json(['status' => 'error', 'message' => 'Customer tidak ditemukan']);
        }

        $customer = $interaksi->customer;
        $nama = $customer->customer_nama;

        // Normalisasi nomor HP
        $nohp = preg_replace('/\D/', '', $customer->customer_nohp);
        if (substr($nohp, 0, 1) === '0') {
            $nohp = '62' . substr($nohp, 1);
        } elseif (substr($nohp, 0, 2) !== '62') {
            $nohp = '62' . $nohp;
        }

        $pesan = $request->input('pesan');
        $token = env('FONNTE_TOKEN');

        try {
            // Kirim request ke API Fonnte
            $response = Http::withHeaders([
                'Authorization' => $token, // tanpa "Bearer "
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $nohp,
                'message' => $pesan,
            ]);

            $result = $response->json();
            Log::info("Broadcast ASK -> {$nohp}", $result);

            if (isset($result['status']) && $result['status'] === true) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Pesan berhasil dikirim ke {$nama}"
                ]);
            } else {
                $errorMsg = $result['reason'] ?? ($result['message'] ?? 'Unknown error');
                return response()->json([
                    'status' => 'error',
                    'message' => "Gagal mengirim pesan ke {$nama}: {$errorMsg}"
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Exception kirim WA ke {$nohp}: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ]);
        }
    }
    public function sendFollowUpSingle(Request $request, $id)
    {
        // Ambil token dari .env
        $token = env('FONNTE_TOKEN');

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token Fonnte tidak ditemukan. Pastikan sudah diatur di file .env dan cache sudah dibersihkan.'
            ]);
        }

        $pesan = $request->input('pesan');

        // ✅ Cari interaksi berdasarkan ID
        $interaksi = \App\Models\InteraksiModel::with('customer')->find($id);

        if (!$interaksi || !$interaksi->customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer tidak ditemukan dari data interaksi.'
            ]);
        }

        // ✅ Ambil data customer
        $customer = $interaksi->customer;

        // ✅ Normalisasi nomor HP ke format 62
        $nohp = preg_replace('/\D/', '', $customer->customer_nohp);
        if (substr($nohp, 0, 1) === '0') {
            $nohp = '62' . substr($nohp, 1);
        } elseif (substr($nohp, 0, 2) !== '62') {
            $nohp = '62' . $nohp;
        }

        try {
            // ✅ Kirim ke Fonnte
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $nohp,
                'message' => $pesan,
            ]);

            $result = $response->json();

            // ✅ Cek hasil
            if (isset($result['detail']) && str_contains(strtolower($result['detail']), 'success')) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Pesan berhasil dikirim ke {$customer->customer_nama})"
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Gagal mengirim pesan ke {$customer->customer_nama}: " . ($result['detail'] ?? 'Tidak diketahui'),
                    'response' => $result
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ]);
        }
    }
    public function sendHoldSingle(Request $request, $id)
    {
        // Ambil token dari .env
        $token = env('FONNTE_TOKEN');

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token Fonnte tidak ditemukan. Pastikan sudah diatur di file .env dan cache sudah dibersihkan.'
            ]);
        }

        $pesan = $request->input('pesan');

        // ✅ Cari interaksi berdasarkan ID
        $interaksi = \App\Models\InteraksiModel::with('customer')->find($id);

        if (!$interaksi || !$interaksi->customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer tidak ditemukan dari data interaksi.'
            ]);
        }

        // ✅ Ambil data customer
        $customer = $interaksi->customer;

        // ✅ Normalisasi nomor HP ke format 62
        $nohp = preg_replace('/\D/', '', $customer->customer_nohp);
        if (substr($nohp, 0, 1) === '0') {
            $nohp = '62' . substr($nohp, 1);
        } elseif (substr($nohp, 0, 2) !== '62') {
            $nohp = '62' . $nohp;
        }

        try {
            // ✅ Kirim ke Fonnte
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $nohp,
                'message' => $pesan,
            ]);

            $result = $response->json();

            // ✅ Cek hasil
            if (isset($result['detail']) && str_contains(strtolower($result['detail']), 'success')) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Pesan berhasil dikirim ke {$customer->customer_nama})"
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Gagal mengirim pesan ke {$customer->customer_nama}: " . ($result['detail'] ?? 'Tidak diketahui'),
                    'response' => $result
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ]);
        }
    }
    public function sendClosingSingle(Request $request, $id)
    {
        // Ambil token dari .env
        $token = env('FONNTE_TOKEN');

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token Fonnte tidak ditemukan. Pastikan sudah diatur di file .env dan cache sudah dibersihkan.'
            ]);
        }

        $pesan = $request->input('pesan');

        // ✅ Cari interaksi berdasarkan ID
        $interaksi = \App\Models\InteraksiModel::with('customer')->find($id);

        if (!$interaksi || !$interaksi->customer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Customer tidak ditemukan dari data interaksi.'
            ]);
        }

        // ✅ Ambil data customer
        $customer = $interaksi->customer;

        // ✅ Normalisasi nomor HP ke format 62
        $nohp = preg_replace('/\D/', '', $customer->customer_nohp);
        if (substr($nohp, 0, 1) === '0') {
            $nohp = '62' . substr($nohp, 1);
        } elseif (substr($nohp, 0, 2) !== '62') {
            $nohp = '62' . $nohp;
        }

        try {
            // ✅ Kirim ke Fonnte
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post('https://api.fonnte.com/send', [
                'target' => $nohp,
                'message' => $pesan,
            ]);

            $result = $response->json();

            // ✅ Cek hasil
            if (isset($result['detail']) && str_contains(strtolower($result['detail']), 'success')) {
                return response()->json([
                    'status' => 'success',
                    'message' => "Pesan berhasil dikirim ke {$customer->customer_nama})"
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => "Gagal mengirim pesan ke {$customer->customer_nama}: " . ($result['detail'] ?? 'Tidak diketahui'),
                    'response' => $result
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ]);
        }
    }
}
