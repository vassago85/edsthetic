<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $location = $this->escape(url('/'));
        $projects = Project::query()->published()->with('media')->orderBy('sort_order')->orderBy('id')->get();
        $updatedAt = $projects->max('updated_at');
        $lastmod = $updatedAt instanceof Carbon
            ? "\n        <lastmod>".$updatedAt->toAtomString().'</lastmod>'
            : '';

        $images = '';

        foreach ($projects as $project) {
            foreach ($project->getMedia('photos') as $media) {
                $url = $media->hasGeneratedConversion('large')
                    ? $media->getFullUrl('large')
                    : $media->getFullUrl();
                $title = $this->escape($project->title);

                $images .= <<<XML
        <image:image>
            <image:loc>{$this->escape($url)}</image:loc>
            <image:title>{$title}</image:title>
        </image:image>
XML;
            }
        }

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <url>
        <loc>{$location}</loc>{$lastmod}
{$images}    </url>
</urlset>
XML;

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1);
    }
}
