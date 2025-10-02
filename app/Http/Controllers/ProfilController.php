<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfilController extends Controller
{
    public function index()
    {
        $activeMenu = 'profil';
        $breadcrumb = (object) [
            'title' => 'Edit Profil',
            'list' => ['Home', 'Edit Profil']
        ];
        $page = (object) [
            'title' => 'Upload foto'
        ];
        $user = Auth::user();
        $profil = $user->profil;
        return view('profil.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'user' => $user, 'profil' => $profil, 'activeMenu' => $activeMenu]);
    }
    public function update(Request $request)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();

        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);


        // Jika ada avatar lama, hapus dari storage
        if ($user->image) {
            Storage::delete('public/images/' . $user->image);
        }

        if ($request->file('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/images', $imageName);
            $user->image = $imageName;
            $user->save();
        } // Upload image baru

        return redirect('/profil')->with('success', 'Foto Profil Berhasil Diperbarui!');
    }

    public function update_image(Request $request)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);


        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($user->image && Storage::exists($user->image)) {
                Storage::delete($user->image);
            }

            // Simpan gambar baru
            $path = $request->file('image')->store('users/image', 'public');
            $user->image = $path;
            $user->save();
        }

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui');
    }

    public function update_data_diri(Request $request)
    {
        /** @var \App\Models\UserModel $user */
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'ttd'  => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $user->nama = $request->nama;

        if ($request->hasFile('ttd')) {
            // Hapus TTD lama jika ada
            if ($user->ttd && Storage::disk('public')->exists($user->ttd)) {
                Storage::disk('public')->delete($user->ttd);
            }

            // Simpan TTD baru
            $path = $request->file('ttd')->store('users/ttd', 'public');
            $user->ttd = $path;
        }

        $user->save();


        return back()->with('success', 'Data berhasil diperbarui');
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
}
