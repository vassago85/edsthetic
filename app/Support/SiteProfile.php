<?php

namespace App\Support;

use App\Models\Project;
use App\Settings\BusinessSettings;
use App\Settings\ContentSettings;

class SiteProfile
{
    /**
     * @return list<array{name: string, description: string, icon: string, category: ?string}>
     */
    public static function services(): array
    {
        return [
            ['name' => 'Kitchens', 'description' => 'New kitchens and refits, from carcasses and doors to pantries, islands and tops.', 'icon' => 'kitchen', 'category' => 'kitchen'],
            ['name' => 'Built-in cupboards', 'description' => 'Bedroom wardrobes, walk-ins and linen cupboards planned around how you use them.', 'icon' => 'builtin', 'category' => 'builtin'],
            ['name' => 'Cabinet facelifts', 'description' => 'Refresh existing cabinets with new doors, wraps, handles and practical upgrades.', 'icon' => 'custom', 'category' => 'custom'],
            ['name' => 'Bathroom and makeup vanities', 'description' => 'Vanities and storage in moisture-resistant board, sealed properly for wet areas.', 'icon' => 'vanity', 'category' => 'vanity'],
            ['name' => 'Heavy and smart storage', 'description' => 'Heavy-duty storage, add-ons and clever layouts that make the most of the space.', 'icon' => 'shelving', 'category' => null],
            ['name' => 'Office cabinets and freestanding', 'description' => 'Office cabinets, desks and freestanding units built to fit the room and the work.', 'icon' => 'shelving', 'category' => null],
            ['name' => 'Door hanging', 'description' => 'Hanging and adjusting doors so they close cleanly and last.', 'icon' => 'carpentry', 'category' => null],
            ['name' => 'Maintenance and repairs', 'description' => 'Repairs, adjustments and add-ons on cabinets and joinery already in the home.', 'icon' => 'carpentry', 'category' => null],
            ['name' => 'Custom pieces', 'description' => 'One-off furniture built from your sketch, a photo or a drawing we do together.', 'icon' => 'custom', 'category' => 'custom'],
        ];
    }

