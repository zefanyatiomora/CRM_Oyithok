<?php

namespace App\Http\Controllers;

use App\Models\InteraksiModel;
use App\Models\CustomersModel;
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

        $bulanList = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
        ];

        $customer = CustomersModel::all();

        $judulRekap = $bulan
            ? "Rekap Bulan " . $bulanList[$bulan] . " $tahun"
            : "Rekap Tahun $tahun";

        $breadcrumb = (object) [
            'title' => $judulRekap,
            'list' => ['Home', $judulRekap]
        ];

        $page = (object) [
            'title' => $judulRekap
        ];

        return view('rekap.index', compact('breadcrumb', 'page', 'activeMenu', 'tahun', 'bulan', 'bulanList', 'customer'));
    }

    public function list(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        Log::info('RekapController@list: Filter Tahun = ' . $tahun . ', Bulan = ' . $bulan);

        $query = InteraksiModel::with(['customer', 'followups'])
            ->select('interaksi_id', 'customer_id', 'produk_id', 'produk_nama', 'tanggal_chat', 'identifikasi_kebutuhan');

        if ($tahun) {
            $query->whereYear('tanggal_chat', $tahun);
        }

        if ($bulan) {
            $query->whereMonth('tanggal_chat', $bulan);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('produk_nama', fn($row) => $row->produk_nama ?? '-')
            ->addColumn('aksi', function ($row) {
                $btn = '<button onclick="modalAction(\'' . url('/rekap/' . $row->interaksi_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/rekap/' . $row->interaksi_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function show_ajax($id)
    {
      $interaksi = InteraksiModel::with('customer')->findOrFail($id);

return view('rekap.show_ajax', [
    'interaksi' => $interaksi,
    'followUpOptions' => ['Follow Up 1', 'Follow Up 2', 'Closing Survey', 'Closing Pasang', 'Closing Product', 'Closing ALL'],
    'selectedFollowUp' => $interaksi->follow_up ?? '',
    'closeValue'       => $interaksi->close ?? ''
]);
    }
public function updateFollowUp(Request $request)
{
    Log::info('updateFollowUp data diterima:', $request->all());

    $validated = $request->validate([
        'interaksi_id' => 'required|exists:interaksi,interaksi_id',
        'customer_id'  => 'required|exists:customers,customer_id',
        'follow_up'    => 'required|string',
        'tahapan'      => 'required|string',
        'pic'          => 'required|string',
    ]);

    $closeValue = match ($validated['follow_up']) {
        'Follow Up 1'    => 'Follow Up 2',
        'Follow Up 2'    => 'Broadcast',
        'Closing Survey',
        'Closing Pasang',
        'Closing Product',
        'Closing ALL'    => 'Closing',
        default          => 'Follow Up 1',
    };

    try {
        InteraksiModel::where('interaksi_id', $validated['interaksi_id'])
            ->update([
                'tahapan'   => $validated['tahapan'],
                'pic'       => $validated['pic'],
                'follow_up' => $validated['follow_up'],
                'close'     => $closeValue,
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data follow up berhasil disimpan',
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

}
