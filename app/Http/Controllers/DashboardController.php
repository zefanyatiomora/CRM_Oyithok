<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\InteraksiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DashboardController extends Controller
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

    $queryBase = InteraksiModel::whereYear('tanggal_chat', $tahun);
    if ($bulan) {
        $queryBase->whereMonth('tanggal_chat', $bulan);
    }

    // === Jumlah berdasarkan tahapan (sudah ada) ===
    $jumlahInteraksi = (clone $queryBase)->count();
    $prosesSurvey    = (clone $queryBase)->where('tahapan', 'survey')->count();
    $prosesPasang    = (clone $queryBase)->where('tahapan', 'pasang')->count();
    $prosesOrder     = (clone $queryBase)->where('tahapan', 'order')->count();

    // === Tambahan: Jumlah berdasarkan STATUS ===
    $jumlahAsk      = (clone $queryBase)->where('status', 'ask')->count();
    $jumlahFollowUp = (clone $queryBase)->where('status', 'follow up')->count();
    $jumlahHold     = (clone $queryBase)->where('status', 'hold')->count();
    $jumlahClosing  = (clone $queryBase)->where('status', 'closing')->count();

    $availableYears = InteraksiModel::selectRaw('YEAR(tanggal_chat) as year')
        ->distinct()->orderBy('year', 'desc')->pluck('year');

    $bulanList = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    // === Logika Chart (biarkan tetap seperti punyamu) ===
    $chartLabels   = [];
    $dataLeadsBaru = [];
    $dataLeadsLama = [];
    if ($bulan) {
        $jumlahHari = \Carbon\Carbon::create($tahun, $bulan)->daysInMonth;
        for ($hari = 1; $hari <= $jumlahHari; $hari++) {
            $chartLabels[] = $hari;
            $queryPerHari = InteraksiModel::whereYear('tanggal_chat', $tahun)
                ->whereMonth('tanggal_chat', $bulan)
                ->whereDay('tanggal_chat', $hari);

            $totalInteraksiHariIni = (clone $queryPerHari)->count();
            $leadsBaruHariIni = (clone $queryPerHari)->whereHas('customer', function ($q) use ($tahun, $bulan) {
                $q->whereYear('created_at', $tahun)->whereMonth('created_at', $bulan);
            })->count();

            $dataLeadsBaru[] = $leadsBaruHariIni;
            $dataLeadsLama[] = $totalInteraksiHariIni - $leadsBaruHariIni;
        }
    } else {
        foreach ($bulanList as $key => $namaBulan) {
            $chartLabels[] = $namaBulan;
            $queryPerBulan = InteraksiModel::whereYear('tanggal_chat', $tahun)
                ->whereMonth('tanggal_chat', $key);

            $totalInteraksiBulanIni = (clone $queryPerBulan)->count();
            $leadsBaruBulanIni = (clone $queryPerBulan)->whereHas('customer', function ($q) use ($tahun, $key) {
                $q->whereYear('created_at', $tahun)->whereMonth('created_at', $key);
            })->count();

            $dataLeadsBaru[] = $leadsBaruBulanIni;
            $dataLeadsLama[] = $totalInteraksiBulanIni - $leadsBaruBulanIni;
        }
    }

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

        // kirim ke view
        'jumlahAsk' => $jumlahAsk,
        'jumlahFollowUp' => $jumlahFollowUp,
        'jumlahHold' => $jumlahHold,
        'jumlahClosing' => $jumlahClosing,

        'chartLabels' => $chartLabels,
        'dataLeadsLama' => $dataLeadsLama,
        'dataLeadsBaru' => $dataLeadsBaru,
    ]);
}

    public function update(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Jika ada avatar lama, hapus dari storage
        if ($user->avatar) {
            Storage::delete('public/avatars/' . $user->avatar);
        }

        if ($request->file('avatar')) {
            $avatarName = time() . '.' . $request->avatar->extension();
            $request->avatar->storeAs('public/avatars', $avatarName);
            /** @var \App\Models\User $user **/
            $user->avatar = $avatarName;
            $user->save();
        } // Upload avatar baru

        return redirect('/profil')->with('success', 'Foto Profil Berhasil Diperbarui!');
    }
    public function updateDataDiri(Request $request)
    {
        // Validasi input menggunakan Validator langsung
        $rules = [
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|string|max:50',
            'no_hp' => 'required|string|max:15',
            'alamat' => 'required|string|max:255',
        ];

        // Jalankan validasi
        $validator = Validator::make($request->all(), $rules);

        // Jika validasi gagal, kembalikan pesan error dalam format JSON
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors(),
            ]);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->nama = $request->nama;
        $user->save();

        // Update data profil di tabel profil_user
        $profil = $user->profil; // Mengambil data profil yang terkait

        // Periksa jika profil ada
        if ($profil) {
            $profil->tempat_lahir = $request->tempat_lahir;
            $profil->tanggal_lahir = $request->tanggal_lahir;
            $profil->jenis_kelamin = $request->jenis_kelamin;
            $profil->agama = $request->agama;
            $profil->no_hp = $request->no_hp;
            $profil->alamat = $request->alamat;
            $profil->save(); // Simpan perubahan pada profil
        } else {
            return redirect('/profil')->with('error', 'Profil tidak ditemukan.');
        }

        return redirect('/profil')->with('success', 'Data Diri Berhasil Diperbarui!');
        //     // Kembalikan response sukses jika semua berhasil
        //     return response()->json([
        //         'status' => true,
        //         'message' => 'Data Diri Berhasil Diperbarui!',
        //     ]);
        // } else {
        //     // Jika profil tidak ditemukan, kembalikan pesan error dalam format JSON
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Profil tidak ditemukan.',
        //     ]);
        // }
    }
    public function updatePassword(Request $request)
    {
        // Validasi input
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:5',
            'confirm_password' => 'required|same:new_password',
        ]);

        // Cek apakah password lama sesuai dengan password user yang sedang login
        $currentPassword = Auth::user()->password;
        if (!Hash::check($request->old_password, $currentPassword)) {
            return redirect()->back()->withErrors(['old_password' => 'Password lama tidak sesuai']);
        }

        /** @var \App\Models\User $user */
        // Update password baru
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil diubah');
    }
   public function ask(Request $request)
{
    $tahun = $request->get('tahun', date('Y'));
    $bulan = $request->get('bulan');

    $query = InteraksiModel::where('status', 'ask')
        ->whereYear('tanggal_chat', $tahun);

    if ($bulan) {
        $query->whereMonth('tanggal_chat', $bulan);
    }

    $interaksi = $query->get();

    $activeMenu = 'dashboard'; // 👈 tambahkan ini

    return view('dashboard.ask', compact('interaksi', 'tahun', 'bulan', 'activeMenu'));
}

}
