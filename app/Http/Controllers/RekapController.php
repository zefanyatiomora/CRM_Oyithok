<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

use App\Models\InteraksiModel;
use App\Models\InteraksiRealtime;
use App\Models\InteraksiAwalModel;
use App\Models\KategoriModel;
use App\Models\ProdukModel;
use App\Models\SurveyModel;
use App\Models\CustomersModel;
use App\Models\PasangKirimModel;
use App\Models\RincianModel;
use App\Models\InvoiceModel;
use App\Models\InvoiceDetailModel;
use App\Models\InvoiceKeteranganModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

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
    ->editColumn('tanggal_chat', function ($row) {
        return \Carbon\Carbon::parse($row->tanggal_chat)->format('d-m-Y');
    })
    ->addColumn('aksi', function ($row) {
        return '<button onclick="modalAction(\'' . url('/rekap/' . $row->interaksi_id . '/show_ajax') . '\')" 
                class="btn btn-info btn-sm">Detail</button>';
    })
    ->rawColumns(['aksi'])
    ->make(true);
    }
  public function show_ajax($interaksi_id)
    {
        $interaksi = InteraksiModel::with('customer', 'survey', 'rincian', 'pasang')
            ->findOrFail($interaksi_id);

        $produkList = ProdukModel::select('produk_id', 'produk_nama')->get();
        $interaksiAwalList = InteraksiAwalModel::where('interaksi_id', $interaksi_id)->get();
        $realtimeList = InteraksiRealtime::with('user')
            ->where('interaksi_id', $interaksi->interaksi_id)
            ->orderBy('tanggal', 'desc')
            ->get();

        $invoices = InvoiceModel::whereHas('details.pasang', function ($q) use ($interaksi_id) {
            $q->where('interaksi_id', $interaksi_id);
        })->with(['details.pasang'])->first();

        $steps = ['Identifikasi', 'Survey', 'Rincian', 'Pasang/Kirim'];

        $currentStep = array_search(
            strtolower($interaksi->tahapan),
            array_map('strtolower', $steps)
        );

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
            'realtimeList'      => $realtimeList,
            'produkList'        => $produkList,
            'steps'             => $steps,
            'currentStep'       => $currentStep,
            'skippedSteps'      => $skippedSteps,
            'followUpOptions'   => ['Ghost', 'Ask', 'Follow Up', 'Hold', 'Closing'],
            'selectedFollowUp'  => $interaksi->status ?? '',
            'closeValue'        => $interaksi->close ?? '',
            'interaksiAwalList' => $interaksiAwalList,
            'invoices'          => $invoices
        ]);
    }    // RekapController.php
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
 public function createIdentifikasiAwal(Request $request)
    {
        $interaksi_id = $request->interaksi_id;
        $kategoriList = KategoriModel::all(); // ambil semua kategori
        return view('rekap.identifikasi_awal', compact('interaksi_id', 'kategoriList'));
    }

    /**
     * Tampilkan Data Identifikasi Awal di Halaman Show
     */
    public function showIdentifikasiAwal($interaksi_id)
    {
        $interaksi = InteraksiModel::with('customer')->findOrFail($interaksi_id);
        $kategoriList = KategoriModel::all();
        $interaksiAwalList = InteraksiAwalModel::where('interaksi_id', $interaksi_id)->get();

        return view('rekap.show_ajax', [
            'interaksi' => $interaksi,
            'kategoriList' => $kategoriList,
            'interaksiAwalList' => $interaksiAwalList,
        ]);
    }

    /**
     * Simpan Identifikasi Awal (AJAX)
     */
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

    /**
     * ✅ Ambil Ulang Tabel Identifikasi Awal (tanpa layout)
     * Digunakan untuk AJAX reload
     */
    public function listIdentifikasiAwal($interaksi_id)
    {
        $interaksiAwalList = InteraksiAwalModel::where('interaksi_id', $interaksi_id)->get();

        return view('rekap.partials.identifikasi_tabel', compact('interaksiAwalList'));
    }
