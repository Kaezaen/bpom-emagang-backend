<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembimbingData extends Model
{
    protected $table = 'pembimbing_data';

    protected $fillable = [
        'user_id',
        'divisi_id',
        'jabatan',
        'phone',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    public function mahasiswa()
    {
        return $this->hasMany(MahasiswaData::class, 'pembimbing_id');
    }
}
