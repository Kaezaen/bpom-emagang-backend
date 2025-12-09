<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectSubmission extends Model
{
    protected $fillable = [
        'project_id',
        'mahasiswa_id',
        'file_path',
        'catatan',
        'status'
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

