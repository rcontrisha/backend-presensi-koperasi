<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawai'; // Nama tabel
    protected $primaryKey = 'id'; // Primary key

    protected $fillable = [
        'user_id',
        'alamat',
        'no_telpon',
        'jabatan',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'pendidikan_terakhir',
        'foto_profil'
    ];

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function peringatan()
    {
        return $this->hasMany(Peringatan::class);
    }
}
