<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(fn ($model) => $model->uuid ??= Str::uuid());
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
