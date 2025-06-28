<?php

use App\Http\Controllers\RekapIzinController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CustomRegisterController;
use App\Http\Controllers\AktivasiUserController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\RekapPresensiController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PeringatanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth'])->name('admin.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'isAdmin'])->prefix('admin')->group(function () {
    // Tampilkan halaman daftar pending users
    Route::get('/admin/pending-users', [AktivasiUserController::class, 'showPendingUsers'])->name('admin.pending-users');

    // Terima user (Accept)
    Route::post('/admin/accept-user/{id}', [AktivasiUserController::class, 'acceptUser'])->name('admin.accept-user');
 
    // Tolak user (Decline)
    Route::post('/admin/decline-user/{id}', [AktivasiUserController::class, 'declineUser'])->name('admin.decline-user');
 
    Route::get('/rekap-presensi', [RekapPresensiController::class, 'index'])->name('admin.rekap-presensi');
    Route::get('/rekap-presensi/{user}', [RekapPresensiController::class, 'detail'])->name('admin.rekap-presensi.detail');
    Route::get('/admin/rekap-presensi/{id}/cetak-pdf', [RekapPresensiController::class, 'cetakPdf'])->name('admin.rekap-presensi.cetak-pdf');
    Route::get('/admin/rekap-presensi/print/{id}', [RekapPresensiController::class, 'printView'])->name('admin.rekap-presensi.print-view');

    Route::get('/rekap-izin', [RekapIzinController::class, 'index'])->name('admin.rekap-izin');
    Route::get('/rekap-izin/{user}', [RekapIzinController::class, 'detail'])->name('admin.rekap-izin.detail');
    Route::get('/admin/rekap-izin/{id}/cetak-pdf', [RekapIzinController::class, 'cetakPdf'])->name('admin.rekap-izin.cetak-pdf');

    Route::get('admin/perizinan/approval', [RekapIzinController::class, 'approvalIndex'])->name('admin.perizinan.approval');
    Route::post('admin/perizinan/approve/{id}', [RekapIzinController::class, 'approve'])->name('admin.perizinan.approve');
    Route::post('admin/perizinan/reject/{id}', [RekapIzinController::class, 'reject'])->name('admin.perizinan.reject');

    Route::get('/pegawai', [PegawaiController::class, 'index'])->name('admin.pegawai.index');
    Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('admin.pegawai.create');
    Route::get('/pegawai/{id}/edit', [PegawaiController::class, 'edit'])->name('admin.pegawai.edit');
    Route::put('/pegawai/{id}', [PegawaiController::class, 'update'])->name('admin.pegawai.update');
    Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('admin.pegawai.destroy');
    Route::get('/akun-pegawai', [AkunController::class, 'index'])->name('admin.akun-pegawai.index');
    Route::post('/admin/akun-pegawai', [AkunController::class, 'store'])->name('admin.akun-pegawai.store');
    Route::put('/admin/akun-pegawai/{id}/toggle-status', [AkunController::class, 'toggleStatus'])->name('admin.akun-pegawai.toggleStatus');

    Route::get('/peringatan', [PeringatanController::class, 'index'])->name('admin.peringatan.index');
    Route::post('/admin/peringatan/kirim/{id}', [PeringatanController::class, 'kirimSurat'])->name('admin.peringatan.kirim');
});


require __DIR__.'/auth.php';
