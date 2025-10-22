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
        // 1) GLOBAL COUNTS (TANPA FILTER)
        // -------------------------
        // Card status harus menunjukkan total seluruh data (tidak terfilter)
        $jumlahGhostGlobal    = InteraksiModel::where('status', 'ghost')->count();
        $jumlahAskGlobal      = InteraksiModel::where('status', 'ask')->count();
        $jumlahFollowUpGlobal = InteraksiModel::whereIn('status', ['followup', 'follow up'])->count();
        $jumlahHoldGlobal     = InteraksiModel::where('status', 'hold')->count();
        $jumlahClosingGlobal  = InteraksiModel::where('status', 'closing')->count();

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

        if ($bulan) {
            $jumlahHari = Carbon::create($tahun, $bulan)->daysInMonth;
            for ($hari = 1; $hari <= $jumlahHari; $hari++) {
                $chartLabels[] = $hari;

                $customerHariIni = DB::table('interaksi_realtime as ir')
                    ->join('interaksi as i', 'ir.interaksi_id', '=', 'i.interaksi_id')
                    ->whereYear('ir.tanggal', $tahun)
                    ->whereMonth('ir.tanggal', $bulan)
                    ->whereDay('ir.tanggal', $hari)
                    ->pluck('i.customer_id')->unique();

                $leadsBaruHariIni = 0;
                foreach ($customerHariIni as $cid) {
                    $firstInteraksi = DB::table('interaksi')
                        ->where('customer_id', $cid)
                        ->orderBy('tanggal_chat', 'asc')
                        ->value('tanggal_chat');

                    if (
                        $firstInteraksi &&
                        Carbon::parse($firstInteraksi)->year == $tahun &&
                        Carbon::parse($firstInteraksi)->month == $bulan
                    ) {
                        $leadsBaruHariIni++;
                    }
                }

                $dataLeadsBaru[] = $leadsBaruHariIni;
                $dataLeadsLama[] = count($customerHariIni) - $leadsBaruHariIni;
            }
        } else {
            foreach ($bulanList as $key => $namaBulan) {
                $chartLabels[] = $namaBulan;

                $customerBulanIni = DB::table('interaksi_realtime as ir')
                    ->join('interaksi as i', 'ir.interaksi_id', '=', 'i.interaksi_id')
                    ->whereYear('ir.tanggal', $tahun)
                    ->whereMonth('ir.tanggal', $key)
                    ->pluck('i.customer_id')->unique();

                $leadsBaruBulanIni = 0;
                foreach ($customerBulanIni as $cid) {
                    $firstInteraksi = DB::table('interaksi')
                        ->where('customer_id', $cid)
                        ->orderBy('tanggal_chat', 'asc')
                        ->value('tanggal_chat');

                    if (
                        $firstInteraksi &&
                        Carbon::parse($firstInteraksi)->year == $tahun &&
                        Carbon::parse($firstInteraksi)->month == $key
                    ) {
                        $leadsBaruBulanIni++;
                    }
                }

                $dataLeadsBaru[] = $leadsBaruBulanIni;
                $dataLeadsLama[] = count($customerBulanIni) - $leadsBaruBulanIni;
            }
        }

        $totalLeadsBaru = array_sum($dataLeadsBaru);
        $totalLeadsLama = array_sum($dataLeadsLama);

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

        // -------------------------
        // KIRIM KE VIEW
        // - jumlahGhost / jumlahAsk / jumlahFollowUp / jumlahHold / jumlahClosing
        //   => sengaja kita kirim sebagai GLOBAL COUNTS (tidak terfilter) untuk card status
        // - customerDoughnut* => data yang terfilter untuk chart doughnut
        // -------------------------
        return view('dashboard.index', [
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
            'jumlahGhost' => $jumlahGhostGlobal,
            'jumlahAsk' => $jumlahAskGlobal,
            'jumlahFollowUp' => $jumlahFollowUpGlobal,
            'jumlahHold' => $jumlahHoldGlobal,
            'jumlahClosing' => $jumlahClosingGlobal,

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
        ]);
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
        $query = InteraksiModel::with('customer')->where('status', 'ghost');

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

        $activeMenu = 'dashboard';
        return view('dashboard.ghost', compact('activeMenu'));
    }
    public function ask(Request $request)
    {
        $query = InteraksiModel::with('customer')->where('status', 'ask');

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
        $query = InteraksiModel::with('customer')->whereIn('status', ['followup', 'follow up']);

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
        $query = InteraksiModel::with('customer')->where('status', 'hold');

        if ($request->ajax()) {
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('customer_kode', fn($row) => $row->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->customer->customer_nama ?? '-')
                ->addColumn('aksi', function ($row) {
                    $urlDetail = route('rekap.show_ajax', $row->interaksi_id);
                    $urlBroadcast = route('hold.broadcast.single', $row->customer->customer_kode ?? '');
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
        $query = InteraksiModel::with('customer')->where('status', 'closing');

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
        return view('dashboard.closing', compact('activeMenu'));
    }
    public function broadcast(Request $request)
    {
        // Modal konfirmasi broadcast
        return view('broadcast.followup_broadcast');
    }
    public function askBroadcast()
    {
        return view('broadcast.ask_broadcast');
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
    
    // Format nomor otomatis biar ke format 62...
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
        public function askFollowup()
{
    return view('broadcast.ask_broadcast');
}
public function sendFollowUpSingle(Request $request, $id)
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
        // $token = env('WABLAS_TOKEN');
        $token = "URWOxNOzTNr9zlRtw6qiptAP3PeMlDFaYVnrXLSxgQs5RurmdbgnY8z";
        // $secret_key = env('WABLAS_SECRET');
        $secret_key = "KfOQexcn";

        try {
            $headers = [
                'Authorization' => 'Bearer ' . $token
            ];
            if ($secret_key) $headers['Secret'] = $secret_key;

            $response = Http::withHeaders($headers)->post('https://sby.wablas.com/api/send-message', [
                'phone'   => $nohp,
                'message' => $pesan,
            ]);

            $result = $response->json();
            Log::info("Broadcast Follow Up -> {$nohp}", $result);

            if (isset($result['status']) && $result['status'] === true) {
                return response()->json([
                    'status'  => 'success',
                    'message' => "Pesan berhasil dikirim ke {$nama}"
                ]);
            } else {
                $errorMsg = $result['message'] ?? 'Unknown error';
                return response()->json([
                    'status'  => 'error',
                    'message' => "Gagal mengirim pesan ke {$nama}: {$errorMsg}"
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Exception kirim WA ke {$nohp}: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ]);
        }
    }


    public function askHold()
    {
        return view('broadcast.ask_broadcast');
    }
    public function sendHoldSingle(Request $request, $id)
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
        $token = env('WABLAS_TOKEN');
        $secret_key = env('WABLAS_SECRET');

        try {
            $headers = [
                'Authorization' => 'Bearer ' . $token
            ];
            if ($secret_key) $headers['Secret'] = $secret_key;

            $response = Http::withHeaders($headers)->post('https://sby.wablas.com/api/send-message', [
                'phone'   => $nohp,
                'message' => $pesan,
            ]);

            $result = $response->json();
            Log::info("Broadcast Hold -> {$nohp}", $result);

            if (isset($result['status']) && $result['status'] === true) {
                return response()->json([
                    'status'  => 'success',
                    'message' => "Pesan berhasil dikirim ke {$nama}"
                ]);
            } else {
                $errorMsg = $result['message'] ?? 'Unknown error';
                return response()->json([
                    'status'  => 'error',
                    'message' => "Gagal mengirim pesan ke {$nama}: {$errorMsg}"
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Exception kirim WA ke {$nohp}: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ]);
        }
    }
}
