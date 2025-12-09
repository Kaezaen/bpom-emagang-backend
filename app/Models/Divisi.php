<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $table = 'divisi';

    protected $fillable = [
        'nama_divisi',
        'deskripsi',
        'kuota'
    ];

    public function pembimbing()
    {
        return $this->hasMany(PembimbingData::class, 'divisi_id');
    }
}
