<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory;

    protected $table = 'izin'; // Nama tabel di database

    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis_izin',
        'keterangan',
        'file_pendukung',
        'status',
    ];

    public $timestamps = true; // Ini default-nya true, tapi ditulis biar lebih jelas

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
