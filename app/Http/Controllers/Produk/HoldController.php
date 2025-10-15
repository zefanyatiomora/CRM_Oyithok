<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RincianModel;
use App\Models\InteraksiAwalModel;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class HoldController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'dashboard';
        $breadcrumb = (object) [
            'title' => 'Data Produk HOLD',
            'list' => ['Home', 'Dashboard', 'Hold Produk']
        ];
        $page = (object) [
            'title' => 'Daftar Produk Hold'
        ];

        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        // Ambil semua kategori produk
        $kategoriLabels = KategoriModel::pluck('kategori_nama');

        // Data HOLD berdasarkan kategori (dari rincian)
        $holdKategori = RincianModel::with(['produk.kategori', 'interaksi'])
        ->where('status', 'hold')
        ->get()
        ->groupBy(fn($item) => $item->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
        ->map->count();

        // Bentuk array final biar urut sesuai master kategori
        $dataHold = [];
        foreach ($kategoriLabels as $kategori) {
            $dataHold[$kategori] = $holdKategori[$kategori] ?? 0;
        }

        // Jumlah total produk HOLD
        $jumlahProdukHold = array_sum($dataHold);

        return view('dashboardproduk.hold.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,

            'tahun' => $tahun,
            'bulan' => $bulan,
            'kategoriLabels' => $kategoriLabels,
            'dataHold' => $dataHold,
            'jumlahProdukHold' => $jumlahProdukHold,
        ]);
    }

    public function list(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        Log::info('HoldController list params', [
            'tahun' => $tahun,
            'bulan' => $bulan
        ]);

        $query = RincianModel::with(['interaksi.customer', 'produk.kategori'])
            ->where('status', 'hold');
            
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
                $url = route('hold.show_ajax', $row->rincian_id);
                return '<a href="javascript:void(0)" onclick="modalAction(\'' . $url . '\')" 
                        class="btn btn-info btn-sm">Detail</a>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
        public function broadcast(Request $request)
{
    // Modal konfirmasi broadcast
    return view('broadcast.hold_broadcast');
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
            $q->where('status', 'hold');
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

         $pesan = "Halo kak {$nama}👋\n"
            . "Beberapa waktu lalu kak {$nama} sempat menunda pesanan produk kami.\n"
            . "Bagaimana kelanjutan pesanannya kak? Atau masih ingin diskusi desain/produk lebih lanjut?\n\n"
            . "Kalau mau lanjut, tinggal balas aja:\n"
            . "✅ Ya → untuk dibantu melanjutkan order/rekomendasi produk\n"
            . "❌ Tidak → kalau belum butuh saat ini";

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

}
