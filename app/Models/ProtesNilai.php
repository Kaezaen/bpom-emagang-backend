<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProtesNilai extends Model
{
    protected $table = 'protes_nilai';

    protected $fillable = [
        'mahasiswa_id',
        'jenis',
        'referensi_id',
        'alasan',
        'status',
        'tanggapan',
        'resolved_by'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaData::class);
    }
}
