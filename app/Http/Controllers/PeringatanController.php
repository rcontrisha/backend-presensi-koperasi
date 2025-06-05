<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Absensi;
use App\Models\Presensi;
use App\Models\Peringatan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\SuratPeringatanMail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use App\Services\FCMServices;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PeringatanController extends Controller
{
    public function index()
    {
        $pegawaiList = Pegawai::with(['user', 'peringatan'])->get();

        foreach ($pegawaiList as $pegawai) {
            $pegawai->memenuhiSyarat = $this->pegawaiMemenuhiSyaratSuratPeringatan($pegawai);
        }

        return view('admin.peringatan.index', compact('pegawaiList'));
    }

    public function getSuratPeringatan()
    {
        $user = Auth::user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (! $pegawai) {
            return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
        }

        // Ambil semua surat peringatan pegawai
        $peringatan = Peringatan::where('pegawai_id', $pegawai->id)
            ->orderBy('tanggal_kirim', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'judul_surat' => 'SURAT PERINGATAN ' . $item->jenis_sp,
                    'tanggal_kirim' => $item->tanggal_kirim->format('Y-m-d'),
                    'file_path' => $item->file_surat_peringatan,
                ];
            });

        return response()->json($peringatan);
    }

    public function kirimSurat($id)
    {
        $pegawai = Pegawai::with('user')->findOrFail($id);

        if (! $this->pegawaiMemenuhiSyaratSuratPeringatan($pegawai)) {
            return back()->with('error', 'Pegawai belum memenuhi syarat (Alpha 10 hari kerja berturut-turut).');
        }

        $lastWarning = Peringatan::where('pegawai_id', $pegawai->id)
            ->orderBy('tanggal_kirim', 'desc')
            ->first();

        $nextSP = match ($lastWarning?->jenis_sp) {
            null => 'SP1',
            'SP1' => 'SP2',
            default => null,
        };

        if (! $nextSP) {
            return back()->with('error', 'Pegawai telah menerima SP3. Tidak dapat mengirim lagi.');
        }

        $isiSurat = request()->input('isi_surat');

        $data = [
            'judul_surat' => "SURAT PERINGATAN {$nextSP}",
            'nomor_surat' => "{$nextSP}/" . strtoupper(Str::random(5)) . '/' . now()->format('m/Y'),
            'nama' => $pegawai->user->name ?? '-',
            'jabatan' => $pegawai->jabatan,
            'isi_surat' => $isiSurat,
            'tanggal' => now()->translatedFormat('d F Y'),
        ];

        $pdf = Pdf::loadView('admin.peringatan.peringatan-pdf', $data);

        // Simpan PDF ke storage
        $namaFile = "Surat_Peringatan_{$nextSP}_" . Str::slug($pegawai->user->name ?? 'pegawai') . ".pdf";
        $path = "surat_peringatan/{$namaFile}";
        Storage::disk('public')->put($path, $pdf->output());

        // Kirim email dengan lampiran PDF
        Mail::to($pegawai->user->email)->send(new SuratPeringatanMail($pdf, $data));

        // Simpan ke tabel peringatan
        Peringatan::create([
            'pegawai_id' => $pegawai->id,
            'jenis_sp' => $nextSP,
            'tanggal_kirim' => now()->toDateString(),
            'file_surat_peringatan' => $path,
        ]);

        // Kirim Push Notification via FCMService
        $fcmToken = $pegawai->user->fcm_token;

        if ($fcmToken) {
            $fcm = new FCMServices();
            $fcm->sendToDevice(
                $fcmToken,
                "Surat Peringatan {$nextSP}",
                "Anda telah menerima Surat Peringatan {$nextSP}. Silakan cek email Anda."
            );
        }

        return back()->with('success', "Surat {$nextSP} berhasil dikirim dan disimpan.");
    }

    private function getTemplateIsi($jenis_sp, $pegawai)
    {
        return match ($jenis_sp) {
            'SP1' => "Dengan ini kami memberikan Surat Peringatan 1 kepada Saudara {$pegawai->user->name} karena tidak masuk kerja selama 10 hari berturut-turut tanpa keterangan.",
            'SP2' => "Dengan ini kami memberikan Surat Peringatan 2 kepada Saudara {$pegawai->user->name} sebagai peringatan terakhir atas pelanggaran berulang berupa ketidakhadiran kerja selama 10 hari berturut-turut.",
        };
    }

    private function getLiburNasional($bulan, $tahun)
    {
        // Ganti URL_API_LIBUR_NASIONAL dengan URL yang kamu gunakan
        $response = Http::get("https://api-harilibur.vercel.app/api?year=$tahun");
        $liburNasional = $response->json();

        return collect($liburNasional)->filter(function ($holiday) use ($bulan, $tahun) {
            $holidayDate = Carbon::parse($holiday['holiday_date']);
            return $holiday['is_national_holiday'] && $holidayDate->month == $bulan && $holidayDate->year == $tahun;
        })->pluck('holiday_date')->toArray();
    }

    private function countHariKerja($bulan, $tahun, $liburNasional)
    {
        $startDate = Carbon::create($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $hariKerja = 0;
        $listHariKerja = [];

        while ($startDate <= $endDate) {
            if ($startDate->isWeekday() && !in_array($startDate->toDateString(), $liburNasional)) {
                $hariKerja++;
                $listHariKerja[] = [
                    'tanggal' => $startDate->toDateString(),
                    'hari' => $startDate->locale('id')->isoFormat('dddd'), // hari dalam bahasa Indonesia
                ];
            }
            $startDate->addDay();
        }

        return [$hariKerja, $listHariKerja];
    }

    private function pegawaiMemenuhiSyaratSuratPeringatan(Pegawai $pegawai): bool
    {
        $bulanIni = now()->format('m');
        $tahunIni = now()->format('Y');
        $hariIni = now()->toDateString();

        // 1. Ambil tanggal terakhir SP milik pegawai
        $tanggalSpTerakhir = Peringatan::where('pegawai_id', $pegawai->id)
            ->orderByDesc('tanggal_kirim')
            ->value('tanggal_kirim');

        // 2. Ambil hari kerja sampai hari ini
        $liburNasional = $this->getLiburNasional($bulanIni, $tahunIni);
        [$_, $listHariKerja] = $this->countHariKerja($bulanIni, $tahunIni, $liburNasional);

        // 3. Filter hanya hari kerja setelah tanggal SP terakhir (jika ada)
        $hariKerjaSetelahSp = collect($listHariKerja)
            ->filter(fn($tgl) => $tgl['tanggal'] > ($tanggalSpTerakhir ?? '1900-01-01') && $tgl['tanggal'] <= $hariIni)
            ->pluck('tanggal')
            ->values();

        if ($hariKerjaSetelahSp->count() < 10) {
            // Belum cukup 10 hari kerja sejak SP terakhir, tidak layak
            return false;
        }

        // 4. Ambil presensi pegawai di tanggal tersebut
        $presensi = Presensi::where('user_id', $pegawai->user_id)
            ->whereBetween('waktu_presensi', [$hariKerjaSetelahSp->first(), $hariKerjaSetelahSp->last()])
            ->pluck('waktu_presensi')
            ->map(fn($tgl) => \Carbon\Carbon::parse($tgl)->toDateString());

        // 5. Cek 10 hari kerja pertama setelah SP terakhir
        $sepuluhHariKerja = $hariKerjaSetelahSp->take(10);
        $alphaSemua = $sepuluhHariKerja->every(fn($tgl) => !$presensi->contains($tgl));

        return $alphaSemua;
    }
}
