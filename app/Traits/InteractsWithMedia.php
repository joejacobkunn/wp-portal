<?php

namespace App\Traits;

use App\Models\Core\Media as CoreMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\InteractsWithMedia as SpatieInteractsWithMedia;


trait InteractsWithMedia
{
    use SpatieInteractsWithMedia;

    public $defaultCollection = 'default';

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion(CoreMedia::PREVIEW_CONVERSION)
              ->width(368)
              ->height(232)
              ->sharpen(10);
    }
}
