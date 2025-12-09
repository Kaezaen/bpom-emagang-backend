<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianKampus extends Model
{
    protected $table = 'penilaian_kampus';

    protected $fillable = [
        'mahasiswa_id',
        'nilai_akhir',
        'locked'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaData::class);
    }
}
