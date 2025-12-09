<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MahasiswaData extends Model
{
    protected $table = 'mahasiswa_data';

    protected $fillable = [
        'user_id',
        'pembimbing_id',
        'formulir_id',
        'status',
        'phone',
        'catatan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pembimbing()
    {
        return $this->belongsTo(PembimbingData::class, 'pembimbing_id');
    }

    public function formulir()
    {
        return $this->belongsTo(FormulirMagang::class, 'formulir_id');
    }
    
    public function laporanAkhir()
    {
        return $this->hasOne(LaporanAkhir::class, 'mahasiswa_id');
    }
    public function penilaianBpom() 
    { 
        return $this->hasOne(PenilaianBpom::class,'mahasiswa_id'); 
    }
    public function penilaianKampus() 
    { 
        return $this->hasOne(PenilaianKampus::class,'mahasiswa_id'); 
    }
    public function protes() 
    { 
        return $this->hasMany(ProtesNilai::class,'mahasiswa_id'); 
    }
}

