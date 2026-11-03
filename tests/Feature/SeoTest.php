<?php

namespace Tests\Feature;

use App\Enums\ProjectCategory;
use App\Models\Project;
use App\Settings\BusinessSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_homepage_uses_settings_for_seo_and_contact_details(): void
    {
        $settings = app(BusinessSettings::class);
        $settings->phone = '082 555 0101';
        $settings->whatsapp_number = '0825550101';
        $settings->meta_title = 'Esthetic Renovations | Cabinet making and carpentry';
        $settings->save();

        $this->get('/')
            ->assertOk()
            ->assertSee('<title>Esthetic Renovations | Cabinet making and carpentry</title>', false)
            ->assertSee('Kitchens, cupboards and carpentry, built in our own workshop.', false)
            ->assertSee('Quote call-out', false)
            ->assertSee('href="tel:0825550101"', false)
            ->assertSee('href="https://wa.me/27825550101?text=Hello%2C%20I%20would%20like%20a%20quote."', false)
            ->assertSee('WhatsApp us', false)
            ->assertDontSee('Stand-in. Replace by selecting a hero photo', false)
            ->assertDontSee('Draft wording. Have it checked', false)
            ->assertDontSee('Recent jobs will appear here once photos are added', false)
            ->assertDontSee('id="gallery"', false)
            ->assertSee('lang="en-ZA"', false)
            ->assertSee('name="robots" content="index, follow, max-image-preview:large', false)
            ->assertSee('rel="alternate" type="text/plain" href="'.url('/llms.txt').'"', false)
            ->assertSee('property="og:locale" content="en_ZA"', false)
            ->assertSee('HomeAndConstructionBusiness', false)
            ->assertSee('hasOfferCatalog', false)
            ->assertSee('"@type":"HowTo"', false)
            ->assertSee('[address]', false)
            ->assertSee('A deposit of <mark>[X]%</mark>', false);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('content-type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.url('/').'</loc>', false);

        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('User-agent: *')
            ->assertSee('Allow: /')
            ->assertSee('Disallow: /admin')
            ->assertSee('User-agent: GPTBot')
            ->assertSee('User-agent: ClaudeBot')
            ->assertSee('User-agent: PerplexityBot')
            ->assertSee('LLM summary: '.url('/llms.txt'))
            ->assertSee('Sitemap: '.url('/sitemap.xml'));

        $this->get('/llms.txt')
            ->assertOk()
            ->assertHeader('content-type', 'text/plain; charset=UTF-8')
            ->assertSee('# Esthetic Renovations')
            ->assertSee('## Services')
            ->assertSee('**Kitchens**: New kitchens and refits')
            ->assertSee('Do not treat it as a fact')
            ->assertSee(url('/').'#contact');
    }

    public function test_the_open_graph_image_uses_the_selected_gallery_photo(): void
    {
        Storage::fake('public');

        $path = storage_path('framework/testing/og.png');

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));

        $project = Project::factory()->published()->create([
            'title' => 'Oak kitchen',
            'category' => ProjectCategory::Kitchen,
        ]);
        $media = $project->addMedia($path)->toMediaCollection('photos');

        $settings = app(BusinessSettings::class);
        $settings->og_image = (string) $media->getKey();
        $settings->save();

        $this->get('/')
            ->assertOk()
            ->assertSee('property="og:image"', false)
            ->assertSee($media->getFullUrl('large'), false)
            ->assertSee('alt="Oak kitchen, Kitchen"', false)
            ->assertSee('id="gallery"', false)
            ->assertSee('href="#gallery"', false);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee('<image:title>Oak kitchen</image:title>', false)
            ->assertSee($media->getFullUrl('large'), false);

        $this->get('/llms.txt')
            ->assertOk()
            ->assertSee('Oak kitchen (Kitchen)');
    }
}
