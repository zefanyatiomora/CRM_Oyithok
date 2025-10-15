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
            'jumlahProdukAsk' => $jumlahProdukAskTotal,
            'jumlahProdukHold' => $jumlahProdukHoldTotal,
            'jumlahProdukClosing' => $jumlahProdukClosingTotal,
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


    public function askProduk(Request $request)
    {
        // Log 1: Memastikan method ini terpanggil oleh request yang benar.
        Log::info('--- Method askProduk() Dipanggil ---', [
            'url' => $request->fullUrl(),
            'ajax' => $request->ajax()
        ]);

        // --- PERBAIKAN QUERY & EAGER LOADING ---
        // 1. Eager load relasi yang dibutuhkan: 'kategori' dan 'interaksi.customer'.
        // 2. Jangan langsung ->get() agar kita bisa log SQL mentahnya.
        $queryBuilder = InteraksiAwalModel::with(['kategori', 'interaksi.customer'])
            ->whereHas('interaksi');

        // Log 2: Menampilkan SQL query mentah dan bindings-nya. Ini SANGAT PENTING.
        // Anda bisa salin query ini dan jalankan langsung di database client (misal: phpMyAdmin).
        Log::info('SQL Query Dijalankan:', [
            'sql' => $queryBuilder->toSql(),
            'bindings' => $queryBuilder->getBindings()
        ]);

        // Eksekusi query untuk mendapatkan collection
        $results = $queryBuilder->get();

        // Log 3: Cek berapa jumlah data yang berhasil diambil dari database.
        Log::info('Jumlah data setelah query:', ['count' => $results->count()]);

        // Log 4 (Opsional): Jika data ada, tampilkan contoh data pertama untuk memeriksa strukturnya.
        if ($results->isNotEmpty()) {
            Log::info('Contoh data pertama yang didapat:', $results->first()->toArray());
        }

        if ($request->ajax()) {
            // Log 5: Memastikan program masuk ke dalam blok AJAX untuk DataTables.
            Log::info('Request adalah AJAX, memproses DataTables...');

            return DataTables::of($results) // Gunakan collection $results yang sudah dieksekusi
                ->addIndexColumn()
                // --- PERBAIKAN AKSES RELASI ---
                // Akses customer melalui relasi interaksi
                ->addColumn('customer_kode', fn($row) => $row->interaksi->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->interaksi->customer->customer_nama ?? '-')
                ->addColumn('kategori_nama', fn($row) => $row->kategori->kategori_nama ?? 'Tanpa Kategori')
                ->addColumn('aksi', function ($row) {
                    // Pastikan interaksi_id ada sebelum membuat route
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

        $activeMenu = 'dashboard';
        return view('dashboardproduk.ask.index', compact('activeMenu'));
    }

    public function holdProduk(Request $request)
    {
        // Log 1: Memastikan method ini terpanggil.
        Log::info('--- Method holdProduk() Dipanggil ---', [
            'url' => $request->fullUrl(),
            'ajax' => $request->ajax()
        ]);

        // --- QUERY & EAGER LOADING ---
        // 1. Mulai dari RincianModel dengan status 'hold'.
        // 2. Eager load relasi yang dibutuhkan: 'interaksi.customer' dan 'produk.kategori'.
        $queryBuilder = RincianModel::with(['interaksi.customer', 'produk.kategori'])
            ->where('status', 'hold');

        // Log 2: Menampilkan SQL query mentah.
        Log::info('SQL Query Dijalankan (holdProduk):', [
            'sql' => $queryBuilder->toSql(),
            'bindings' => $queryBuilder->getBindings()
        ]);

        // Eksekusi query hanya jika request adalah AJAX, untuk efisiensi
        if ($request->ajax()) {
            $results = $queryBuilder->get();

            // Log 3: Cek jumlah data yang berhasil diambil.
            Log::info('Jumlah data setelah query (holdProduk):', ['count' => $results->count()]);

            // Log 4 (Opsional): Tampilkan contoh data pertama.
            if ($results->isNotEmpty()) {
                Log::info('Contoh data pertama (holdProduk):', $results->first()->toArray());
            }

            // Log 5: Memproses DataTables.
            Log::info('Request adalah AJAX, memproses DataTables (holdProduk)...');

            return DataTables::of($results)
                ->addIndexColumn()
                // Akses data customer melalui relasi interaksi
                ->addColumn('customer_kode', fn($row) => $row->interaksi->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->interaksi->customer->customer_nama ?? '-')
                // Akses data produk dan kategori
                ->addColumn('produk_nama', fn($row) => $row->produk->produk_nama ?? 'Tanpa Produk')
                ->addColumn('kategori_nama', fn($row) => $row->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
                ->addColumn('aksi', function ($row) {
                    // Pastikan interaksi_id ada
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

        // Untuk initial page load (non-AJAX)
        $activeMenu = 'dashboard';
        // Pastikan Anda memiliki view ini
        return view('dashboardproduk.hold.index', compact('activeMenu'));
    }

    public function closingProduk(Request $request)
    {
        // Log 1: Memastikan method ini terpanggil.
        Log::info('--- Method closingProduk() Dipanggil ---', [
            'url' => $request->fullUrl(),
            'ajax' => $request->ajax()
        ]);

        // --- QUERY & EAGER LOADING ---
        // 1. Mulai dari PasangKirimModel dengan status closing.
        // 2. Eager load relasi yang dibutuhkan.
        $queryBuilder = PasangKirimModel::with(['interaksi.customer', 'produk.kategori'])
            ->whereIn('status', ['closing produk', 'closing pasang', 'closing all']);

        // Log 2: Menampilkan SQL query mentah.
        Log::info('SQL Query Dijalankan (closingProduk):', [
            'sql' => $queryBuilder->toSql(),
            'bindings' => $queryBuilder->getBindings()
        ]);

        // Eksekusi query hanya jika request adalah AJAX.
        if ($request->ajax()) {
            $results = $queryBuilder->get();

            // Log 3: Cek jumlah data yang berhasil diambil.
            Log::info('Jumlah data setelah query (closingProduk):', ['count' => $results->count()]);

            return DataTables::of($results)
                ->addIndexColumn()
                // Akses data customer melalui relasi interaksi
                ->addColumn('customer_kode', fn($row) => $row->interaksi->customer->customer_kode ?? '-')
                ->addColumn('customer_nama', fn($row) => $row->interaksi->customer->customer_nama ?? '-')
                // Akses data produk dan kategori
                ->addColumn('produk_nama', fn($row) => $row->produk->produk_nama ?? 'Tanpa Produk')
                ->addColumn('kategori_nama', fn($row) => $row->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
                // Menambahkan kolom status untuk kejelasan
                ->addColumn('status', fn($row) => ucwords($row->status) ?? '-')
                ->addColumn('aksi', function ($row) {
                    // Pastikan interaksi_id ada
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

        // Untuk initial page load (non-AJAX)
        $activeMenu = 'dashboard';
        // Pastikan Anda memiliki view ini
        return view('dashboardproduk.closing.index', compact('activeMenu'));
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
                    $url = route('rekap.show_ajax', $row->interaksi_id);
                    return '<button onclick="modalAction(\'' . $url . '\')" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Detail
                    </button>';
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
                    $url = route('rekap.show_ajax', $row->interaksi_id);
                    return '<button onclick="modalAction(\'' . $url . '\')" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Detail
                    </button>';
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
                    $url = route('rekap.show_ajax', $row->interaksi_id);
                    return '<button onclick="modalAction(\'' . $url . '\')" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Detail
                    </button>';
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
                    $url = route('rekap.show_ajax', $row->interaksi_id);
                    return '<button onclick="modalAction(\'' . $url . '\')" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i> Detail
                    </button>';
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
    public function sendBroadcast(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan');

        $token = env('WABLAS_API_TOKEN', 'rsOFZQEEWNCTK3BRb5vijjQ0xCo59C32OqSh8yYmdhkyPS6cOSx7eZa');
        $secret = env('WABLAS_SECRET_KEY', 'IXMoblCR'); // kalau ada secret key

        $interaksi = InteraksiAwalModel::with(['interaksi.customer'])
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_chat', $tahun);
                if ($bulan) {
                    $q->whereMonth('tanggal_chat', $bulan);
                }
                $q->where('status', 'follow up');
            })
            ->get();

        foreach ($interaksi as $item) {
            $customer = $item->interaksi->customer ?? null;
            if (!$customer || !$customer->customer_nohp) {
                continue;
            }

            $nama = $customer->customer_nama;
            $nohp = $customer->customer_nohp;

            // ✅ Normalisasi nomor: ubah 08xxx jadi 62xxx
            $nohp = preg_replace('/\D/', '', $nohp); // buang non digit
            $nohp = preg_replace('/^0/', '62', $nohp);

            $pesan = "Halo kak {$nama}👋, kami mau follow up terkait katalog & inspirasi desain yang sudah kami kirim kemarin.\n"
                . "Kalau ada desain atau produk yang kak {$nama} suka, bisa langsung balas ya 🙌. Kalau masih bingung, tim kami siap bantu kasih rekomendasi sesuai kebutuhan.\n\n"
                . "Kalau mau lanjut, tinggal balas aja:\n"
                . "✅ Ya — untuk dibantu rekomendasi produk/desain\n"
                . "❌ Tidak — kalau belum butuh saat ini";

            try {
                $headers = [
                    'Authorization' => $token,
                ];
                if ($secret) {
                    $headers['Secret'] = $secret;
                }

                $response = Http::withHeaders($headers)->post('https://sby.wablas.com/api/send-message', [
                    'phone'   => $nohp,
                    'message' => $pesan,
                ]);

                $result = $response->json();
                Log::info("WA Broadcast -> {$nohp}", $result);

                if (!isset($result['status']) || $result['status'] !== true) {
                    Log::error("Gagal kirim ke {$nohp}", $result);
                }

                sleep(1); // jeda agar tidak dianggap spam

            } catch (\Exception $e) {
                Log::error("Exception kirim WA ke {$nohp}: " . $e->getMessage());
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Broadcast berhasil diproses, cek log untuk hasil detail'
        ]);
    }
    public function ghostBroadcast()
{
    return view('broadcast.ghost_broadcast');
}

// Mengirim broadcast untuk customer GHOST
public function sendGhostBroadcast()
{
    $token = env('WABLAS_API_TOKEN', 'rsOFZQEEWNCTK3BRb5vijjQ0xCo59C32OqSh8yYmdhkyPS6cOSx7eZa');
    $secret = env('WABLAS_SECRET_KEY', 'IXMoblCR');

    // Ambil semua customer dengan status ghost
    $customers = InteraksiModel::with('customer')->where('status', 'ghost')->get();

    foreach ($customers as $item) {
        $customer = $item->customer;
        if (!$customer || !$customer->customer_nohp) {
            continue;
        }

        $nama = $customer->customer_nama;
        $nohp = preg_replace('/^0/', '62', preg_replace('/\D/', '', $customer->customer_nohp));

        $pesan = "Halo kak {$nama}👋, gimana kabarnya hari ini? Semoga sehat selalu ya 🙏\n"
            . "Beberapa waktu lalu kakak sempat hubungi kami.\n"
            . "Kalau sekarang lagi belum butuh, nggak apa-apa kak 😊 Tapi kalau masih ada rencana, kami siap bantu kasih katalog & rekomendasi sesuai kebutuhan kakak.";

        try {
            $headers = ['Authorization' => $token];
            if ($secret) $headers['Secret'] = $secret;

            $response = Http::withHeaders($headers)->post('https://sby.wablas.com/api/send-message', [
                'phone' => $nohp,
                'message' => $pesan,
            ]);

            $result = $response->json();
            Log::info("WA Ghost Broadcast -> {$nohp}", $result);

            if (!isset($result['status']) || $result['status'] !== true) {
                Log::error("Gagal kirim ke {$nohp}", $result);
            }

            sleep(1); // jeda agar tidak dianggap spam

        } catch (\Exception $e) {
            Log::error("Exception kirim WA ke {$nohp}: " . $e->getMessage());
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Broadcast GHOST berhasil diproses, cek log untuk detail.'
    ]);
}
}