    /**
     * @return list<array{name: string, text: string}>
     */
    public static function steps(): array
    {
        return [
            ['name' => 'Measure', 'text' => 'We come to you, measure up and talk through what you need and what it has to hold.'],
            ['name' => 'Quote', 'text' => 'You get drawings, a materials list, the price and the payment schedule in writing.'],
            ['name' => 'Build', 'text' => 'Units are cut, assembled and finished in our workshop, not on your floor.'],
            ['name' => 'Fit', 'text' => 'We install, adjust every door and drawer, clean up and go through aftercare with you.'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function graph(BusinessSettings $business, ContentSettings $content, ?string $image): array
    {
        $businessId = url('/').'#business';
        $page = url('/');

        $organization = [
            '@type' => ['HomeAndConstructionBusiness', 'LocalBusiness'],
            '@id' => $businessId,
            'name' => $business->business_name,
            'description' => $business->meta_description,
            'slogan' => $content->hero_heading,
            'url' => $page,
            'telephone' => $business->phone,
            'email' => $business->email,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $business->address,
                'addressCountry' => 'ZA',
            ],
            'openingHours' => $business->hours,
            'areaServed' => [
                '@type' => 'AdministrativeArea',
                'name' => $business->service_area,
            ],
            'knowsAbout' => array_column(self::services(), 'name'),
            'knowsLanguage' => 'en-ZA',
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => 'Cabinet making and carpentry',
                'itemListElement' => array_map(fn (array $service): array => [
                    '@type' => 'Offer',
                    'itemOffered' => [
                        '@type' => 'Service',
                        'name' => $service['name'],
                        'description' => $service['description'],
                        'areaServed' => $business->service_area,
                        'provider' => ['@id' => $businessId],
                    ],
                ], self::services()),
            ],
            'contactPoint' => array_values(array_filter([
                [
                    '@type' => 'ContactPoint',
                    'contactType' => 'customer service',
                    'telephone' => $business->phone,
                    'email' => $business->email,
                    'areaServed' => 'ZA',
                    'availableLanguage' => ['English'],
                ],
            ])),
        ];

        $whatsapp = Phone::whatsappContact($business->whatsapp_number, $business->phone);

        if ($whatsapp !== null) {
            $organization['contactPoint'][] = [
                '@type' => 'ContactPoint',
                'contactType' => 'customer service',
                'url' => $whatsapp,
                'name' => 'WhatsApp',
                'areaServed' => 'ZA',
                'availableLanguage' => ['English'],
            ];
        }

        if ($image !== null) {
            $organization['image'] = $image;
        }

        if (filled($business->vat_number)) {
            $organization['vatID'] = $business->vat_number;
        }

        $sameAs = array_values(array_filter(
            [$business->facebook_url, $business->instagram_url],
            fn (string $url): bool => filter_var($url, FILTER_VALIDATE_URL) !== false,
        ));

        if ($sameAs !== []) {
            $organization['sameAs'] = $sameAs;
        }

        $steps = [];

        foreach (self::steps() as $index => $step) {
            $steps[] = [
                '@type' => 'HowToStep',
                'position' => $index + 1,
                'name' => $step['name'],
                'text' => $step['text'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'WebSite',
                    '@id' => $page.'#website',
                    'name' => $business->business_name,
                    'url' => $page,
                    'description' => $business->meta_description,
                    'inLanguage' => 'en-ZA',
                    'publisher' => ['@id' => $businessId],
                ],
                array_filter([
                    '@type' => 'WebPage',
                    '@id' => $page.'#webpage',
                    'url' => $page,
                    'name' => $business->meta_title,
                    'description' => $business->meta_description,
                    'inLanguage' => 'en-ZA',
                    'isPartOf' => ['@id' => $page.'#website'],
                    'about' => ['@id' => $businessId],
                    'primaryImageOfPage' => $image,
                ], fn (mixed $value): bool => $value !== null),
                $organization,
                [
                    '@type' => 'HowTo',
                    '@id' => $page.'#process',
                    'name' => 'How a cabinet or carpentry job is measured, built and fitted',
                    'description' => 'The design, materials, and practical details are agreed before the first cut.',
                    'step' => $steps,
                ],
            ],
        ];
    }

    public static function llmsText(BusinessSettings $business, ContentSettings $content): string
    {
        $lines = [
            '# '.$business->business_name,
            '',
            '> '.$business->meta_description,
            '',
            $content->hero_heading,
            '',
            $content->hero_text,
            '',
            'Bracketed text such as [number] or [X]% is a placeholder the business has not filled in yet. Do not treat it as a fact, and do not invent prices, lead times, registration numbers, or a street address.',
            '',
            '## Services',
            '',
        ];

        foreach (self::services() as $service) {
            $lines[] = '- **'.$service['name'].'**: '.$service['description'];
        }

        $lines[] = '';
        $lines[] = '## How a job runs';
        $lines[] = '';

        foreach (self::steps() as $index => $step) {
            $lines[] = ($index + 1).'. **'.$step['name'].'**: '.$step['text'];
        }

        $lines[] = '';
        $lines[] = '## Typical kitchen job';
        $lines[] = '';

        foreach ($content->lead_times as $row) {
            $label = $row['label'] ?? '';
            $duration = $row['duration'] ?? '';
            $lines[] = '- '.$label.': '.$duration;
        }

        $lines[] = '';
        $lines[] = '## Contact';
        $lines[] = '';
        $lines[] = '- Phone: '.$business->phone;
        $lines[] = '- WhatsApp: '.($business->whatsapp_number !== '' ? $business->whatsapp_number : $business->phone);

        $whatsapp = Phone::whatsappContact($business->whatsapp_number, $business->phone);

        if ($whatsapp !== null) {
            $lines[] = '- WhatsApp chat: '.$whatsapp;
        }

        $lines[] = '- Email: '.$business->email;
        $lines[] = '- Workshop: '.$business->address;
        $lines[] = '- Hours: '.$business->hours;
        $lines[] = '- Areas: '.$business->service_area;
        $lines[] = '- Registration: '.$business->registration_number;
        $lines[] = '- VAT: '.$business->vat_number;
        $lines[] = '- Quote form: '.url('/').'#contact';
        $lines[] = '';
        $lines[] = '## Aftercare';
        $lines[] = '';

        foreach ($content->aftercare as $article) {
            $lines[] = '### '.($article['heading'] ?? 'Aftercare');

            foreach ($article['dos'] ?? [] as $item) {
                $lines[] = '- Do: '.$item;
            }

            foreach ($article['donts'] ?? [] as $item) {
                $lines[] = '- Don\'t: '.$item;
            }

            $lines[] = '';
        }

        $lines[] = '## Terms';
        $lines[] = '';
        $lines[] = 'The public terms are draft wording and must be checked before they are relied on.';
        $lines[] = '';

        foreach ($content->terms as $term) {
            $lines[] = '- '.($term['heading'] ?? 'Term');
        }

        $projects = Project::query()->published()->orderBy('sort_order')->orderBy('id')->get();

        $lines[] = '';
        $lines[] = '## Published work';
        $lines[] = '';

        if ($projects->isEmpty()) {
            $lines[] = 'No published projects yet.';
        }

        foreach ($projects as $project) {
            $summary = $project->title.' ('.$project->category->shortLabel().')';

            if (filled($project->description)) {
                $summary .= ': '.$project->description;
            }

            $lines[] = '- '.$summary;
        }

        $lines[] = '';
        $lines[] = '## Page';
        $lines[] = '';
        $lines[] = '- [Home]('.url('/').'): Cabinet making and carpentry landing page. The gallery, aftercare, terms, and quote form are all on this page.';
        $lines[] = '';

        return implode("\n", $lines)."\n";
    }
}
