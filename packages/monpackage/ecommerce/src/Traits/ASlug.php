<?php

namespace MonPackage\Ecommerce\Traits;

use Illuminate\Support\Str;

trait ASlug
{
    protected static function bootASlug(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::genererSlugUnique($model->nom);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('nom') && ! $model->isDirty('slug')) {
                $model->slug = static::genererSlugUnique($model->nom, $model->id);
            }
        });
    }

    public static function genererSlugUnique(string $nom, ?int $excludeId = null): string
    {
        $slug  = Str::slug($nom);
        $base  = $slug;
        $count = 1;

        while (true) {
            $query = static::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            if (! $query->exists()) break;
            $slug = $base . '-' . $count++;
        }

        return $slug;
    }
}
