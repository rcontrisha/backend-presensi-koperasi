<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi'; // nama tabel di database

    protected $fillable = [
        'user_id',
        'foto_masuk',
        'foto_pulang',
        'latitude',
        'longitude',
        'waktu_presensi',
        'waktu_pulang'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
