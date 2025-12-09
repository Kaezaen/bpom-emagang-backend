<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanAkhir extends Model
{
    protected $table = 'laporan_akhir';

    protected $fillable = [
        'mahasiswa_id',
        'file_laporan',
        'status',
        'catatan',
    ];


    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaData::class, 'mahasiswa_id');
    }
}
