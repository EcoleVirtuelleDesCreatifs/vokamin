<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'prestations' => 'array', 'beneficiaires' => 'array', 'methode' => 'array', 'avantages' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)->orderBy('position');
    }

    public function projects()
    {
        return Project::published()->whereJsonContains('service_slugs', $this->slug);
    }
}
