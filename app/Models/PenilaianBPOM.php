<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianBPOM extends Model
{
    protected $table = 'penilaian_bpom';

    protected $fillable = [
        'mahasiswa_id',
        'kehadiran','taat_jadwal','pemahaman_materi','praktek_kerja',
        'komunikasi','laporan','presentasi',
        'nilai_akhir','locked'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaData::class);
    }
}
