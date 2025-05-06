<?php

namespace App\Http\Controllers;

use App\Models\PendingUser;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\PasswordGeneratedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AktivasiUserController extends Controller
{
    // Halaman untuk melihat pending users
    public function showPendingUsers()
    {
        $pendingUsers = PendingUser::where('is_active', 0)->get();
 
        return view('admin.aktivasi', compact('pendingUsers'));
    }
 
    // Proses untuk menerima user
    public function acceptUser($id)
    {
        $pendingUser = PendingUser::findOrFail($id);

        $randomPassword = \Illuminate\Support\Str::random(8);

        $user = User::create([
            'name'      => $pendingUser->name,
            'email'     => $pendingUser->email,
            'password'  => \Illuminate\Support\Facades\Hash::make($randomPassword),
            'role'      => $pendingUser->role,
            'is_active' => true,
        ]);

        $pendingUser->delete();

        try {
            Mail::to($user->email)->send(new PasswordGeneratedMail($randomPassword));

            return redirect()->route('admin.pending-users')->with('status', 'User berhasil diterima. Password sudah dikirim ke email.');
        } catch (\Exception $e) {
            return redirect()->route('admin.pending-users')->with('error', 'User berhasil dibuat, tetapi gagal mengirim email: ' . $e->getMessage());
        }
    }
 
    // Proses untuk menolak user
    public function declineUser($id)
    {
        $pendingUser = PendingUser::findOrFail($id);
 
        // Hapus user yang ditolak
        $pendingUser->delete();

        return redirect()->route('admin.pending-users')->with('status', 'User berhasil ditolak.');
    }
}
