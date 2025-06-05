<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AkunController extends Controller
{
    public function index()
    {
        // Ambil semua akun dengan role pegawai, load relasi pegawai (meskipun bisa null)
        $users = User::where('role', 'pegawai')
                    ->with('pegawai')
                    ->get();

        return view('admin.pegawai.akun', compact('users'));
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->back()->with('success', 'Status akun berhasil diperbarui.');
    }
}
