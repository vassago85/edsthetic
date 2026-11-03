<?php

namespace App\Providers;

use App\Listeners\StripExifFromUploadedMedia;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Spatie\LaravelSettings\Events\SettingsSaved;
use Spatie\LaravelSettings\Support\SettingsCacheFactory;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAddedEvent;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(MediaHasBeenAddedEvent::class, StripExifFromUploadedMedia::class);

        Event::listen(function (SettingsSaved $event): void {
            app()->terminating(function (): void {
                foreach (app(SettingsCacheFactory::class)->all() as $cache) {
                    if ($cache->isEnabled()) {
                        $cache->clear();
                    }
                }
            });
        });
    }
}
