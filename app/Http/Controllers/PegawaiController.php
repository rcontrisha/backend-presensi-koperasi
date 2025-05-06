<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    /**
     * Tampilkan semua pegawai
     */
    public function index()
    {
        $pegawaiList = Pegawai::with('user')->get();
        return view('admin.pegawai.index', compact('pegawaiList'));
    }

    /**
     * Tampilkan form tambah pegawai
     */
    public function create()
    {
        return view('admin.pegawai.create');
    }

    /**
     * Tampilkan form edit pegawai
     */
    public function edit($id)
    {
        $pegawai = Pegawai::with('user')->findOrFail($id);
        return view('admin.pegawai.edit', compact('pegawai'));
    }

    /**
     * Update pegawai
     */
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $request->validate([
            'alamat' => 'required|string',
            'no_telpon' => 'required|string',
            'jabatan' => 'required|string',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'pendidikan_terakhir' => 'required|string',
        ]);

        // Update pegawai
        $pegawai->update([
            'alamat' => $request->alamat,
            'no_telpon' => $request->no_telpon,
            'jabatan' => $request->jabatan,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'pendidikan_terakhir' => $request->pendidikan_terakhir,
        ]);

        return redirect()->route('admin.pegawai.index')->with('status', 'Pegawai berhasil diupdate!');
    }

    /**
     * Hapus pegawai
     */
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

        return redirect()->route('admin.pegawai.index')->with('success', 'Data pegawai berhasil dihapus.');
    }
}
