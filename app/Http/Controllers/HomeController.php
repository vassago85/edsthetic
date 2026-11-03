<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Settings\BusinessSettings;
use App\Settings\ContentSettings;
use App\Support\SiteProfile;
use Illuminate\Contracts\View\View;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $business = app(BusinessSettings::class);
        $ogImage = $this->openGraphImage($business);
        $showGallery = Project::query()->published()->exists();

        return view('home', [
            'business' => $business,
            'content' => app(ContentSettings::class),
            'ogImage' => $ogImage,
            'showGallery' => $showGallery,
            'jsonLd' => json_encode(
                SiteProfile::graph($business, app(ContentSettings::class), $ogImage),
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_THROW_ON_ERROR,
            ),
        ]);
    }

    private function openGraphImage(BusinessSettings $business): ?string
    {
        if (blank($business->og_image)) {
            return null;
        }

        $media = Media::query()->find($business->og_image);

        if ($media === null) {
            return null;
        }

        return $media->hasGeneratedConversion('large')
            ? $media->getFullUrl('large')
            : $media->getFullUrl();
    }
}
