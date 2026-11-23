<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['published_at' => 'datetime', 'is_published' => 'boolean'];
    }

    /** Photos supplémentaires de l'actualité, dans l'ordre choisi dans l'admin. */
    public function images()
    {
        return $this->hasMany(PostImage::class)->orderBy('position')->orderBy('id');
    }

    /** Image principale, ou à défaut la première photo de la galerie. */
    public function getCoverUrlAttribute(): string
    {
        return media_url($this->image ?: optional($this->images->first())->path);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $q): Builder
    {
        return $q->where('is_published', true)->where(fn ($w) => $w->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->orderByDesc('published_at');
    }

    public function getReadingTimeAttribute(): int
    {
        return max(1, (int) ceil(preg_match_all('/\S+/u', \App\Support\RichText::plain($this->body)) / 200));
    }
}
