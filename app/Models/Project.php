<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    protected $fillable = [
        'pembimbing_id',
        'title',
        'description',
        'deadline',
        'status',
    ];

    public function pembimbing()
    {
        return $this->belongsTo(PembimbingData::class, 'pembimbing_id');
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class, 'project_id');
    }

    public function progress()
    {
        return $this->hasMany(ProjectProgress::class, 'project_id');
    }
}
