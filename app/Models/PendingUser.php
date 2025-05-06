<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $table = 'pending_users';
}
