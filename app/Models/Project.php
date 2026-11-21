<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $guarded = ['id', 'views'];

    protected function casts(): array
    {
        return ['service_slugs' => 'array', 'is_featured' => 'boolean', 'is_published' => 'boolean'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('position');
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)->orderBy('position')->orderByDesc('id');
    }

    public function services()
    {
        return Service::published()->whereIn('slug', $this->service_slugs ?? [])->get();
    }

    public function getCoverUrlAttribute(): string
    {
        return media_url($this->cover ?: optional($this->images->first())->path);
    }

    public function getTagListAttribute(): array
    {
        return array_values(array_filter(explode(' ', (string) $this->tags)));
    }
}
