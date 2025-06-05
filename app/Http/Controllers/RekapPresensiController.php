<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RekapPresensiController extends Controller
{
    public function index()
    {
        $bulanIni = Carbon::now()->format('m');
        $tahunIni = Carbon::now()->format('Y');
        $hariIni = Carbon::now()->format('Y-m-d'); // Ambil hari ini

        // Ambil data libur nasional
        $liburNasional = $this->getLiburNasional($bulanIni, $tahunIni);

        // Ambil semua pegawai
        $pegawai = User::where('role', 'pegawai')
            ->whereHas('pegawai') // hanya ambil user yang punya relasi ke tabel pegawai
            ->get();

        $rekap = $pegawai->map(function ($user) use ($bulanIni, $tahunIni, $liburNasional, $hariIni) {
            // Hitung hari kerja
            [$totalHariKerja, $listHariKerja] = $this->countHariKerja($bulanIni, $tahunIni, $liburNasional);

            // Filter hari kerja hanya sampai hari ini
            $totalHariKerjaSampaiHariIni = collect($listHariKerja)
                ->filter(function ($tanggal) use ($hariIni) {
                    return $tanggal['tanggal'] <= $hariIni;
                })
                ->count();

            // Jumlah presensi (hadir)
            $jumlahPresensi = $user->presensis()
                ->whereMonth('waktu_presensi', $bulanIni)
                ->whereYear('waktu_presensi', $tahunIni)
                ->whereDate('waktu_presensi', '<=', $hariIni)
                ->count();

            // Jumlah hari izin diterima
            $jumlahIzinDiterima = $user->izins()
                ->where('status', 'Diterima')
                ->whereMonth('tanggal_mulai', $bulanIni)
                ->whereYear('tanggal_mulai', $tahunIni)
                ->whereDate('tanggal_mulai', '<=', $hariIni)
                ->count();

            // Total kehadiran = presensi + izin
            $totalHadir = $jumlahPresensi + $jumlahIzinDiterima;

            // Hitung jumlah absen
            $jumlahAbsen = $totalHariKerjaSampaiHariIni - $totalHadir;

            // Hitung persentase kehadiran
            $presentase = $totalHariKerjaSampaiHariIni > 0 ? round(($totalHadir / $totalHariKerjaSampaiHariIni) * 100, 2) : 0;

            return [
                'id' => $user->id,
                'nama' => $user->name,
                'jumlah_presensi' => $jumlahPresensi,
                'jumlah_izin' => $jumlahIzinDiterima,
                'jumlah_absen' => $jumlahAbsen,
                'total_hari_kerja' => $totalHariKerjaSampaiHariIni,
                'presentase' => $presentase,
            ];
        });

        return view('admin.rekap-presensi.index', compact('rekap'));
    }

    public function detail(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $query = $user->presensis()->orderBy('waktu_presensi', 'asc');

        // Filter berdasarkan bulan & tahun kalau ada input
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('waktu_presensi', $request->bulan)
                ->whereYear('waktu_presensi', $request->tahun);
        } else {
            // Default ke bulan ini
            $query->whereMonth('waktu_presensi', Carbon::now()->month)
                ->whereYear('waktu_presensi', Carbon::now()->year);
        }

        $presensis = $query->get();

        // Mengecek apakah request adalah AJAX
        if ($request->ajax()) {
            // Kembalikan hanya bagian tabel presensi
            return view('admin.rekap-presensi.table', compact('presensis'));
        }

        return view('admin.rekap-presensi.detail', compact('user', 'presensis'));
    }

    public function cetakPdf(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $query = $user->presensis()->orderBy('waktu_presensi', 'asc');

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('waktu_presensi', $request->bulan)
                ->whereYear('waktu_presensi', $request->tahun);
        } else {
            $query->whereMonth('waktu_presensi', Carbon::now()->month)
                ->whereYear('waktu_presensi', Carbon::now()->year);
        }

        $presensis = $query->get();

        $pdf = Pdf::loadView('admin.rekap-presensi.cetak-pdf', compact('user', 'presensis', 'request'));

        return $pdf->download('Rekap-Presensi-'.$user->name.'.pdf');
    }

    public function printView(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $query = $user->presensis()->orderBy('waktu_presensi', 'asc');

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('waktu_presensi', $request->bulan)
                ->whereYear('waktu_presensi', $request->tahun);
        } else {
            $query->whereMonth('waktu_presensi', Carbon::now()->month)
                ->whereYear('waktu_presensi', Carbon::now()->year);
        }

        $presensis = $query->get();

        return view('admin.rekap-presensi.cetak-pdf', [
            'user' => $user,
            'presensis' => $presensis,
            'bulan' => $request->bulan,
            'tahun' => $request->tahun
        ]);
    }

    private function getLiburNasional($bulan, $tahun)
    {
        $response = Http::get("https://api-harilibur.vercel.app/api?year=$tahun");
        $liburNasional = $response->json();

        Log::info('Libur Nasional:', ['data' => $liburNasional]);

        return collect($liburNasional)->filter(function ($holiday) use ($bulan, $tahun) {
            $holidayDate = Carbon::parse($holiday['holiday_date']);
            $formattedDate = $holidayDate->format('Y-m-d'); // FORMAT FIX

            Log::info('Libur Nasional yang Disaring:', ['tanggal' => $formattedDate]);

            return $holiday['is_national_holiday'] &&
                $holidayDate->month == $bulan &&
                $holidayDate->year == $tahun &&
                $holidayDate->isWeekday();
        })->map(function ($holiday) {
            return Carbon::parse($holiday['holiday_date'])->format('Y-m-d'); // FORMAT FIX
        })->toArray();
    }

    private function countHariKerja($bulan, $tahun, $liburNasional)
    {
        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = Carbon::createFromDate($tahun, $bulan, $startDate->daysInMonth);
        $hariKerja = [];

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if ($date->isWeekday()) {
                $formattedDate = $date->format('Y-m-d'); // FORMAT FIX
                if (!in_array($formattedDate, $liburNasional)) {
                    $hariKerja[] = ['tanggal' => $formattedDate];
                } else {
                    Log::info("Libur Nasional terdeteksi sebagai hari kerja: $formattedDate");
                }
            }
        }

        Log::info("Jumlah Hari Kerja setelah Penyaringan: " . count($hariKerja));

        return [count($hariKerja), $hariKerja];
    }
}
