<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasPublish
{
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('publish', true);
    }
}
