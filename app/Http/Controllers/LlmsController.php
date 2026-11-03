<?php

namespace App\Http\Controllers;

use App\Settings\BusinessSettings;
use App\Settings\ContentSettings;
use App\Support\SiteProfile;
use Illuminate\Http\Response;

class LlmsController extends Controller
{
    public function __invoke(): Response
    {
        $body = SiteProfile::llmsText(
            app(BusinessSettings::class),
            app(ContentSettings::class),
        );

        return response($body, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'X-Robots-Tag' => 'index, follow',
        ]);
    }
}
