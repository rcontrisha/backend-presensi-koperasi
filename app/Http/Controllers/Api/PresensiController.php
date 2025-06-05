<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PresensiController extends Controller
{
    public function statusHariIni(Request $request)
    {
        $user = Auth::user();
        $tanggalHariIni = now()->toDateString();

        // Cek presensi hari ini berdasarkan tanggal dari waktu_presensi
        $presensi = Presensi::where('user_id', $user->id)
            ->whereDate('waktu_presensi', $tanggalHariIni)
            ->first();

        $response = [
            'status' => 'belum presensi',
            'jam_hadir' => null,
            'jam_pulang' => null,
        ];

        if ($presensi) {
            $response['status'] = 'sudah absen masuk';
            $response['jam_hadir'] = \Carbon\Carbon::parse($presensi->waktu_presensi)->format('H:i:s');

            if ($presensi->waktu_pulang) {
                $response['status'] = 'sudah absen pulang';
                $response['jam_pulang'] = \Carbon\Carbon::parse($presensi->waktu_pulang)->format('H:i:s');
            }
        }

        return response()->json($response);
    }

    // Menangani presensi masuk dan pulang dalam satu fungsi
    public function presensi(Request $request)
    {
        $user = Auth::user();
        
        // Cek apakah user sudah presensi hari ini
        $presensi = Presensi::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->first();

        // Validasi input
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($presensi) {
            // Jika sudah ada presensi masuk, artinya user sedang melakukan presensi pulang
            if ($presensi->waktu_pulang) {
                return response()->json(['message' => 'Anda sudah presensi pulang hari ini'], 400);
            }

            // Validasi foto pulang jika user melakukan presensi pulang
            $request->validate([
                'foto_pulang' => 'required|image|mimes:jpeg,png,jpg,gif',
            ]);

            // Menyimpan foto presensi pulang
            $fotoPulangPath = $request->file('foto_pulang')->store('presensi_foto', 'public');

            // Update presensi dengan waktu pulang
            $presensi->foto_pulang = $fotoPulangPath;
            $presensi->latitude = $request->latitude;
            $presensi->longitude = $request->longitude;
            $presensi->waktu_pulang = Carbon::now();
            $presensi->save();

            return response()->json(['message' => 'Presensi pulang berhasil', 'data' => $presensi], 200);
        } else {
            // Jika belum ada presensi masuk, artinya user sedang melakukan presensi masuk
            // Validasi foto masuk jika user melakukan presensi masuk
            $request->validate([
                'foto_masuk' => 'required|image|mimes:jpeg,png,jpg,gif',
            ]);

            // Menyimpan foto presensi masuk
            $fotoMasukPath = $request->file('foto_masuk')->store('presensi_foto', 'public');

            // Membuat presensi masuk
            $presensi = new Presensi();
            $presensi->user_id = $user->id;
            $presensi->foto_masuk = $fotoMasukPath;
            $presensi->latitude = $request->latitude;
            $presensi->longitude = $request->longitude;
            $presensi->waktu_presensi = Carbon::now();
            $presensi->save();

            return response()->json(['message' => 'Presensi masuk berhasil', 'data' => $presensi], 200);
        }
    }

    public function riwayatPresensi(Request $request)
    {
        $user = Auth::user();

        // Ambil riwayat presensi milik user, urutkan dari terbaru
        $riwayat = Presensi::where('user_id', $user->id)
            ->orderBy('waktu_presensi', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => \Carbon\Carbon::parse($item->waktu_presensi)->format('Y-m-d'),
                    'jam_masuk' => $item->waktu_presensi ? \Carbon\Carbon::parse($item->waktu_presensi)->format('H:i:s') : null,
                    'jam_pulang' => $item->waktu_pulang ? \Carbon\Carbon::parse($item->waktu_pulang)->format('H:i:s') : null,
                    'foto_masuk' => $item->foto_masuk ? asset('storage/' . $item->foto_masuk) : null,
                    'foto_pulang' => $item->foto_pulang ? asset('storage/' . $item->foto_pulang) : null,
                    'lokasi_masuk' => [$item->latitude, $item->longitude],
                ];
            });

        return response()->json([
            'message' => 'Riwayat presensi berhasil diambil',
            'data' => $riwayat,
        ]);
    }
}
