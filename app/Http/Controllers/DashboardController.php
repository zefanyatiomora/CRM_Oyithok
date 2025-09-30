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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
            'title' => 'Selamat Datang, Wallpaper Indonesia ID',
            'list' => ['Home', 'Dashboard']
        ];
        $page = (object) [
            'title' => 'Selamat Datang, Wallpaper Indonesia ID'
        ];

        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        $queryBase = InteraksiModel::whereYear('tanggal_chat', $tahun);
        if ($bulan) {
            $queryBase->whereMonth('tanggal_chat', $bulan);
        }

        // === Jumlah berdasarkan tahapan ===
        $jumlahInteraksi = (clone $queryBase)->count();
        $customerDoughnut = $this->getCustomerDoughnutData($tahun, $bulan);

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

        // === Logika Leads Baru vs Lama ===
        $chartLabels   = [];
        $dataLeadsBaru = [];
        $dataLeadsLama = [];

        if ($bulan) {
            // Per hari dalam bulan terpilih
            $jumlahHari = Carbon::create($tahun, $bulan)->daysInMonth;
            for ($hari = 1; $hari <= $jumlahHari; $hari++) {
                $chartLabels[] = $hari;

                // Semua customer unik di hari ini (via join interaksi_realtime -> interaksi)
                $customerHariIni = DB::table('interaksi_realtime as ir')
                    ->join('interaksi as i', 'ir.interaksi_id', '=', 'i.interaksi_id') // ✅ fix disini
                    ->whereYear('ir.tanggal', $tahun)
                    ->whereMonth('ir.tanggal', $bulan)
                    ->whereDay('ir.tanggal', $hari)
                    ->pluck('i.customer_id')
                    ->unique();

                $leadsBaruHariIni = 0;
                foreach ($customerHariIni as $cid) {
                    $firstInteraksi = DB::table('interaksi')
                        ->where('customer_id', $cid)
                        ->orderBy('tanggal_chat', 'asc')
                        ->value('tanggal_chat');

                    if (
                        $firstInteraksi && Carbon::parse($firstInteraksi)->year == $tahun
                        && Carbon::parse($firstInteraksi)->month == $bulan
                    ) {
                        $leadsBaruHariIni++;
                    }
                }

                $dataLeadsBaru[] = $leadsBaruHariIni;
                $dataLeadsLama[] = count($customerHariIni) - $leadsBaruHariIni;
            }
        } else {
            // Per bulan dalam setahun
            foreach ($bulanList as $key => $namaBulan) {
                $chartLabels[] = $namaBulan;

                // Semua customer unik di bulan ini (via join interaksi_realtime -> interaksi)
                $customerBulanIni = DB::table('interaksi_realtime as ir')
                    ->join('interaksi as i', 'ir.interaksi_id', '=', 'i.interaksi_id') // ✅ fix disini juga
                    ->whereYear('ir.tanggal', $tahun)
                    ->whereMonth('ir.tanggal', $key)
                    ->pluck('i.customer_id')
                    ->unique();

                $leadsBaruBulanIni = 0;
                foreach ($customerBulanIni as $cid) {
                    $firstInteraksi = DB::table('interaksi')
                        ->where('customer_id', $cid)
                        ->orderBy('tanggal_chat', 'asc')
                        ->value('tanggal_chat');

                    if (
                        $firstInteraksi && Carbon::parse($firstInteraksi)->year == $tahun
                        && Carbon::parse($firstInteraksi)->month == $key
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


        // Debug log
        Log::info('--- DEBUGGING DASHBOARD LEADS ---', [
            'Tahun' => $tahun,
            'Bulan' => $bulan,
            'Data Leads Baru' => $dataLeadsBaru,
            'Data Leads Lama' => $dataLeadsLama,
            'Total Leads Baru' => $totalLeadsBaru,
            'Total Leads Lama' => $totalLeadsLama,
        ]);
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
        $rateClosing = $this->getRateClosingData($tahun, $bulan);

        return view('dashboard.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,

            'tahun' => $tahun,
            'bulan' => $bulan,
            'availableYears' => $availableYears,
            'bulanList' => $bulanList,

            'jumlahInteraksi' => $jumlahInteraksi,

            'jumlahGhost' => $customerDoughnut['customerDoughnutData'][0],
            'jumlahAsk' => $customerDoughnut['customerDoughnutData'][1],
            'jumlahFollowUp' => $customerDoughnut['customerDoughnutData'][2],
            'jumlahHold' => $customerDoughnut['customerDoughnutData'][3],
            'jumlahClosing' => $customerDoughnut['customerDoughnutData'][4],

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

            'customerDoughnutLabels' => $customerDoughnut['customerDoughnutLabels'],
            'customerDoughnutData'   => $customerDoughnut['customerDoughnutData'],
            'customerDoughnutColors' => $customerDoughnut['customerDoughnutColors'],

            'doughnutLabels' => $doughnutLabels,
            'doughnutData' => $doughnutData,
            'doughnutColors' => array_slice($doughnutColors, 0, count($doughnutLabels)),

            'rateClosingLabels' => $rateClosing['rateClosingLabels'],
            'rateClosingDatasets' => $rateClosing['rateClosingDatasets'],
        ]);
    }
private function getRateClosingData($tahun, $bulan)
{
    $startOfMonth = Carbon::create($tahun, $bulan, 1);
    $endOfMonth   = $startOfMonth->copy()->endOfMonth();

    // Tentukan minggu dalam bulan
    $weeks = [];
    $current = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);

    while ($current <= $endOfMonth) {
        $weeks[] = [
            'start' => $current->copy(),
            'end'   => $current->copy()->endOfWeek(Carbon::SUNDAY),
        ];
        $current->addWeek();
    }

    $rateClosingLabels = [];
    $allData    = [];
    $produkData = [];
    $surveyData = [];
    $pasangData = [];

    foreach ($weeks as $index => $week) {
        $startDate = $week['start'];
        $endDate   = $week['end'];

        $rateClosingLabels[] = "Minggu " . ($index + 1);

        // === Closing All ===
        $countAll = PasangKirimModel::where('status', 'closing all')
            ->whereBetween('jadwal_pasang_kirim', [$startDate, $endDate])
            ->count();

        // === Closing Produk ===
        $countProduk = PasangKirimModel::where('status', 'closing produk')
            ->whereBetween('jadwal_pasang_kirim', [$startDate, $endDate])
            ->count();

        // === Closing Survey ===
        $countSurvey = SurveyModel::where('status', 'closing')
            ->whereBetween('jadwal_survey', [$startDate, $endDate])
            ->count();

        // === Closing Pasang ===
        $countPasang = PasangKirimModel::where('status', 'closing')
            ->whereBetween('jadwal_pasang_kirim', [$startDate, $endDate])
            ->count();

        $allData[]    = $countAll;
        $produkData[] = $countProduk;
        $surveyData[] = $countSurvey;
        $pasangData[] = $countPasang;
    }
return [
    'rateClosingLabels' => ['All', 'Produk', 'Survey', 'Pasang'],
    'rateClosingDatasets' => [
        [
            'label' => 'Minggu 1',
            'data'  => [$allData[0] ?? 0, $produkData[0] ?? 0, $surveyData[0] ?? 0, $pasangData[0] ?? 0],
            'borderColor' => '#5C54AD',
            'backgroundColor' => 'rgba(92, 84, 173, 0.2)',
            'tension' => 0.3,
            'fill' => false,
        ],
        [
            'label' => 'Minggu 2',
            'data'  => [$allData[1] ?? 0, $produkData[1] ?? 0, $surveyData[1] ?? 0, $pasangData[1] ?? 0],
            'borderColor' => '#6690FF',
            'backgroundColor' => 'rgba(102, 144, 255, 0.2)',
            'tension' => 0.3,
            'fill' => false,
        ],
        [
            'label' => 'Minggu 3',
            'data'  => [$allData[2] ?? 0, $produkData[2] ?? 0, $surveyData[2] ?? 0, $pasangData[2] ?? 0],
            'borderColor' => '#A374FF',
            'backgroundColor' => 'rgba(163, 116, 255, 0.2)',
            'tension' => 0.3,
            'fill' => false,
        ],
        [
            'label' => 'Minggu 4',
            'data'  => [$allData[3] ?? 0, $produkData[3] ?? 0, $surveyData[3] ?? 0, $pasangData[3] ?? 0],
            'borderColor' => '#FF7373',
            'backgroundColor' => 'rgba(255, 115, 115, 0.2)',
            'tension' => 0.3,
            'fill' => false,
        ],
        [
            'label' => 'Minggu 5',
            'data'  => [$allData[4] ?? 0, $produkData[4] ?? 0, $surveyData[4] ?? 0, $pasangData[4] ?? 0],
            'borderColor' => '#A26360',
            'backgroundColor' => 'rgba(162, 99, 96, 0.2)',
            'tension' => 0.3,
            'fill' => false,
        ],
    ],
];
}
    private function getProdukChartData($tahun, $bulan)
    {
        $kategoriLabels = KategoriModel::pluck('kategori_nama');

        $askKategori = InteraksiAwalModel::with('kategori')
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_chat', $tahun);
                if ($bulan) $q->whereMonth('tanggal_chat', $bulan);
            })
            ->get()
            ->groupBy(fn($item) => $item->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

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
            ->whereIn('status', ['closing produk', 'closing pasang', 'closing all'])
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_chat', $tahun);
                if ($bulan) $q->whereMonth('tanggal_chat', $bulan);
            })
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

        return [
            'kategoriLabels' => $kategoriLabels,
            'dataAsk' => $dataAsk,
            'dataHold' => $dataHold,
            'dataClosing' => $dataClosing,
            'jumlahProdukAsk' => array_sum($dataAsk),
            'jumlahProdukHold' => array_sum($dataHold),
            'jumlahProdukClosing' => array_sum($dataClosing),
            'closingKategori' => $closingKategori,
        ];
    }
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
            'customerDoughnutLabels' => ['Ghost','Ask', 'Follow up', 'Hold', 'Closing'],
            'customerDoughnutData'   => [$jumlahGhost, $jumlahAsk, $jumlahFollowUp, $jumlahHold, $jumlahClosing],
            'customerDoughnutColors' => ['#9a9d9eff','#87CEEB', '#A374FF', '#5C54AD', '#FF7373'],
        ];
    }
public function ghost(Request $request)
{
    $query = InteraksiModel::with('customer')
        ->where('status', 'ghost');

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
        $query = InteraksiModel::with('customer')
            ->where('status', 'ask');

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

    // RekapController.php

    public function followup(Request $request)
    {
        $activeMenu = 'followup';
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        // di followup()
        $query = InteraksiModel::with('customer')
            ->whereIn('status', ['followup', 'follow up']);

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

    public function hold(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        $query = InteraksiModel::with('customer')
            ->where('status', 'hold');

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
            ->where('status', 'closing');

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
