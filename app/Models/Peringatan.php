<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peringatan extends Model
{
    protected $table = 'peringatan';

    protected $fillable = [
        'pegawai_id',
        'jenis_sp',
        'tanggal_kirim',
        'file_surat_peringatan'
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
    ];

    // Relasi ke Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
