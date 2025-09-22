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
            'title' => 'Dashboard CRM',
            'list' => ['Home', 'Dashboard']
        ];
        $page = (object) [
            'title' => 'Dashboard CRM'
        ];

        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        $queryBase = InteraksiModel::whereYear('tanggal_chat', $tahun);
        if ($bulan) {
            $queryBase->whereMonth('tanggal_chat', $bulan);
        }

        // === Jumlah berdasarkan tahapan ===
        $jumlahInteraksi = (clone $queryBase)->count();
        $prosesSurvey    = (clone $queryBase)->where('tahapan', 'survey')->count();
        $prosesPasang    = (clone $queryBase)->where('tahapan', 'pasang')->count();
        $prosesOrder     = (clone $queryBase)->where('tahapan', 'order')->count();

        // === Jumlah berdasarkan status ===
        $jumlahAsk = InteraksiModel::where('status', 'ask')->count();
        $jumlahFollowUp = InteraksiModel::whereIn('status', ['followup', 'follow up'])->count();
        $jumlahHold = InteraksiModel::where('status', 'hold')->count();
        $jumlahClosing = InteraksiModel::where('status', 'closing')->count();

        // Data Doughnut Chart Customer
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

        // === Logika Leads Baru vs Lama ===
$chartLabels   = [];
$dataLeadsBaru = [];
$dataLeadsLama = [];

if ($bulan) {
    // Per hari dalam bulan terpilih
    $jumlahHari = Carbon::create($tahun, $bulan)->daysInMonth;
    for ($hari = 1; $hari <= $jumlahHari; $hari++) {
        $chartLabels[] = $hari;

        $customerHariIni = DB::table('interaksi_realtime as ir')
            ->join('interaksi as i', 'ir.interaksi_id', '=', 'i.interaksi_id')
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

            if ($firstInteraksi && Carbon::parse($firstInteraksi)->year == $tahun 
                && Carbon::parse($firstInteraksi)->month == $bulan) {
                $leadsBaruHariIni++;
            }
        }

        $dataLeadsBaru[] = $leadsBaruHariIni;
        $dataLeadsLama[] = count($customerHariIni) - $leadsBaruHariIni;
    }
}

