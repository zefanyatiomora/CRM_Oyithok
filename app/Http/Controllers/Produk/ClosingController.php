<?php

namespace App\Http\Controllers\Produk;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RincianModel;
use App\Models\KategoriModel;
use App\Models\InteraksiAwalModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ClosingController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'dashboard';
        $breadcrumb = (object) [
            'title' => 'Data Produk CLOSING',
            'list' => ['Home', 'Dashboard', 'Closing Produk']
        ];
        $page = (object) [
            'title' => 'Daftar Produk Closing'
        ];

        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');

        // Ambil semua kategori produk
        $kategoriLabels = KategoriModel::pluck('kategori_nama');

        // Data CLOSING berdasarkan kategori
        $closingKategori = RincianModel::with(['produk.kategori', 'interaksi'])
            ->where('status', 'closing')
            ->whereHas('interaksi', function ($q) use ($tahun, $bulan) {
                $q->whereYear('tanggal_chat', $tahun);
                if ($bulan) {
                    $q->whereMonth('tanggal_chat', $bulan);
                }
            })
            ->get()
            ->groupBy(fn($item) => $item->produk->kategori->kategori_nama ?? 'Tanpa Kategori')
            ->map->count();

        // Bentuk array final biar urut sesuai master kategori
        $dataClosing = [];
        foreach ($kategoriLabels as $kategori) {
            $dataClosing[$kategori] = $closingKategori[$kategori] ?? 0;
        }

        // Jumlah total produk CLOSING
        $jumlahProdukClosing = array_sum($dataClosing);

        return view('dashboardproduk.closing.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,

            'tahun' => $tahun,
            'bulan' => $bulan,
            'kategoriLabels' => $kategoriLabels,
            'dataClosing' => $dataClosing,
            'jumlahProdukClosing' => $jumlahProdukClosing,
        ]);
    }

    public function list(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        Log::info('ClosingController list params', [
            'tahun' => $tahun,
            'bulan' => $bulan
        ]);

        $query = RincianModel::with(['interaksi.customer', 'produk.kategori'])
            ->where('status', 'closing')
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
                return $row->produk->kategori->kategori_nama ?? 'Tanpa Kategori';
            })
            ->addColumn('aksi', function ($row) {
                $url = route('closing.show_ajax', $row->rincian_id);
                return '<a href="javascript:void(0)" onclick="modalAction(\'' . $url . '\')" 
                        class="btn btn-info btn-sm">Detail</a>';
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
      public function broadcast(Request $request)
{
    // Modal konfirmasi broadcast
    return view('broadcast.closing_broadcast');
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
            $q->where('status', 'closing');
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

        $pesan = "Halo Kak {$nama} 👋😊\n\n"
                . "Terima kasih sudah percaya dan kerja sama bareng tim kami🙏\n"
                . "Kami selalu berusaha kasih hasil yang terbaik biar sesuai sama harapan Kak {$nama} ✨\n\n"
                . "Kalau berkenan, boleh bantu kami dengan memberikan feedback singkat? Cukup balas dengan angka:\n\n"
                . "1 - Puas\n"
                . "2 - Cukup Puas\n"
                . "3 - Kurang Puas\n\n"
                . "Masukan dari Kakak sangat berarti buat kami agar bisa terus memberikan layanan yang lebih baik lagi 🙏";

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
