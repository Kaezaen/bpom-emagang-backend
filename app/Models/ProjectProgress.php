<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectProgress extends Model
{
    protected $table = 'project_progress';

    protected $fillable = [
        'project_id',
        'mahasiswa_id',
        'judul',
        'deskripsi',
        'file_path',
    ];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaData::class, 'mahasiswa_id');
    }
}
