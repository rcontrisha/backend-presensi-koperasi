<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class IzinController extends Controller
{
    private static $daftarJenisIzin = [
        'Diperbantukan atau Ditugaskan pada Instansi Vertikal',
        'Melaksanakan Cuti',
        'Menghadiri Rapat, Perjalanan Dinas, dan Tugas Lain yang Berkaitan dengan Kedinasan',
        'Mengikuti Diklat',
        'Presensi Manual',
        'Tugas Belajar',
        'Tugas Kedinasan',
    ];

    // ✅ 1. Ambil semua izin milik pegawai yang login (dengan filter opsional bulan/tahun)
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Izin::where('user_id', $user->id)
                     ->orderBy('tanggal_mulai', 'desc');

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal_mulai', $request->bulan)
                  ->whereYear('tanggal_mulai', $request->tahun);
        }

        $izinList = $query->get();

        return response()->json([
            'status' => true,
            'message' => 'Daftar izin berhasil diambil',
            'data' => $izinList
        ]);
    }

    // ✅ 2. Ambil detail izin tertentu milik pegawai yang login
    public function show($id)
    {
        $user = Auth::user();

        $izin = Izin::where('id', $id)->where('user_id', $user->id)->first();

        if (!$izin) {
            return response()->json([
                'status' => false,
                'message' => 'Data izin tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Detail izin berhasil diambil',
            'data' => $izin
        ]);
    }

    // ✅ 3. Ajukan izin baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jenis_izin' => ['required', 'string', Rule::in(self::$daftarJenisIzin)],
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string|max:255',
            'file_pendukung' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', // maksimal 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        $filePath = null;
        if ($request->hasFile('file_pendukung')) {
            $filePath = $request->file('file_pendukung')->store('izin_pendukung', 'public');
        }

        $izin = Izin::create([
            'user_id' => $user->id,
            'jenis_izin' => $request->jenis_izin,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan' => $request->keterangan,
            'status' => 'menunggu',
            'file_pendukung' => $filePath,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Pengajuan izin berhasil dikirim',
            'data' => $izin
        ], 201);
    }
}
