<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordGeneratedMail;

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

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        // Generate random password
        $password = Str::random(8);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => 'pegawai',
            'is_active' => true,
        ]);

        // Kirim password ke email user dengan tampilan proper
        try {
            Mail::to($user->email)->send(new \App\Mail\PasswordGeneratedMail($password));
        } catch (\Exception $e) {
            // Jika gagal kirim email, tetap lanjut
        }

        // Simpan password ke session agar bisa ditampilkan ke admin sekali saja
        return redirect()->back()->with([
            'success' => 'Akun pegawai berhasil dibuat.',
            'generated_password' => $password,
            'generated_email' => $user->email,
        ]);
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->back()->with('success', 'Status akun berhasil diperbarui.');
    }
}
