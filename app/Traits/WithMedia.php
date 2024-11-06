<?php

namespace App\Traits;

use Spatie\MediaLibraryPro\Http\Livewire\Concerns\WithMedia as SpatieWithMedia;

trait WithMedia
{
    use SpatieWithMedia;

    public function onMediaChanged($name, $media): void
    {
        $media = $this->makeSureCustomPropertiesUseRightCasing($media);
        $this->$name = $media;

        $this->dispatch($name .':media-updated',  [
            'collection' => $this->collection,
            'name' => $name,
            'media' => $media,
            'model' => $this->model,
        ]);
    }

    public function bootWithMedia(): void
    {
        $this->fileVar = uniqid();
        $this->mediaComponentNames = [$this->fileVar];
    }
}
