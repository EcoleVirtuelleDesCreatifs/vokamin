<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientReference extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['logo_authorized' => 'boolean', 'is_published' => 'boolean'];
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
