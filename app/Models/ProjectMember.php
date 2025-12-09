<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    protected $table = 'project_members';

    protected $fillable = [
        'project_id',
        'mahasiswa_id',
        'is_leader',
        'status'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function mahasiswa()
    {
        return $this->belongsTo(MahasiswaData::class, 'mahasiswa_id');
    }
}
