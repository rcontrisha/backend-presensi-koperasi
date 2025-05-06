<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class RekapIzinController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua user
        $users = User::where('role', 'pegawai')->get();

        // Ambil semua izin, bisa difilter bulan/tahun
        $query = Izin::with('user')
                    ->where('status', '!=', 'menunggu')
                    ->orderBy('tanggal_mulai', 'asc');

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal_mulai', $request->bulan)
                  ->whereYear('tanggal_mulai', $request->tahun);
        } else {
            // Default: bulan ini
            $query->whereMonth('tanggal_mulai', Carbon::now()->month)
                  ->whereYear('tanggal_mulai', Carbon::now()->year);
        }

        $izinList = $query->get();

        return view('admin.perizinan.index', compact('users', 'izinList'));
    }

    public function detail($id, Request $request)
    {
        // Detail izin per user
        $user = User::findOrFail($id);

        $query = $user->izins()->where('status', '!=', 'menunggu')->orderBy('tanggal_mulai', 'asc');

        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal_mulai', $request->bulan)
                  ->whereYear('tanggal_mulai', $request->tahun);
        } else {
            $query->whereMonth('tanggal_mulai', Carbon::now()->month)
                  ->whereYear('tanggal_mulai', Carbon::now()->year);
        }

        $izins = $query->get();

        // Mengecek apakah request adalah AJAX
        if ($request->ajax()) {
            // Kembalikan hanya bagian tabel presensi
            return view('admin.perizinan.table', compact('izins'));
        }

        return view('admin.perizinan.detail', compact('user', 'izins'));
    }

    public function cetakPdf(Request $request, $id)
    {
        // Ambil data user
        $user = User::findOrFail($id);

        // Query untuk mendapatkan data izin yang terkait dengan user
        $query = $user->izins()->orderBy('tanggal_mulai', 'asc');

        // Filter berdasarkan bulan dan tahun jika ada
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal_mulai', $request->bulan)
                ->whereYear('tanggal_mulai', $request->tahun);
        } else {
            $query->whereMonth('tanggal_mulai', Carbon::now()->month)
                ->whereYear('tanggal_mulai', Carbon::now()->year);
        }

        $izins = $query->get();

        // Load view dan generate PDF
        $pdf = Pdf::loadView('admin.perizinan.cetak-pdf', compact('user', 'izins', 'request'));

        // Download file PDF
        return $pdf->download('Rekap-Izin-'.$user->name.'.pdf');
    }

    // Fungsi untuk menampilkan daftar pengajuan izin yang menunggu approval
    public function approvalIndex(Request $request)
    {
        // Ambil semua izin yang statusnya 'Menunggu Approval'
        $izinList = Izin::with('user')
                        ->where('status', 'menunggu')
                        ->orderBy('tanggal_mulai', 'asc');

        // Filter berdasarkan bulan dan tahun jika ada
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $izinList->whereMonth('tanggal_mulai', $request->bulan)
                     ->whereYear('tanggal_mulai', $request->tahun);
        } else {
            // Default: bulan ini
            $izinList->whereMonth('tanggal_mulai', Carbon::now()->month)
                     ->whereYear('tanggal_mulai', Carbon::now()->year);
        }

        $izinList = $izinList->get();

        return view('admin.perizinan.approval', compact('izinList'));
    }

    // Fungsi untuk meng-approve izin
    public function approve($id)
    {
        $izin = Izin::findOrFail($id);
        
        // Ubah status izin menjadi 'Disetujui'
        $izin->status = 'diterima';
        $izin->save();

        return redirect()->route('admin.perizinan.approval')->with('status', 'Izin telah disetujui!');
    }

    // Fungsi untuk menolak izin
    public function reject($id)
    {
        $izin = Izin::findOrFail($id);
        
        // Ubah status izin menjadi 'Ditolak'
        $izin->status = 'ditolak';
        $izin->save();

        return redirect()->route('admin.perizinan.approval')->with('status', 'Izin telah ditolak!');
    }
}