public function storeRealtime(Request $request)
{
    $request->validate([
        'interaksi_id' => 'required|exists:interaksi,interaksi_id',
        'tanggal' => 'required|date',
        'keterangan' => 'required|string',
        'user_id' => 'required|exists:m_user,user_id',
    ]);

    // Simpan data baru
    $realtime = InteraksiRealtime::create([
        'interaksi_id' => $request->interaksi_id,
        'tanggal' => $request->tanggal,
        'keterangan' => $request->keterangan,
        'user_id' => $request->user_id,
    ]);

    // Ambil data realtime terbaru untuk interaksi ini
    $realtimeList = InteraksiRealtime::with('user')
        ->where('interaksi_id', $request->interaksi_id)
        ->orderBy('tanggal', 'desc')
        ->get();

    // Render ulang partial tabel realtime
    $html = view('rekap.partials.realtime_tabel', compact('realtimeList'))->render();

    return response()->json([
        'status' => 'success',
        'message' => 'Data realtime berhasil disimpan.',
        'html' => $html
    ]);
}
public function getRealtimeList($interaksi_id)
{
    try {
        $realtimeList = InteraksiRealtime::with('user')
            ->where('interaksi_id', $interaksi_id)
            ->orderBy('tanggal', 'desc')
            ->get();

        // ✅ Gunakan view() yang menghasilkan HTML partial
        $html = view('rekap.partials.realtime_tabel', compact('realtimeList'))->render();

        return response()->json([
            'status' => 'success',
            'html' => $html
        ]);
    } catch (\Exception $e) {
        Log::error("Gagal load realtime list: " . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan server',
            'error' => $e->getMessage()
        ], 500);
    }
}
    public function createRealtime($id_interaksi)
    {
        try {
            $interaksi = InteraksiModel::findOrFail($id_interaksi);
            $picList = UserModel::select('user_id', 'nama')->get();
            $realtime = InteraksiRealtime::with('user')
                ->where('interaksi_id', $id_interaksi)
                ->get();

            return view('rekap.create_realtime', compact('interaksi', 'realtime', 'picList'));
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Interaksi tidak ditemukan.',
                'error'   => $e->getMessage()
            ], 500);
        }
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
    public function getRincianList($interaksi_id)
{
    $rincianList = \App\Models\RincianModel::with(['produk.kategori'])
        ->where('interaksi_id', $interaksi_id)
        ->get();

    $html = view('rekap.partials.rincian_tabel', compact('rincianList'))->render();

    return response()->json([
        'status' => 'success',
        'html' => $html
    ]);
}
    public function createPasang($id_interaksi)
    {
        try {
            $interaksi = InteraksiModel::findOrFail($id_interaksi);
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
            $interaksi = InteraksiModel::findOrFail($id_interaksi);

            $pasang = PasangKirimModel::with('produk')
                ->where('interaksi_id', $id_interaksi)
                ->get();
            $lastInvoice = InvoiceModel::latest()->first();

            return view('rekap.create_invoice', compact('interaksi', 'pasang', 'lastInvoice'));
        } catch (\Exception $e) {
            Log::error('createInvoice error: ' . $e->getMessage());
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
        'deskripsi' => 'required|string|max:255'
    ]);

    try {
        $rincian = RincianModel::create($request->all());

        // Panggil fungsi updateTahapan (jika ada)
        $this->updateTahapan($rincian->interaksi_id, 'Rincian');

        // Ambil tabel terbaru setelah insert
        $rincianList = RincianModel::with(['produk.kategori'])
            ->where('interaksi_id', $rincian->interaksi_id)
            ->get();

        $html = view('rekap.partials.rincian_tabel', compact('rincianList'))->render();

        return response()->json([
            'status' => 'success',
            'message' => 'Rincian berhasil disimpan!',
            'html' => $html
        ]);
    } catch (\Exception $e) {
        Log::error('Store Rincian - Error:', ['message' => $e->getMessage()]);
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan saat menyimpan rincian.'
        ], 500);
    }
}
public function storePasang(Request $request)
{
    $request->validate([
        'interaksi_id'        => 'required|integer|exists:interaksi,interaksi_id',
        'produk_id'           => 'required|integer|exists:produks,produk_id',
        'kuantitas'           => 'required|numeric|min:1',
        'deskripsi'           => 'nullable|string|max:255',
        'jadwal_pasang_kirim' => 'required|date',
        'alamat'              => 'required|string|max:255',
        'status'              => 'required|string',
    ]);

    try {
        $data = $request->only([
            'interaksi_id', 'produk_id', 'kuantitas',
            'deskripsi', 'jadwal_pasang_kirim', 'alamat', 'status'
        ]);

        // Normalisasi format tanggal
        $data['jadwal_pasang_kirim'] = \Carbon\Carbon::createFromFormat('Y-m-d', $data['jadwal_pasang_kirim'])->format('Y-m-d H:i:s');

        // Simpan data pasang
        $pasang = \App\Models\PasangKirimModel::create($data);

        // Ambil interaksi terbaru beserta relasi
        $interaksi = \App\Models\InteraksiModel::with('pasang.produk.kategori')->find($data['interaksi_id']);

        $html = view('rekap.partials.pasang_tabel', compact('interaksi'))->render();

        return response()->json([
            'status' => 'success',
            'message' => 'Pasang/Kirim berhasil disimpan!',
            'html' => $html
        ]);
    } catch (\Exception $e) {
        Log::error('Store Pasang - Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);

        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan saat menyimpan Pasang/Kirim.',
            'error_detail' => $e->getMessage()
        ], 500);
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
            'ppn'         => 'nullable|numeric',
            'nominal_ppn' => 'nullable|numeric',
            'potongan_harga'    => 'nullable|numeric',
            'cashback'          => 'nullable|numeric',
            'total_akhir'       => 'nullable|numeric',
            'dp'                => 'nullable|numeric',
            'tanggal_dp'        => 'nullable|date',
            'tanggal_pelunasan' => 'nullable|date',
            'sisa_pelunasan'    => 'nullable|numeric',
            'catatan'           => 'nullable|string',
            'total_produk'      => 'nullable|integer',
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
            $firstPasang = PasangKirimModel::findOrFail($request->pasangkirim_id[0]);
            $interaksiId = $firstPasang->interaksi_id;

            // simpan header invoice (termasuk total_produk)
            $invoice = InvoiceModel::create([
                'interaksi_id'      => $interaksiId,
                'nomor_invoice'     => $request->nomor_invoice,
                'customer_invoice'  => $request->customer_invoice,
                'pesanan_masuk'     => $request->pesanan_masuk,
                'batas_pelunasan'   => $request->batas_pelunasan,
                'ppn'               => $request->ppn ?? 0,
                'nominal_ppn'       => $request->nominal_ppn ?? 0,
                'potongan_harga'    => $request->potongan_harga ?? 0,
                'cashback'          => $request->cashback ?? 0,
                'total_akhir'       => $request->total_akhir ?? 0,
                'dp'                => $request->dp ?? 0,
                'tanggal_dp'        => $request->tanggal_dp,
                'tanggal_pelunasan' => $request->tanggal_pelunasan,
                'sisa_pelunasan'    => $request->sisa_pelunasan ?? 0,
                'catatan'           => $request->catatan,
                'total_produk'      => $request->total_produk ?? 0,
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

            $invoice->refresh();

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice berhasil dibuat',
                'invoice_id' => $invoice->invoice_id,
                'nomor_invoice' => $invoice->nomor_invoice,
                'customer_invoice' => $invoice->customer_invoice,
                'invoice' => $invoice,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('storeInvoice error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan invoice: ' . $e->getMessage()
            ], 500);
        }
    }
