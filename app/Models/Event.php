<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai'
    ];

    protected $appends = ['status'];

    public function getStatusAttribute()
    {
        $today = Carbon::today();
        $end = Carbon::parse($this->tanggal_selesai ?? $this->tanggal_mulai);

        if ($today->lessThanOrEqualTo($end)) {
            return 'ongoing';
        }

        // sudah lewat → expired
        return 'expired';
    }
}
