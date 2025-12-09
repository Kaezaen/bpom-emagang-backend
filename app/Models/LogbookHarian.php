<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogbookHarian extends Model
{
    protected $table = 'logbook_harian';

    protected $fillable = [
        'mahasiswa_id',
        'tanggal',
        'judul',
        'deskripsi',
        'file_path',
        'status',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaData::class, 'mahasiswa_id');
    }
}
