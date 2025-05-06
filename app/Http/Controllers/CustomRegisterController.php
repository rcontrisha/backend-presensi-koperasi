<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendingUser;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CustomRegisterController extends Controller
{
    // Menampilkan halaman form register
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request): JsonResponse|RedirectResponse
    {
        // Validasi input
        $validated = Validator::make($request->all(), [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:pending_users,email',
        ]);

        if ($validated->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Validasi gagal',
                    'errors' => $validated->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($validated)->withInput();
        }

        // Simpan ke tabel pending_users
        $pendingUser = PendingUser::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => 'pegawai',
            'is_active' => false,
        ]);

        // Respons untuk API (Flutter)
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pendaftaran berhasil, tunggu persetujuan dari admin.',
                'data' => $pendingUser
            ]);
        }

        // Respons untuk Web
        return redirect()->route('login')->with('status', 'Pendaftaran berhasil, tunggu persetujuan dari admin.');
    }
}
