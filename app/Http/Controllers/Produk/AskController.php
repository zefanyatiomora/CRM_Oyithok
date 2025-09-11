<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InteraksiAwalModel;
use App\Models\KategoriModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;


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
                $url = route('ask.show_ajax', $row->interaksi_id);
                return '<a href="javascript:void(0)" onclick="modalAction(\'' . $url . '\')" 
                        class="btn btn-info btn-sm">Detail</a>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    public function broadcast(Request $request)
{
    // Modal konfirmasi broadcast
    return view('broadcast.ask_broadcast');
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
            $q->where('status', 'ask');
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
            . "Terima kasih sudah tertarik dengan produk kami ✨\n"
            . "Biar kak {$nama} nggak bingung memilih, kami sudah siapkan katalog & inspirasi desain terbaru 📂\n\n"
            . "Kalau mau lihat atau masih ada pertanyaan, tinggal balas:\n"
            . "Ketik 1 → untuk minta katalog/inspirasi desain\n"
            . "Ketik 0 → kalau belum butuh saat ini";

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

// public function sendBroadcast(Request $request)
// {
//     $token = env('WABLAS_API_TOKEN', 'rsOFZQEEWNCTK3BRb5vijjQ0xCo59C32OqSh8yYmdhkyPS6cOSx7eZa');
//     $pesan = "Halo, ini tes broadcast dari Laravel!";

//     // contoh nomor (ubah jadi dinamis nanti)
//     $nohp = "081553364342"; 

//     // pastikan format 62
//     $nohp = preg_replace('/^0/', '62', $nohp);

//    $response = Http::withHeaders([
//     'Authorization' => $token,
//     'Secret' => env('zEvUEPZJ'),
// ])->post('https://sby.wablas.com/api/send-message', [
//     'phone'   => $nohp,
//     'message' => $pesan,
// ]);


//     Log::info("Wablas response", $response->json());

//     return response()->json($response->json());
// }

}
