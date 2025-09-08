<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\InteraksiModel;
use App\Models\PasangKirimModel;
use App\Models\RincianModel;
use App\Models\KategoriModel;
use App\Models\SurveyModel;
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

        // === PERSIAPAN DATA UNTUK TAB CUSTOMER ===

        // Data untuk Doughnut Chart (Data Customer)
        $customerDoughnutLabels = ['Ask', 'Follow up', 'Hold', 'Closing'];
        $customerDoughnutData = [$jumlahAsk, $jumlahFollowUp, $jumlahHold, $jumlahClosing];
        $customerDoughnutColors = ['#87CEEB', '#A374FF', '#5C54AD', '#FF7373'];

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
            $jumlahHari = Carbon::create($tahun, $bulan)->daysInMonth;
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
        // Kita jumlahkan total dari array data leads baru dan lama
        $totalLeadsBaru = array_sum($dataLeadsBaru);
        $totalLeadsLama = array_sum($dataLeadsLama);

        // TAMBAHKAN BLOK LOG DI SINI UNTUK DEBUGGING
        // ======================================================
        Log::info('--- DEBUGGING DASHBOARD LEADS ---');
        Log::info('Filter Aktif:', ['Tahun' => $tahun, 'Bulan' => $bulan]);
        Log::info('Data Leads Baru (Per hari/bulan):', $dataLeadsBaru);
        Log::info('Data Leads Lama (Per hari/bulan):', $dataLeadsLama);
        Log::info('TOTAL YANG DIKIRIM KE CHART:', [
            'Total Leads Baru' => $totalLeadsBaru,
            'Total Leads Lama' => $totalLeadsLama
        ]);


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

        $closingKategori = PasangKirimModel::with('produk.kategori', 'interaksi')
            ->whereIn('status', ['closing produk', 'closing pasang'])
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_chat', $tahun);
                if ($bulan) $q->whereMonth('tanggal_chat', $bulan);
            })
            ->get()
            ->groupBy(fn($item) => $item->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

        // ... setelah semua perhitungan $jumlah... dan $closingKategori ...

        // ======================================================
        // PERSIAPAN DATA UNTUK LINE CHART (RATE CUSTOMER CLOSING)
        // ======================================================
        $rateClosingLabels = ['All', 'Produk', 'Pasang', 'Survei'];
        $rateClosingDatasets = [];

        // Logika ini hanya berjalan jika filter bulan aktif
        if ($bulan) {
            $weeks = [
                ['start' => 1, 'end' => 7],
                ['start' => 8, 'end' => 14],
                ['start' => 15, 'end' => 21],
                // Minggu ke-4 dimulai dari tgl 22 sampai akhir bulan
                ['start' => 22, 'end' => \Carbon\Carbon::create($tahun, $bulan)->endOfMonth()->day],
            ];

            $lineColors = ['#5C54AD', '#000000', '#FF7373', '#20c997'];

            foreach ($weeks as $index => $week) {
                $startDate = Carbon::create($tahun, $bulan, $week['start'])->startOfDay();
                $endDate = Carbon::create($tahun, $bulan, $week['end'])->endOfDay();

                // 1. Hitung closing 'produk' dan 'pasang' dari PasangKirimModel untuk minggu ini
                $pasangQuery = PasangKirimModel::whereHas('interaksi', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_chat', [$startDate, $endDate]);
                });

                $countProduk = (clone $pasangQuery)->where('status', 'closing produk')->count();
                $countPasang = (clone $pasangQuery)->where('status', 'closing pasang')->count();

                // 2. Hitung closing 'survey' dari SurveyModel untuk minggu ini
                $countSurvey = SurveyModel::whereHas('interaksi', function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('tanggal_chat', [$startDate, $endDate]);
                })->count();

                // 3. Hitung total 'All'
                $countAll = $countProduk + $countPasang + $countSurvey;

                // 4. Buat dataset untuk minggu ini
                $rateClosingDatasets[] = [
                    'label' => 'Minggu ' . ($index + 1),
                    'data' => [$countAll, $countProduk, $countPasang, $countSurvey],
                    'borderColor' => $lineColors[$index],
                    'tension' => 0.1, // Membuat garis sedikit melengkung
                    'fill' => false,
                ];
            }
        }
        Log::info('--- DEBUGGING RATE CUSTOMER CLOSING ---');
        Log::info('Filter Aktif:', ['Tahun' => $tahun, 'Bulan' => $bulan]);
        Log::info('Data yang dihasilkan untuk Line Chart:', $rateClosingDatasets);


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
        Log::info('ClosingKategori detail:', $closingKategori->toArray());

        // 1. Filter kategori yang closing-nya lebih dari 0
        $penjualanData = collect($closingKategori)->filter(function ($value) {
            return $value > 0;
        });

        // 2. Siapkan variabel untuk dikirim ke view
        $doughnutLabels = $penjualanData->keys();
        $doughnutData = $penjualanData->values();

        // 3. Siapkan palet warna yang menarik
        $doughnutColors = [
            '#6690FF', // Biru Muda
            '#A374FF', // Ungu
            '#5C54AD', // Biru Tua
            '#FF7373', // Merah/Pink
            '#6C63AC', // Ungu Tua
            '#FFB6C1', // Light Pink
            '#87CEEB'  // Sky Blue
        ];

        // === AKHIR PERSIAPAN DATA ===

        // Logging hasilnya (opsional, untuk debugging)
        Log::info('Doughnut Labels:', $doughnutLabels->toArray());
        Log::info('Doughnut Data:', $doughnutData->toArray());
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
            // / Tambahkan 2 variabel TOTAL ini
            'totalLeadsBaru' => $totalLeadsBaru,
            'totalLeadsLama' => $totalLeadsLama,

            'kategoriLabels' => $kategoriLabels,
            'dataAsk' => $dataAsk,
            'dataHold' => $dataHold,
            'dataClosing' => $dataClosing,
            // Tambahkan variabel baru ini untuk dikirim ke view
            'doughnutLabels' => $doughnutLabels,
            'doughnutData' => $doughnutData,
            'doughnutColors' => array_slice($doughnutColors, 0, $doughnutLabels->count()),
            // Variabel BARU untuk chart di Tab Customer
            'customerDoughnutLabels' => $customerDoughnutLabels,
            'customerDoughnutData' => $customerDoughnutData,
            'customerDoughnutColors' => $customerDoughnutColors,

            // Variabel BARU untuk line chart
            'rateClosingLabels' => $rateClosingLabels,
            'rateClosingDatasets' => $rateClosingDatasets,
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
