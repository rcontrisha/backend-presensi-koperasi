<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Ambil data profil pegawai yang sedang login
     */
    public function show()
    {
        $user = Auth::user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (!$pegawai) {
            return response()->json([
                'message' => 'Profil pegawai belum diisi.',
                'data' => null,
            ], 200);
        }

        return response()->json([
            'message' => 'Profil pegawai ditemukan.',
            'data' => $pegawai,
        ], 200);
    }

    /**
     * Buat data profil pegawai baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Cek jika sudah pernah membuat profil
        if (Pegawai::where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'Profil sudah ada. Gunakan endpoint update untuk memperbarui.',
            ], 409);
        }

        $request->validate([
            'alamat' => 'required|string',
            'no_telpon' => 'required|string',
            'jabatan' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'pendidikan_terakhir' => 'required|string',
        ]);

        $pegawai = Pegawai::create([
            'user_id' => $user->id,
            'alamat' => $request->alamat,
            'no_telpon' => $request->no_telpon,
            'jabatan' => $request->jabatan,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
        ]);

        return response()->json([
            'message' => 'Profil pegawai berhasil dibuat.',
            'data' => $pegawai,
        ], 201);
    }

    /**
     * Update data profil pegawai
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $pegawai = Pegawai::where('user_id', $user->id)->first();
        if (!$pegawai) {
            return response()->json([
                'message' => 'Profil belum ada. Gunakan endpoint store untuk membuat.',
            ], 404);
        }

        $request->validate([
            'alamat' => 'nullable|string',
            'no_telpon' => 'nullable|string',
            'jabatan' => 'nullable|string',
            'tempat_lahir' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|in:L,P',
            'pendidikan_terakhir' => 'nullable|string',
        ]);

        $pegawai->update([
            'alamat' => $request->alamat,
            'no_telpon' => $request->no_telpon,
            'jabatan' => $request->jabatan,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
        ]);

        return response()->json([
            'message' => 'Profil pegawai berhasil diperbarui.',
            'data' => $pegawai,
        ], 200);
    }
}