public function storeSurvey(Request $request)
{
    // Validasi input
    $request->validate([
        'interaksi_id'   => 'required|integer|exists:interaksi,interaksi_id',
        'alamat_survey'  => 'required|string|max:255',
        'jadwal_survey'  => 'required|date',
    ]);

    try {
        // Ambil data yang valid
        $data = $request->only(['interaksi_id', 'alamat_survey', 'jadwal_survey']);

        // Normalisasi jadwal_survey ke format datetime MySQL
        try {
            $data['jadwal_survey'] = \Carbon\Carbon::parse($data['jadwal_survey'])->toDateTimeString();
        } catch (\Exception $e) {
            throw new \Exception('Format tanggal/waktu tidak valid: ' . $data['jadwal_survey']);
        }

        // Set status default
        $data['status'] = 'closing survey';

        // Simpan survey
        $survey = \App\Models\SurveyModel::create($data);

        // Update tahapan interaksi (fungsi sudah ada)
        $this->updateTahapan($survey->interaksi_id, 'Survey');

        // Ambil interaksi terbaru beserta survey untuk partial
        $interaksi = \App\Models\InteraksiModel::with('survey')->find($survey->interaksi_id);

        // Render partial survey_tabel
        $html = view('rekap.partials.survey_tabel', compact('interaksi'))->render();

        // Jika request via AJAX, kirim JSON
        if ($request->ajax()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Survey berhasil disimpan!',
                'html'    => $html
            ]);
        }

        // Jika bukan AJAX, redirect biasa
        return redirect()->back()->with('success', 'Survey berhasil disimpan!');
    } catch (\Exception $e) {
        Log::error('Store Survey - Error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan Survey.',
                'error'   => $e->getMessage()
            ], 500);
        }

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
            'deskripsi'           => 'required|string|max:255',
            'alamat'              => 'required|string|max:255',
            'jadwal_pasang_kirim' => 'required|date',
            'status'              => 'required|in:closing all,closing produk,closing pasang',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Tambahkan log alasan validasi gagal
            Log::warning('Update Pasang - Validasi gagal', [
                'errors'   => $validator->errors()->toArray(),
                'payload'  => $request->all(),
                'pasangId' => $pasang_id
            ]);

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
            // Tambahkan log error lengkap
            Log::error('Update Pasang - Error', [
                'message'  => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
                'payload'  => $request->all(),
                'pasangId' => $pasang_id
            ]);

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
    public function editInvoice($invoice_id)
    {
        try {
            // Ambil invoice beserta detail -> pasang -> produk
            $invoice = InvoiceModel::with(['details.pasang.produk'])->findOrFail($invoice_id);

            // Jika invoice belum punya interaksi_id, coba ambil interaksi dari detail->pasang
            if (!$invoice->interaksi && $invoice->details->isNotEmpty()) {
                $firstPasang = $invoice->details->first()->pasang;
                $interaksi = $firstPasang ? $firstPasang->interaksi : null;
            } else {
                $interaksi = $invoice->interaksi;
            }

            // Jika butuh daftar pasang terkait interaksi (untuk menampilkan di view)
            $pasang = $interaksi
                ? PasangKirimModel::with('produk')->where('interaksi_id', $interaksi->interaksi_id)->get()
                : collect();

            // Kirim ke view edit
            return view('rekap.edit_invoice', compact('invoice', 'pasang', 'interaksi'));
        } catch (\Exception $e) {
            Log::error('editInvoice error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Invoice / Interaksi tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }
    public function updateInvoice(Request $request, $invoice_id)
    {
        $request->validate([
            // header
            'nomor_invoice' => 'nullable|string|max:120',
            'customer_invoice' => 'nullable|string|max:255',
            'pesanan_masuk' => 'nullable|date',
            'batas_pelunasan' => 'nullable|in:H+1 setelah pasang,H-1 sebelum kirim',
            // tambahkan validasi PPN & total_produk
            'ppn' => 'nullable|numeric',
            'nominal_ppn' => 'nullable|numeric',
            'total_produk' => 'nullable|numeric',
            'potongan_harga' => 'nullable|numeric',
            'cashback' => 'nullable|numeric',
            'total_akhir' => 'nullable|numeric',
            'dp' => 'nullable|numeric',
            'tanggal_dp' => 'nullable|date',
            'tanggal_pelunasan' => 'nullable|date',
            'sisa_pelunasan' => 'nullable|numeric',
            'catatan' => 'nullable|string',

            // detail arrays
            'pasangkirim_id' => 'required|array',
            'pasangkirim_id.*' => 'required|integer|exists:pasang_kirim,pasangkirim_id',
            'harga_satuan' => 'required|array',
            'harga_satuan.*' => 'numeric',
            'total' => 'required|array',
            'total.*' => 'numeric',
            'diskon' => 'nullable|array',
            'diskon.*' => 'numeric',
            'grand_total' => 'required|array',
            'grand_total.*' => 'numeric',
        ]);

        DB::beginTransaction();
        try {
            $invoice = InvoiceModel::findOrFail($invoice_id);

            // Ambil arrays detail dari request
            $pasangIds = $request->input('pasangkirim_id', []);
            $hargaArr  = $request->input('harga_satuan', []);
            $totalArr  = $request->input('total', []);
            $diskonArr = $request->input('diskon', []);
            $grandArr  = $request->input('grand_total', []);

            // Jika total_produk tidak disertakan, hitung dari grand_total[] sebagai fallback
            $totalProdukFromRequest = $request->input('total_produk', null);
            if ($totalProdukFromRequest === null) {
                $computedTotalProduk = 0;
                foreach ($grandArr as $g) {
                    $computedTotalProduk += (int) $g;
                }
            } else {
                $computedTotalProduk = $totalProdukFromRequest;
            }

            // update header (cast tipe agar konsisten)
            $invoice->nomor_invoice     = $request->input('nomor_invoice');
            $invoice->customer_invoice  = $request->input('customer_invoice');
            $invoice->pesanan_masuk     = $request->input('pesanan_masuk') ?: null;
            $invoice->batas_pelunasan   = $request->input('batas_pelunasan') ?: null;
            $invoice->ppn               = $request->has('ppn') && $request->input('ppn') !== '' ? (float) $request->input('ppn') : 0.0;
            $invoice->nominal_ppn       = $request->has('nominal_ppn') ? (int) $request->input('nominal_ppn') : (int) ($invoice->nominal_ppn ?? 0);
            $invoice->total_produk      = (int) $computedTotalProduk;
            $invoice->potongan_harga    = $request->input('potongan_harga') !== null ? (int) $request->input('potongan_harga') : 0;
            $invoice->cashback          = $request->input('cashback') !== null ? (int) $request->input('cashback') : 0;
            $invoice->total_akhir       = $request->input('total_akhir') !== null ? (int) $request->input('total_akhir') : 0;
            $invoice->dp                = $request->input('dp') !== null ? (int) $request->input('dp') : 0;
            $invoice->tanggal_dp        = $request->input('tanggal_dp') ?: null;
            $invoice->tanggal_pelunasan = $request->input('tanggal_pelunasan') ?: null;
            $invoice->sisa_pelunasan    = $request->input('sisa_pelunasan') !== null ? (int) $request->input('sisa_pelunasan') : 0;
            $invoice->catatan           = $request->input('catatan');

            $invoice->save();

            // sinkron detail: hapus & recreate (sederhana)
            InvoiceDetailModel::where('invoice_id', $invoice->invoice_id)->delete();

            foreach ($pasangIds as $index => $pasangId) {
                InvoiceDetailModel::create([
                    'invoice_id'     => $invoice->invoice_id,
                    'pasangkirim_id' => $pasangId,
                    'harga_satuan'   => isset($hargaArr[$index]) ? $hargaArr[$index] : 0,
                    'total'          => isset($totalArr[$index]) ? $totalArr[$index] : 0,
                    'diskon'         => isset($diskonArr[$index]) ? $diskonArr[$index] : 0,
                    'grand_total'    => isset($grandArr[$index]) ? $grandArr[$index] : 0,
                ]);
            }

            DB::commit();

            // Kembalikan invoice yang diperbarui supaya frontend punya data terbaru
            $invoice->load('details');

            return response()->json([
                'status' => 'success',
                'message' => 'Invoice diperbarui',
                'invoice' => $invoice,
                // opsional: kirim nomor/customer supaya frontend dapat update lastInvoice quick-check
                'nomor_invoice' => $invoice->nomor_invoice,
                'customer_invoice' => $invoice->customer_invoice,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('updateInvoice error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function export_pdf($id)
    {
        $invoice = InvoiceModel::with([
            'details.pasang.produk.kategori', // langsung dari pasang ke produk
            'details.pasang.interaksi.customer' // kalau butuh customer dari interaksi
        ])->findOrFail($id);

        $invoice_keterangan = InvoiceKeteranganModel::first();

        // Load view export-pdf
        $pdf = Pdf::loadView('invoice.export_pdf', compact('invoice', 'invoice_keterangan'))
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true);

        $filename = preg_replace('/[\/\\\\]/', '-', $invoice->nomor_invoice);
        return $pdf->stream('Invoice-' . $filename . '.pdf');
    }
}
