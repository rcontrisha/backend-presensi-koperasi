<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class StatistikPresensiController extends Controller
{
    public function index(Request $request)
    {        
        $user = Auth::user();

        $bulan = Carbon::now()->month;
        $tahun = Carbon::now()->year;
        $hariIni = Carbon::now()->toDateString();

        // Jumlah presensi hadir
        $jumlahPresensi = $user->presensis()
            ->whereMonth('waktu_presensi', $bulan)
            ->whereYear('waktu_presensi', $tahun)
            ->whereDate('waktu_presensi', '<=', $hariIni)
            ->count();

        // Jumlah izin diterima
        $jumlahIzin = $user->izins()
            ->where('status', 'Diterima')
            ->whereMonth('tanggal_mulai', $bulan)
            ->whereYear('tanggal_mulai', $tahun)
            ->whereDate('tanggal_mulai', '<=', $hariIni)
            ->count();

        // Ambil data hari kerja
        $liburNasional = $this->getLiburNasional($bulan, $tahun);
        [$totalHariKerja, $listHariKerja] = $this->countHariKerja($bulan, $tahun, $liburNasional);

        $totalHariKerjaSampaiHariIni = collect($listHariKerja)
        ->filter(fn($tanggal) => Carbon::parse($tanggal['tanggal'])->lte(Carbon::parse($hariIni)))
        ->count();
    
        Log::info("Total Hari Kerja Sampai Hari Ini: " . $totalHariKerjaSampaiHariIni);
    
        // Menghitung total hadir (presensi + izin)
        $totalHadir = $jumlahPresensi + $jumlahIzin;

        // Menghitung jumlah absen
        $jumlahAbsen = $totalHariKerjaSampaiHariIni - $totalHadir;

        // Menghitung persentase kehadiran
        $presentase = $totalHariKerjaSampaiHariIni > 0 
            ? round(($totalHadir / $totalHariKerjaSampaiHariIni) * 100, 2) 
            : 0;

        return response()->json([
            'nama' => $user->name,
            'jumlah_presensi' => $jumlahPresensi,
            'jumlah_izin' => $jumlahIzin,
            'jumlah_absen' => $jumlahAbsen,
            'hari_kerja' => $listHariKerja,
            'libur_bulan_ini' => $liburNasional,
            'total_hari_kerja' => $totalHariKerjaSampaiHariIni,
            'presentase' => $presentase
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

