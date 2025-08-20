<?php

namespace App\Http\Controllers;

use App\Models\InteraksiModel;
use App\Models\InteraksiRealtime;
use App\Models\CustomersModel;
use App\Models\ProdukModel;
use App\Models\RincianModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $activeMenu = 'dashboard';
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan'); // nullable
        $interaksiId = $request->input('interaksi_id');

        // Log::info('RekapController index params', [
        //     'tahun' => $tahun,
        //     'bulan' => $bulan,
        //     'interaksi_id' => $interaksiId
        // ]);
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
            '12' => 'Desember',
        ];

        $judulRekap = $bulan
            ? "Rekap Bulan " . $bulanList[$bulan] . " $tahun"
            : "Rekap Tahun $tahun";

        $breadcrumb = (object) [
            'title' => 'Daftar Rekap',
            'list' => [
                'Home' => url('/'),       // link ke home
                $judulRekap // aktif, tidak ada link
            ]
        ];

        $page = (object) [
            'title' => $judulRekap
        ];
        // Query data interaksi
        $query = InteraksiModel::with(['customer'])
            ->whereYear('tanggal_chat', $tahun);

        if ($bulan) {
            $query->whereMonth('tanggal_chat', $bulan);
        }
        if ($interaksiId) {
            $query->where('interaksi_id', $interaksiId);
        }
        $interaksi = $query->get();
        // Log::info('Jumlah interaksi hasil filter', ['count' => $interaksi->count()]);

        return view('rekap.index', compact('breadcrumb', 'page', 'activeMenu', 'tahun', 'bulan', 'bulanList', 'interaksi', 'interaksiId'));
    }
    public function indexRealtime($interaksi_id)
    {
        $interaksis = InteraksiModel::findOrFail($interaksi_id);

        return view('rekap.index_realtime', [
            'interaksis' => $interaksis,
            'activeMenu' => 'dashboard'
        ]);
    }
    public function list(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $interaksiId = $request->input('interaksi_id');

        // Log::info('RekapController@list: Filter Tahun = ' . $tahun . ', Bulan = ' . $bulan . ',Interaksi ID = ' . $interaksiId);

        $query = InteraksiModel::with(['customer'])
            ->select('interaksi_id', 'customer_id', 'produk_id', 'produk_nama', 'tanggal_chat', 'media', 'tahapan', 'identifikasi_kebutuhan', 'item_type');

        if ($tahun) {
            $query->whereYear('tanggal_chat', $tahun);
        }

        if ($bulan) {
            $query->whereMonth('tanggal_chat', $bulan);
        }
        if ($interaksiId) {
            $query->where('interaksi_id', $interaksiId);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('aksi', function ($row) {
                $btn = '<button onclick="modalAction(\'' . url('/rekap/' . $row->interaksi_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show_ajax($id)
    {
        $interaksi = InteraksiModel::with('customer', 'produk')->findOrFail($id);
        $produkList = ProdukModel::all(); // ambil semua produk untuk dropdown

        $steps = ['Identifikasi', 'Survey', 'Rincian', 'Pasang', 'Done'];

        $originalStep = array_search(
            strtolower($interaksi->tahapan),
            array_map('strtolower', $steps)
        );

        $currentStep = $originalStep; // default sama, nanti berubah di update

        Log::info('[Edit Interaksi]', [
            'interaksi_id' => $id,
            'tahapan'      => $interaksi->tahapan,
            'originalStep' => $originalStep,
            'currentStep'  => $currentStep,
        ]);
        return view('rekap.show_ajax', [
            'interaksi' => $interaksi,
            'produkList' => $produkList,
            'followUpOptions' => ['Ask', 'Follow Up', 'Closing Survey', 'Closing Pasang', 'Closing Product', 'Closing ALL'],
            'selectedFollowUp' => $interaksi->status ?? '',
            'closeValue'       => $interaksi->close ?? '',
            'steps'       => $steps,
            'originalStep'       => $originalStep,
            'currentStep'       => $currentStep
        ]);
    }
    public function updateFollowUp(Request $request)
    {
        Log::info('updateFollowUp data diterima:', $request->all());

        $validated = $request->validate([
            'interaksi_id' => 'required|exists:interaksi,interaksi_id',
            'customer_id'  => 'required|exists:customers,customer_id',
            'status'    => 'required|string',
            'tahapan'      => 'required|string',
            'pic'          => 'required|string',
        ]);

        // Tentukan tahapan proses
        $steps = ['Identifikasi', 'Survey', 'Rincian', 'Pasang', 'Done'];

        $interaksi = InteraksiModel::findOrFail($validated['interaksi_id']);

        // Hitung step lama dan step baru
        $originalStep = array_search(
            strtolower($interaksi->tahapan),
            array_map('strtolower', $steps)
        );

        $currentStep = array_search(
            strtolower($validated['tahapan']),
            array_map('strtolower', $steps)
        );

        Log::info('Progress Step Update:', [
            'interaksi_id'   => $validated['interaksi_id'],
            'originalTahapan' => $interaksi->tahapan,
            'currentTahapan' => $validated['tahapan'],
            'originalStep'   => $originalStep,
            'currentStep'    => $currentStep,
            'steps'          => $steps
        ]);

        try {
            $updateResult = InteraksiModel::where('interaksi_id', $validated['interaksi_id'])
                ->update([
                    'tahapan'   => $validated['tahapan'],
                    'pic'       => $validated['pic'],
                ]);

            Log::info('Update result:', [
                'rows_affected' => $updateResult
            ]);
            // Simpan rincian produk baru
            // if (!empty($request->produk_id)) {
            //     foreach ($request->produk_id as $idx => $produkId) {
            //         if ($produkId) {
            //             RincianModel::create([
            //                 'interaksi_id' => $interaksi->interaksi_id,
            //                 'produk_id'    => $produkId,
            //                 'deskripsi'    => $request->keterangan[$idx] ?? null,
            //                 'kuantitas'    => $request->kuantitas[$idx] ?? 1,
            //             ]);
            //         }
            //     }
            // }

            return response()->json([
                'status'       => 'success',
                'message'      => 'Data follow up berhasil disimpan',
                'originalStep' => $originalStep,
                'currentStep'  => $currentStep
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan follow up: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan follow up',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    // Tambah kebutuhan harian
    public function storeRealtime(Request $request)
    {
        $interaksi_id = $request->interaksi_id;
        $tanggals = $request->tanggal;      // array
        $keterangans = $request->keterangan; // array

        if (is_array($tanggals) && is_array($keterangans)) {
            foreach ($tanggals as $i => $tgl) {
                $keterangan = $keterangans[$i] ?? null;
                if ($tgl) { // pastikan tanggal diisi
                    InteraksiRealtime::create([
                        'interaksi_id' => $interaksi_id,
                        'tanggal' => $tgl,
                        'keterangan' => $keterangan,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    // List realtime
    public function getRealtimeList($interaksi_id)
    {
        $interaksi = InteraksiModel::with('realtime')->findOrFail($interaksi_id);
        return view('rekap.realtime_list', ['realtime' => $interaksi->realtime]);
    }
}
