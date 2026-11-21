<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectImage extends Model
{
    protected $guarded = ['id'];
    protected $appends = ['url'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getUrlAttribute(): string
    {
        return media_url($this->path);
    }
}
