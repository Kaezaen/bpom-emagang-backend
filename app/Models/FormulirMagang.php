<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulirMagang extends Model
{
    protected $table = 'formulir_magang';

    protected $fillable = [
        'nama',
        'nik',
        'nomor_hp',

        'universitas',
        'alamat_universitas',
        'jurusan',
        'semester',

        'divisi_id',
        'waktu_mulai',
        'waktu_selesai',

        'proposal',
        'surat_permohonan',

        'status',
        'alasan_tolak',
    ];

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    // relasi ke mahasiswa setelah diterima
    public function mahasiswa()
    {
        return $this->hasOne(MahasiswaData::class, 'formulir_id');
    }
}