// total hanya dihitung kalau ada bulan
$totalLeadsBaru = $bulan ? array_sum($dataLeadsBaru) : 0;
$totalLeadsLama = $bulan ? array_sum($dataLeadsLama) : 0;

        // Debug log
        Log::info('--- DEBUGGING DASHBOARD LEADS ---', [
            'Tahun' => $tahun,
            'Bulan' => $bulan,
            'Data Leads Baru' => $dataLeadsBaru,
            'Data Leads Lama' => $dataLeadsLama,
            'Total Leads Baru' => $totalLeadsBaru,
            'Total Leads Lama' => $totalLeadsLama,
        ]);

        // === Data kategori produk ===
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
            ->whereIn('status', ['closing produk', 'closing pasang'])
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_chat', $tahun);
                if ($bulan) $q->whereMonth('tanggal_chat', $bulan);
            })
            ->get()
            ->groupBy(fn($item) => $item->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

// === Rate Closing Line Chart ===
$rateClosingDatasets = [];

if ($bulan) {
    // ---------------------------
    // MODE PER MINGGU (dalam 1 bulan)
    // ---------------------------
    $weeks = [
        ['start' => 1, 'end' => 7],
        ['start' => 8, 'end' => 14],
        ['start' => 15, 'end' => 21],
        ['start' => 22, 'end' => Carbon::create($tahun, $bulan)->endOfMonth()->day],
    ];

    $rateClosingLabels = ['All', 'Produk', 'Pasang', 'Survei'];

    foreach ($rateClosingLabels as $label) {
        $data = [];
        foreach ($weeks as $week) {
            $startDate = Carbon::create($tahun, $bulan, $week['start'])->startOfDay();
            $endDate   = Carbon::create($tahun, $bulan, $week['end'])->endOfDay();

            // Hitung sesuai kategori
            if ($label === 'All') {
                $count = PasangKirimModel::whereHas('interaksi', fn($q) =>
                            $q->whereBetween('tanggal_chat', [$startDate, $endDate]))
                        ->whereIn('status', ['closing produk','closing pasang'])
                        ->count()
                        +
                        SurveyModel::whereHas('interaksi', fn($q) =>
                            $q->whereBetween('tanggal_chat', [$startDate, $endDate]))
                        ->count();
            } elseif ($label === 'Produk') {
                $count = PasangKirimModel::whereHas('interaksi', fn($q) =>
                            $q->whereBetween('tanggal_chat', [$startDate, $endDate]))
                        ->where('status', 'closing produk')
                        ->count();
            } elseif ($label === 'Pasang') {
                $count = PasangKirimModel::whereHas('interaksi', fn($q) =>
                            $q->whereBetween('tanggal_chat', [$startDate, $endDate]))
                        ->where('status', 'closing pasang')
                        ->count();
            } else { // Survei
                $count = SurveyModel::whereHas('interaksi', fn($q) =>
                            $q->whereBetween('tanggal_chat', [$startDate, $endDate]))
                        ->count();
            }

            $data[] = $count;
        }

        $rateClosingDatasets[] = [
            'label' => $label,
            'data' => $data,
            'borderColor' => $label === 'All' ? '#6C63AC' :
                             ($label === 'Produk' ? '#FF7373' :
                             ($label === 'Pasang' ? '#87CEEB' : '#5C54AD')),
            'tension' => 0.3,
            'fill' => false,
        ];
    }

} else {
    // ---------------------------
    // MODE PER BULAN (1 tahun penuh)
    // ---------------------------
    $months = range(1, 12);
    $rateClosingLabels = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    foreach (['All','Produk','Pasang','Survei'] as $label) {
        $data = [];
        foreach ($months as $m) {
            $startDate = Carbon::create($tahun, $m, 1)->startOfMonth();
            $endDate   = Carbon::create($tahun, $m, 1)->endOfMonth();

            if ($label === 'All') {
                $count = PasangKirimModel::whereHas('interaksi', fn($q) =>
                            $q->whereBetween('tanggal_chat', [$startDate, $endDate]))
                        ->whereIn('status', ['closing produk','closing pasang'])
                        ->count()
                        +
                        SurveyModel::whereHas('interaksi', fn($q) =>
                            $q->whereBetween('tanggal_chat', [$startDate, $endDate]))
                        ->count();
            } elseif ($label === 'Produk') {
                $count = PasangKirimModel::whereHas('interaksi', fn($q) =>
                            $q->whereBetween('tanggal_chat', [$startDate, $endDate]))
                        ->where('status', 'closing produk')
                        ->count();
            } elseif ($label === 'Pasang') {
                $count = PasangKirimModel::whereHas('interaksi', fn($q) =>
                            $q->whereBetween('tanggal_chat', [$startDate, $endDate]))
                        ->where('status', 'closing pasang')
                        ->count();
            } else { // Survei
                $count = SurveyModel::whereHas('interaksi', fn($q) =>
                            $q->whereBetween('tanggal_chat', [$startDate, $endDate]))
                        ->count();
            }

            $data[] = $count;
        }

        $rateClosingDatasets[] = [
            'label' => $label,
            'data' => $data,
            'borderColor' => $label === 'All' ? '#6C63AC' :
                             ($label === 'Produk' ? '#FF7373' :
                             ($label === 'Pasang' ? '#87CEEB' : '#5C54AD')),
            'tension' => 0.3,
            'fill' => false,
        ];
    }
}

        // === Data untuk chart kategori produk ===
        $dataAsk = [];
        $dataHold = [];
        $dataClosing = [];
        foreach ($kategoriLabels as $kategori) {
            $dataAsk[] = $askKategori[$kategori] ?? 0;
            $dataHold[] = $holdKategori[$kategori] ?? 0;
            $dataClosing[] = $closingKategori[$kategori] ?? 0;
        }

        $jumlahProdukAsk     = array_sum($dataAsk);
        $jumlahProdukHold    = array_sum($dataHold);
        $jumlahProdukClosing = array_sum($dataClosing);

        // Filter hanya kategori dengan closing > 0
        $penjualanData = collect($closingKategori)->filter(fn($v) => $v > 0);

        $doughnutLabels = $penjualanData->keys();
        $doughnutData   = $penjualanData->values();

        $doughnutColors = [
            '#6690FF', '#A374FF', '#5C54AD',
            '#FF7373', '#6C63AC', '#FFB6C1', '#87CEEB'
        ];

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
            'totalLeadsBaru' => $totalLeadsBaru,
            'totalLeadsLama' => $totalLeadsLama,

            'kategoriLabels' => $kategoriLabels,
            'dataAsk' => $dataAsk,
            'dataHold' => $dataHold,
            'dataClosing' => $dataClosing,

            'doughnutLabels' => $doughnutLabels,
            'doughnutData' => $doughnutData,
            'doughnutColors' => array_slice($doughnutColors, 0, $doughnutLabels->count()),

            'customerDoughnutLabels' => $customerDoughnutLabels,
            'customerDoughnutData' => $customerDoughnutData,
            'customerDoughnutColors' => $customerDoughnutColors,

            'rateClosingLabels' => $rateClosingLabels,
            'rateClosingDatasets' => $rateClosingDatasets,
        ]);
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
