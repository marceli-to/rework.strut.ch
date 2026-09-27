<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SeoSetting extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid',
        'landing_meta_description',
        'projects_meta_description',
        'werkliste_meta_description',
        'profile_meta_description',
        'team_meta_description',
        'jobs_meta_description',
        'contact_meta_description',
    ];

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    protected function ogImage(): Attribute
    {
        return Attribute::get(fn () => $this->media->firstWhere('is_og', true)?->file
            ?? $this->media->first(fn ($m) => $m->isImage())?->file);
    }
}
