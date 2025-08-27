<?php

namespace App\Http\Controllers;

use App\Models\InteraksiModel;
use App\Models\InteraksiRealtime;
use App\Models\InteraksiAwalModel;
use App\Models\KategoriModel;
use App\Models\PICModel;
use App\Models\ProdukModel;
use App\Models\RincianModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

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
            ->select('interaksi_id', 'customer_id', 'awal_id', 'realtime_id', 'rincian_id', 'tanggal_chat', 'media', 'status');

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

    public function show_ajax($interaksi_id)
    {
        $interaksi = InteraksiModel::with('customer', 'produk')->findOrFail($interaksi_id);
        $produkList = ProdukModel::all(); // ambil semua produk untuk dropdown
        $interaksiAwalList = InteraksiAwalModel::where('interaksi_id', $interaksi_id)->get();
        $interaksi = InteraksiModel::with('customer', 'produk', 'rincian')->findOrFail($interaksi_id);
        $produkList = ProdukModel::select('produk_id', 'produk_nama')->get();
           $picList = PICModel::select('pic_id', 'pic_nama')->orderBy('pic_nama')->get(); 

        $steps = ['Identifikasi', 'Survey', 'Rincian', 'Pasang', 'Done'];

        $originalStep = array_search(
            strtolower($interaksi->tahapan),
            array_map('strtolower', $steps)
        );

        $currentStep = $originalStep; // default sama, nanti berubah di update

        Log::info('[Edit Interaksi]', [
            'interaksi_id' => $interaksi_id,
            'tahapan'      => $interaksi->tahapan,
            'originalStep' => $originalStep,
            'currentStep'  => $currentStep,
        ]);
        // Log::info('[Edit Interaksi]', [
        //     'interaksi_id' => $id,
        //     'tahapan'      => $interaksi->tahapan,
        //     'originalStep' => $originalStep,
        //     'currentStep'  => $currentStep,
        // ]);
        return view('rekap.show_ajax', [
            'interaksi' => $interaksi,
            'produkList' => $produkList,
            'followUpOptions' => ['Ask', 'Follow Up', 'Closing Survey', 'Closing Pasang', 'Closing Product', 'Closing ALL'],
            'selectedFollowUp' => $interaksi->status ?? '',
            'closeValue'       => $interaksi->close ?? '',
            'steps'       => $steps,
            'originalStep'       => $originalStep,
            'currentStep'       => $currentStep,
            'interaksiAwalList' => $interaksiAwalList, // <-- pastikan dikirim
            'picList'          => $picList
        ]);
    }
    public function updateFollowUp(Request $request)
    {
        Log::info('updateFollowUp data diterima:', $request->all());

        $validated = $request->validate([
            'interaksi_id' => 'required|exists:interaksi,interaksi_id',
            'customer_id'  => 'required|exists:customers,customer_id',
            'status'       => 'required|string',
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
    public function createIdentifikasiAwal(Request $request)
    {
        $interaksi_id = $request->interaksi_id;
        $kategoriList = KategoriModel::all(); // ambil semua kategori
        return view('rekap.identifikasi_awal', compact('interaksi_id', 'kategoriList'));
    }

    public function showIdentifikasiAwal($interaksi_id)
    {
        $interaksi = InteraksiModel::with('customer')->findOrFail($interaksi_id);
        $kategoriList = KategoriModel::all(); // ambil semua kategori
        $interaksiAwalList = InteraksiAwalModel::where('interaksi_id', $interaksi_id)->get();

        return view('rekap.show_ajax', [
            'interaksi' => $interaksi,
            'kategoriList' => $kategoriList,
            'interaksiAwalList' => $interaksiAwalList,
        ]);
    }
    public function storeIdentifikasiAwal(Request $request)
    {
        $request->validate([
            'interaksi_id' => 'required|exists:interaksi,interaksi_id',
            'kategori_id'  => 'required|array',
            'kategori_id.*' => 'exists:kategoris,kategori_id'
        ]);

        $interaksi_id = $request->interaksi_id;
        $kategori_ids = $request->kategori_id;

        $awal = null; // siapkan variabel untuk menampung hasil terakhir

        foreach ($kategori_ids as $kategori_id) {
            $kategori = KategoriModel::find($kategori_id);

            $awal = InteraksiAwalModel::updateOrCreate(
                [
                    'interaksi_id' => $interaksi_id,
                    'kategori_id'  => $kategori_id,
                ],
                [
                    'kategori_nama' => $kategori->kategori_nama
                ]
            );
        }

        // update FK di tabel interaksi pakai awal terakhir
        if ($awal) {
            $interaksi = InteraksiModel::find($interaksi_id);
            if ($interaksi) {
                $interaksi->awal_id = $awal->awal_id;
                $interaksi->save();
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Kategori berhasil ditambahkan ke identifikasi awal'
        ]);
    }
    public function listIdentifikasiAwal($interaksi_id)
    {
        $interaksiAwalList = InteraksiAwalModel::where('interaksi_id', $interaksi_id)
            ->with('kategoris')
            ->get();

        return view('rekap.identifikasi_list', compact('interaksiAwalList'));
    }
public function storeRealtime(Request $request)
{
    $request->validate([
        'interaksi_id' => 'required|exists:interaksi,interaksi_id',
        'tanggal' => 'required|array',
        'tanggal.*' => 'nullable|date',
        'keterangan' => 'required|array',
        'keterangan.*' => 'nullable|string',
        'pic_id' => 'required|array',
        'pic_id.*' => 'nullable|integer',
    ]);

    $interaksi_id = $request->interaksi_id;

    foreach ($request->tanggal as $i => $tgl) {
        if ($tgl || $request->keterangan[$i] || $request->pic[$i]) {
InteraksiRealtime::updateOrCreate(
    [
        'interaksi_id' => $interaksi_id,
        'tanggal' => $tgl,
    ],
    [
        'keterangan' => $request->keterangan[$i],
        'pic_id' => $request->pic_id[$i] ?? null,
    ]
);

        }
    }

    $list = InteraksiRealtime::with('pic')
        ->where('interaksi_id', $interaksi_id)
        ->orderBy('tanggal')
        ->get();

    return response()->json([
        'status' => 'success',
        'list' => $list
    ]);
}
    public function listRealtime($interaksi_id)
    {
        $list = InteraksiRealtime::with('pic')
            ->where('interaksi_id', $interaksi_id)
            ->orderBy('tanggal')
            ->get();

        return view('rekap.realtime_list', compact('list'))->render();
    }

    public function deleteRealtime($id)
    {
        $item = InteraksiRealtime::findOrFail($id);
        $item->delete();

        return response()->json(['status' => 'success']);
    }
    // List realtime
    // public function getRealtimeList($interaksi_id)
    // {
    //     $interaksi = InteraksiModel::with('realtime')->findOrFail($interaksi_id);
    //     return view('rekap.realtime_list', ['realtime' => $interaksi->realtime]);
    // }
    public function searchProduct(Request $request)
    {
        $keyword = $request->get('keyword');

        $produks = ProdukModel::where('produk_nama', 'like', "%$keyword%")
            ->get(['produk_id as id', 'produk_nama as text']);

        return response()->json(['results' => $produks]);
    }
    public function createRincian($id_interaksi)
    {
        try {
            // Log::info('Create Rincian dipanggil.', ['id_interaksi' => $id_interaksi]);

            $interaksi = InteraksiModel::findOrFail($id_interaksi);
            // Log::info('Interaksi ditemukan.', ['interaksi' => $interaksi]);

            $produk = ProdukModel::select('produk_id', 'produk_nama')->get();

            $rincian = RincianModel::with('produk')
                ->where('interaksi_id', $id_interaksi)
                ->get();

            return view('rekap.create_rincian', compact('interaksi', 'produk', 'rincian'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Interaksi tidak ditemukan.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function storeRincian(Request $request)
    {
        $request->validate([
            'interaksi_id' => 'required|integer',
            'produk_id' => 'required|integer',
            'motif_id' => 'nullable|integer',
            'kuantitas' => 'required|numeric',
            'satuan' => 'required|string',
            'deskripsi' => 'required|string|max:255'
        ]);

        try {
            // Log input request
            Log::info('Store Rincian - Input request:', $request->all());

            // Simpan data ke database
            $rincian = RincianModel::create($request->all());

            // Log hasil setelah create
            Log::info('Store Rincian - Data berhasil disimpan:', $rincian->toArray());

            return response()->json([
                'message' => 'Data Rincian berhasil disimpan.',
            ], 200);
        } catch (\Exception $e) {
            // Log error lengkap
            Log::error('Store Rincian - Gagal menyimpan data', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->all()
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
