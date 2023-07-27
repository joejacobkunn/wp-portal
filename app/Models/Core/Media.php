<?php

namespace App\Models\Core;

use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Media extends SpatieMedia
{
    protected $table = 'media';

    protected $hidden = [
        'deleted_at',
    ];

    protected $guarded = [
        'id'
    ];

    const PREVIEW_CONVERSION = 'preview';

    protected static function boot() {

        static::deleting(function(Media $media) {
            if ($media->hasGeneratedConversion(self::PREVIEW_CONVERSION)) {
                $previewPath = $media->getPath(self::PREVIEW_CONVERSION);
                @unlink($previewPath);
            }
        });

        parent::boot();
    }

    public function getFileSystem()
    {
        return app(\Spatie\MediaLibrary\MediaCollections\Filesystem::class);
    }

    public function getParentDirectory()
    {
        $path = $this->getPath();
        $parentDirectoryFullPath = $this->getFileSystem()->getMediaDirectory($this);
        $parentDirectoryPathParts = explode('/', $parentDirectoryFullPath);

        return substr($path, 0, strpos($path, $parentDirectoryPathParts[0])) . $parentDirectoryPathParts[0];
    }
}