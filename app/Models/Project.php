<?php

namespace App\Models;

use App\Enums\ProjectCategory;
use App\Enums\TileSize;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Fillable([
    'title',
    'slug',
    'category',
    'description',
    'tile_size',
    'sort_order',
    'is_published',
])]
class Project extends Model implements HasMedia
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    use InteractsWithMedia;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => ProjectCategory::class,
            'tile_size' => TileSize::class,
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Project $project): void {
            $base = Str::slug($project->slug ?: $project->title) ?: 'project';
            $slug = $base;
            $suffix = 2;

            while (
                static::query()
                    ->where('slug', $slug)
                    ->when($project->exists, fn (Builder $query) => $query->whereKeyNot($project->getKey()))
                    ->exists()
            ) {
                $slug = $base.'-'.$suffix;
                $suffix++;
            }

            $project->slug = $slug;
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos')
            ->acceptsMimeTypes([
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif',
            ]);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(600)
            ->format('webp')
            ->performOnCollections('photos')
            ->withResponsiveImages()
            ->nonQueued();

        $this->addMediaConversion('large')
            ->width(1600)
            ->format('webp')
            ->performOnCollections('photos')
            ->withResponsiveImages()
            ->nonQueued();
    }

    /**
     * @param  Builder<Project>  $query
     * @return Builder<Project>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
