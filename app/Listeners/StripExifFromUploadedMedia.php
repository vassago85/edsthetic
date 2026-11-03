<?php

namespace App\Listeners;

use Spatie\Image\Image;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class StripExifFromUploadedMedia
{
    public function handle(MediaHasBeenAddedEvent $event): void
    {
        $media = $event->media;
        $mime = (string) $media->mime_type;

        if (! str_starts_with($mime, 'image/') || $mime === 'image/svg+xml') {
            return;
        }

        $path = $media->getPath();

        if (! is_file($path)) {
            return;
        }

        Image::load($path)->orientation()->save();

        clearstatcache(true, $path);

        $media->size = filesize($path) ?: $media->size;
        $media->save();
    }
}
