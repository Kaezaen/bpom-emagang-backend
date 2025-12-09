<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* -------------------------
       RELASI UTAMA
    ------------------------- */

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Data tambahan pembimbing (jika role = pembimbing)
    public function pembimbingData()
    {
        return $this->hasOne(PembimbingData::class, 'user_id');
    }

    // Data tambahan mahasiswa (jika role = mahasiswa)
    public function mahasiswaData()
    {
        return $this->hasOne(MahasiswaData::class, 'user_id');
    }
}
