<?php

namespace App\Http\Controllers;

use App\Models\InteraksiModel;
use App\Models\InteraksiRealtime;
use App\Models\InteraksiAwalModel;
use App\Models\PICModel;
use App\Models\KategoriModel;
use App\Models\ProdukModel;
use App\Models\SurveyModel;
use App\Models\CustomersModel;
use App\Models\PasangKirimModel;
use App\Models\RincianModel;
use App\Models\InvoiceModel;
use App\Models\InvoiceDetailModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



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
            ->select('interaksi_id', 'customer_id', 'tanggal_chat', 'media', 'status');

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
        $interaksi = InteraksiModel::with('customer', 'produk', 'survey', 'rincian', 'pasang')
            ->findOrFail($interaksi_id);

        $produkList = ProdukModel::select('produk_id', 'produk_nama')->get();
        $interaksiAwalList = InteraksiAwalModel::where('interaksi_id', $interaksi_id)->get();
        $picList = PICModel::orderBy('pic_nama')->get();
        $kebutuhanList = InteraksiRealtime::where('interaksi_id', $interaksi_id)
            ->with('pic')
            ->orderBy('tanggal', 'asc')
            ->get();
        $invoices = InvoiceModel::whereHas('details.pasang', function ($q) use ($interaksi_id) {
        $q->where('interaksi_id', $interaksi_id);
    })->with(['details.pasang'])->get();

        $steps = ['Identifikasi', 'Survey', 'Rincian', 'Pasang/Kirim'];

        $currentStep = array_search(
            strtolower($interaksi->tahapan),
            array_map('strtolower', $steps)
        );

        // Ambil skipped steps dari DB
        $skippedSteps = $interaksi->skipsteps
            ? json_decode($interaksi->skipsteps, true)
            : [];

        Log::info('[Edit Interaksi]', [
            'interaksi_id' => $interaksi_id,
            'tahapan'      => $interaksi->tahapan,
            'currentStep'  => $currentStep,
            'skippedSteps' => $skippedSteps,
        ]);

        return view('rekap.show_ajax', [
            'interaksi'         => $interaksi,
            'kebutuhanList'     => $kebutuhanList,
            'produkList'        => $produkList,
            'picList'           => $picList,
            'steps'             => $steps,
            'currentStep'       => $currentStep,
            'skippedSteps'      => $skippedSteps,  // cuma ini yang dipakai di blade
            'followUpOptions'   => ['Ask', 'Follow Up', 'Hold', 'Closing'],
            'selectedFollowUp'  => $interaksi->status ?? '',
            'closeValue'        => $interaksi->close ?? '',
            'interaksiAwalList' => $interaksiAwalList,
            'invoices'          => $invoices
        ]);
    }
    // RekapController.php
    public function updateStatus(Request $request, $interaksi_id)
    {
        Log::info('UpdateStatus dipanggil', [
            'id' => $interaksi_id,
            'status' => $request->status
        ]);

        $interaksi = InteraksiModel::findOrFail($interaksi_id);
        $interaksi->status = $request->status;
        $interaksi->save();
        $interaksi->refresh();
        $customer = $interaksi->customer;
        $customer->refreshLoyalty();


        return response()->json(['success' => true]);
    }
    // Tambah kebutuhan harian
    public function storeRealtime(Request $request)
    {
        $request->validate([
            'interaksi_id' => 'required|exists:interaksi,interaksi_id',
            'tanggal'      => 'required|date',
            'keterangan'   => 'required|string',
            'pic_id'       => 'required|exists:pic,pic_id',
        ]);

        // Simpan ke tabel interaksi_realtime
        InteraksiRealtime::create([
            'interaksi_id' => $request->interaksi_id,
            'tanggal'      => $request->tanggal,
            'keterangan'   => $request->keterangan,
            'pic_id'       => $request->pic_id,
        ]);

        // Update juga ke tabel interaksi (kolom pic_id)
        \App\Models\InteraksiRealtime::where('interaksi_id', $request->interaksi_id)
            ->update([
                'pic_id' => $request->pic_id,
            ]);

        return response()->json(['status' => 'success']);
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

        foreach ($kategori_ids as $kategori_id) {
            $kategori = KategoriModel::find($kategori_id);

            // Simpan ke tabel interaksi_awal
            InteraksiAwalModel::updateOrCreate(
                [
                    'interaksi_id' => $interaksi_id,
                    'kategori_id'  => $kategori_id,
                ],
                [
                    'kategori_nama' => $kategori->kategori_nama
                ]
            );
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

    // List realtime
    public function getRealtimeList($interaksi_id)
    {
        $interaksi = InteraksiModel::with('realtime')->findOrFail($interaksi_id);
        return view('rekap.realtime_list', ['realtime' => $interaksi->realtime]);
    }
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

            $produk = ProdukModel::with('kategori')
                ->select('produk_id', 'produk_nama', 'satuan', 'kategori_id')
                ->get();
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
    public function createPasang($id_interaksi)
    {
        try {
            // Log::info('Create Rincian dipanggil.', ['id_interaksi' => $id_interaksi]);

            $interaksi = InteraksiModel::findOrFail($id_interaksi);
            // Log::info('Interaksi ditemukan.', ['interaksi' => $interaksi]);

            $produk = ProdukModel::with('kategori')
                ->select('produk_id', 'produk_nama', 'satuan', 'kategori_id')
                ->get();
            $closing = ['closing all', 'closing produk', 'closing pasang'];

            $pasang = PasangKirimModel::with('produk')
                ->where('interaksi_id', $id_interaksi)
                ->get();

            return view('rekap.create_pasang', compact('interaksi', 'produk', 'pasang', 'closing'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Interaksi tidak ditemukan.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
public function createInvoice($id_interaksi)
{
    try {
        // Cari interaksi
        $interaksi = InteraksiModel::findOrFail($id_interaksi);

        $pasang = PasangKirimModel::with('produk')
                ->where('interaksi_id', $id_interaksi)
                ->get();
        $lastInvoice = InvoiceModel::latest()->first();

        return view('rekap.create_invoice', compact('interaksi', 'pasang', 'lastInvoice'));
        } catch (\Exception $e) {
            Log::error('createInvoice error: '.$e->getMessage());
            return response()->json([
                'message' => 'Interaksi tidak ditemukan.',
                'error'   => $e->getMessage()
            ], 500);
    }
}

    public function createSurvey($id_interaksi)
    {
        $interaksi = InteraksiModel::findOrFail($id_interaksi);
        return view('rekap.create_survey', compact('interaksi'));
    }
    public function editPasang($id_pasang)
    {
        $pasang = PasangKirimModel::findOrFail($id_pasang);
        $produk = ProdukModel::with('kategori')
            ->select('produk_id', 'produk_nama', 'satuan', 'kategori_id')
            ->get();
        return view('rekap.edit_pasang', compact('pasang', 'produk'));
    }
    public function storeRincian(Request $request)
    {
        $request->validate([
            'interaksi_id' => 'required|integer',
            'produk_id' => 'required|integer',
            'kuantitas' => 'required|numeric',
            'satuan' => 'required|string',
            'deskripsi' => 'required|string|max:255'
        ]);

        try {
            // Simpan data ke database
            $rincian = RincianModel::create($request->all());

            // Panggil fungsi updateTahapan
            $this->updateTahapan($rincian->interaksi_id, 'Rincian');

            return redirect()->back()->with('success', 'Rincian berhasil disimpan!');
        } catch (\Exception $e) {
            Log::error('Store Rincian - Error:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan rincian.');
        }
    }
    public function storePasang(Request $request)
    {
        $request->validate([
            'interaksi_id' => 'required|integer',
            'produk_id' => 'required|integer',
            'kuantitas' => 'required|numeric',
            'satuan' => 'required|string',
            'deskripsi' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'jadwal_pasang_kirim' => 'required|date',
            'status' => 'required|in:closing all,closing produk,closing pasang', // tambahkan validasi status
        ]);

        try {
            // Simpan data ke database
            $pasang = PasangKirimModel::create([
                'interaksi_id'        => $request->interaksi_id,
                'produk_id'           => $request->produk_id,
                'kuantitas'           => $request->kuantitas,
                'satuan'              => $request->satuan,
                'deskripsi'           => $request->deskripsi,
                'alamat'              => $request->alamat,
                'jadwal_pasang_kirim' => $request->jadwal_pasang_kirim,
                'status'              => $request->status,
            ]);

            // Update tahapan
            $this->updateTahapan($pasang->interaksi_id, 'Pasang/Kirim');

            return redirect()->back()->with('success', 'Pasang/Kirim berhasil disimpan!');
        } catch (\Exception $e) {
            Log::error('Store Pasang - Error:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan Pasang.');
        }
    }
public function storeInvoice(Request $request)
{
    $request->validate([
        'pasangkirim_id'   => 'required|array',
        'pasangkirim_id.*' => 'required|integer|exists:pasang_kirim,pasangkirim_id',
        // header invoice
        'nomor_invoice'     => 'nullable|string|max:120',
        'customer_invoice'  => 'nullable|string|max:255',
        'pesanan_masuk'     => 'nullable|date',
        'batas_pelunasan'   => 'nullable|in:H+1 setelah pasang,H-1 sebelum kirim',
        'potongan_harga'    => 'nullable|numeric',
        'cashback'          => 'nullable|numeric',
        'total_akhir'       => 'nullable|numeric',
        'dp'                => 'nullable|numeric',
        'tanggal_dp'        => 'nullable|date',
        'tanggal_pelunasan' => 'nullable|date',
        'sisa_pelunasan'    => 'nullable|numeric',
        'catatan'           => 'nullable|string',
        // detail arrays
        'harga_satuan'   => 'required|array',
        'harga_satuan.*' => 'numeric',
        'total'          => 'required|array',
        'total.*'        => 'numeric',
        'diskon'         => 'nullable|array',
        'diskon.*'       => 'numeric',
        'grand_total'    => 'required|array',
        'grand_total.*'  => 'numeric',
    ]);

    DB::beginTransaction();
    try {
        // Ambil interaksi_id dari pasang pertama yang dipilih
        $firstPasang = \App\Models\PasangKirimModel::findOrFail($request->pasangkirim_id[0]);
        $interaksiId = $firstPasang->interaksi_id;

        // simpan header invoice
        $invoice = InvoiceModel::create([
            'interaksi_id'      => $interaksiId, // auto ambil dari pasang
            'nomor_invoice'     => $request->nomor_invoice,
            'customer_invoice'  => $request->customer_invoice,
            'pesanan_masuk'     => $request->pesanan_masuk,
            'batas_pelunasan'   => $request->batas_pelunasan,
            'potongan_harga'    => $request->potongan_harga ?? 0,
            'cashback'          => $request->cashback ?? 0,
            'total_akhir'       => $request->total_akhir ?? 0,
            'dp'                => $request->dp ?? 0,
            'tanggal_dp'        => $request->tanggal_dp,
            'tanggal_pelunasan' => $request->tanggal_pelunasan,
            'sisa_pelunasan'    => $request->sisa_pelunasan ?? 0,
            'catatan'           => $request->catatan,
        ]);

        // simpan detail invoice
        foreach ($request->pasangkirim_id as $index => $pasangId) {
            InvoiceDetailModel::create([
                'invoice_id'     => $invoice->invoice_id,
                'pasangkirim_id' => $pasangId,
                'harga_satuan'   => $request->harga_satuan[$index],
                'total'          => $request->total[$index],
                'diskon'         => $request->diskon[$index] ?? 0,
                'grand_total'    => $request->grand_total[$index],
            ]);
        }

        DB::commit();
        return response()->json([
            'status' => 'success',
            'message' => 'Invoice berhasil dibuat',
            'invoice_id' => $invoice->invoice_id
        ]);
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('storeInvoice error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menyimpan invoice: '.$e->getMessage()
        ], 500);
    }
}
    public function storeSurvey(Request $request)
    {
        $request->validate([
            'interaksi_id' => 'required|integer',
            'alamat_survey' => 'required|string|max:255',
            'jadwal_survey' => 'required|date',
        ]);
        try {
            // Tambahkan status ke data request
            $data = $request->all();
            $data['status'] = 'closing survey';

            // Simpan data ke database
            $survey = SurveyModel::create($data);
            // Panggil fungsi updateTahapan
            $this->updateTahapan($survey->interaksi_id, 'Survey');

            return redirect()->back()->with('success', 'Survey berhasil disimpan!');
        } catch (\Exception $e) {
            Log::error('Store Survey - Error:', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan Survey.');
        }
    }


    public function editRincian(string $rincian_id)
    {
        $rincian = RincianModel::findOrFail($rincian_id);
        $produk = ProdukModel::with('kategori')
            ->select('produk_id', 'produk_nama', 'satuan', 'kategori_id')
            ->get();
        return view('rekap.edit_rincian', compact('rincian', 'produk'));
    }
    public function updateRincian(Request $request, $rincian_id)
    {
        $rincian = RincianModel::findOrFail($rincian_id);

        $rules = [
            'interaksi_id' => 'required|integer',
            'produk_id' => 'required|integer',
            'motif_id' => 'nullable|integer',
            'kuantitas' => 'required|numeric|min:1',
            'satuan' => 'required|string',
            'deskripsi' => 'nullable|string|max:255',
            'status' => 'required|in:hold,closing', // tambahkan validasi status
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal.',
                'msgField' => $validator->errors(),
            ]);
        }

        $rincian->update($request->only([
            'interaksi_id',
            'produk_id',
            'motif_id',
            'kuantitas',
            'satuan',
            'deskripsi',
            'status'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Data rincian berhasil diperbarui',
        ]);
    }

    public function updatePasang(Request $request, $pasang_id)
    {
        $pasang = PasangKirimModel::findOrFail($pasang_id);

        $rules = [
            'interaksi_id'        => 'required|integer',
            'produk_id'           => 'required|integer',
            'kuantitas'           => 'required|numeric',
            'satuan'              => 'required|string',
            'deskripsi'           => 'required|string|max:255',
            'alamat'              => 'required|string|max:255',
            'jadwal_pasang_kirim' => 'required|date',
            'status'              => 'required|in:closing all,closing produk,closing pasang',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'   => false,
                'message'  => 'Validasi gagal.',
                'msgField' => $validator->errors(),
            ]);
        }

        try {
            $pasang->update([
                'interaksi_id'        => $request->interaksi_id,
                'produk_id'           => $request->produk_id,
                'kuantitas'           => $request->kuantitas,
                'satuan'              => $request->satuan,
                'deskripsi'           => $request->deskripsi,
                'alamat'              => $request->alamat,
                'jadwal_pasang_kirim' => $request->jadwal_pasang_kirim,
                'status'              => $request->status,
            ]);

            // update tahapan (biar sama kayak store)
            $this->updateTahapan($pasang->interaksi_id, 'Pasang/Kirim');

            return response()->json([
                'status'  => true,
                'message' => 'Data Pasang/Kirim berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            Log::error('Update Pasang - Error:', ['message' => $e->getMessage()]);

            return response()->json([
                'status'  => false,
                'message' => 'Terjadi kesalahan saat memperbarui Pasang/Kirim.',
            ]);
        }
    }

    public function updateTahapan($interaksi_id, $tahapanBaru)
    {
        $steps = ['Identifikasi', 'Survey', 'Rincian', 'Pasang/Kirim'];

        $interaksi = InteraksiModel::findOrFail($interaksi_id);

        $originalStep = $interaksi->original_step ?? 0;
        $currentStep  = array_search(strtolower($tahapanBaru), array_map('strtolower', $steps));

        // Ambil skip lama
        $existingSkips = $interaksi->skipsteps ? json_decode($interaksi->skipsteps, true) : [];

        // Kalau user mengisi step yang sebelumnya skip → hapus dari skipped
        if (in_array($currentStep, $existingSkips)) {
            $existingSkips = array_diff($existingSkips, [$currentStep]);
        }

        $allSkips = $existingSkips;
        $newStep  = $originalStep; // default tetap di step terakhir

        // Kalau user maju → hitung skip baru & update step
        if ($currentStep > $originalStep) {
            $skippedSteps = [];
            if ($currentStep > $originalStep + 1) {
                for ($i = $originalStep + 1; $i < $currentStep; $i++) {
                    $skippedSteps[] = $i;
                }
            }

            $allSkips = array_values(array_unique(array_merge($existingSkips, $skippedSteps)));
            $newStep  = $currentStep; // update tahapan hanya saat maju
        }

        // Update interaksi
        $interaksi->update([
            'tahapan'       => $steps[$newStep],
            'original_step' => $newStep,
            'skipsteps'     => json_encode($allSkips),
        ]);

        Log::info('[Update Tahapan]', [
            'interaksi_id' => $interaksi_id,
            'tahapan'      => $steps[$newStep],
            'originalStep' => $originalStep,
            'currentStep'  => $currentStep,
            'skipped'      => $allSkips,
        ]);

        return $interaksi;
    }
    
}

