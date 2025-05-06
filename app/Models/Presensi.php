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
        'foto',
        'latitude',
        'longitude',
        'waktu_presensi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
