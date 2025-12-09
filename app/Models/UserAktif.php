<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAktif extends Model
{
    protected $table = 'user_aktif';

    protected $fillable = [
        'user_id',
        'pembimbing_id',
        'status',
        'catatan',
    ];

    /** Mahasiswa */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Pembimbing */
    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }
}