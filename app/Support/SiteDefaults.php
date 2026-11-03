<?php

namespace App\Support;

use Spatie\LaravelSettings\Factories\SettingsRepositoryFactory;
use Spatie\LaravelSettings\SettingsRepositories\SettingsRepository;
use Spatie\LaravelSettings\Support\SettingsCacheFactory;

class SiteDefaults
{
    /**
     * @return array<string, mixed>
     */
    public static function business(): array
    {
        return [
            'business_name' => 'Esthetic Renovations',
            'phone' => '079 511 7900',
            'whatsapp_number' => '079 511 7900',
            'email' => 'info@edsthetic.co.za',
            'notification_email' => 'info@edsthetic.co.za',
            'address' => '[address]',
            'hours' => 'Monday to Friday',
            'service_area' => 'Gauteng. AA rates apply outside Gauteng.',
            'registration_number' => '[Registration number]',
            'vat_number' => '',
            'facebook_url' => '',
            'instagram_url' => '',
            'meta_title' => 'Esthetic Renovations | Made to measure. Built with pride.',
            'meta_description' => 'Kitchens, built-ins, vanities, storage and carpentry across Gauteng. Made to measure, fitted by our own team.',
            'callback_days' => '5–7',
            'og_image' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function content(): array
    {
        return [
            'hero_heading' => 'Kitchens, cupboards and carpentry, built in our own workshop.',
            'hero_text' => 'We measure on site, build to the millimetre and fit it ourselves. The person who quotes your job is the person who stands in your kitchen on installation day.',
            'owner_heading' => "Who you'll deal with",
            'owner_bio' => "<p>[Owner name] has been building cabinets since [year]. [One or two sentences in their own words: where they trained, why they started the business, what they care about on a job.]</p><p>[Team size] people work out of our workshop in [suburb]. We don't subcontract the joinery, so the standard you see in the samples is the standard you get.</p>",
            'owner_signoff' => '[Owner name], owner',
            'lead_times' => [
                ['label' => 'Quote call-out', 'duration' => '5–7 working days'],
                ['label' => 'Measure to finished quote', 'duration' => '3–8 working days'],
                ['label' => 'Melamine (from deposit)', 'duration' => '4–7 working days'],
                ['label' => 'Wrap (from deposit)', 'duration' => 'About 2 weeks'],
                ['label' => 'Workshop prep / assembly', 'duration' => '1–3 days'],
                ['label' => 'Installation', 'duration' => '2–10 working days'],
            ],
            'terms' => [
                [
                    'heading' => 'Quotes',
                    'body' => '<p>Quotes are valid for <mark>[X] days</mark> and list the work, drawings, dimensions, materials, finish, price and anything excluded. Final measurements are checked on site before manufacture. Board prices can change; we confirm before ordering.</p>',
                ],
                [
                    'heading' => 'Deposit and payment',
                    'body' => '<p>A deposit of <mark>[X]%</mark> confirms the order and pays for materials. The balance is due <mark>[on completion / per the schedule in your quote]</mark>. Goods remain ours until paid in full.</p>',
                ],
                [
                    'heading' => 'Lead times and installation',
                    'body' => '<p>We aim to respond to quote call-outs within <mark>5–7 working days</mark>. From measuring to a finished quote is typically <mark>3–8 working days</mark>. After the deposit clears, melamine delivery is usually <mark>4–7 working days</mark> and wrap delivery about <mark>2 weeks</mark>. Workshop prep is <mark>1–3 days</mark> and installation <mark>2–10 working days</mark>, depending on the size of the job. You provide clear access, power and a room ready for installation. If other trades or site conditions hold us up, we agree new dates with you before carrying on. Work outside Gauteng is quoted with AA rates.</p>',
                ],
                [
                    'heading' => 'Changes to the job',
                    'body' => "<p>Changes after sign-off are quoted separately and may move the price and completion date. We start a change once you've approved it in writing.</p>",
                ],
                [
                    'heading' => 'Natural materials and samples',
                    'body' => "<p>Timber and veneer vary in grain and colour and change with age and light. Samples show the type of finish, not an exact match. This variation isn't a defect.</p>",
                ],
                [
                    'heading' => 'Plumbing, electrical and stone tops',
                    'body' => "<p>Your quote states whether plumbing, electrical work and stone tops are included and who arranges them. Where these are done by others, we coordinate with them but don't guarantee their workmanship.</p>",
                ],
                [
                    'heading' => 'Handover and guarantee',
                    'body' => "<p>We walk through the finished job with you and note anything outstanding. Our workmanship is guaranteed for <mark>[X months]</mark> from installation, and hardware carries the manufacturer's warranty. The guarantee doesn't cover damage from water, heat, incorrect cleaning, misuse or alterations by others. Nothing here limits your rights under the Consumer Protection Act.</p>",
                ],
                [
                    'heading' => 'Cancellation',
                    'body' => '<p>Tell us as soon as possible if you need to cancel. Materials already cut or ordered, and work already done, are deducted from any refund. Cancellations are handled in line with the Consumer Protection Act.</p>',
                ],
                [
                    'heading' => 'Your information',
                    'body' => "<p>We use your details only to quote, schedule and complete your job, and handle them in line with POPIA. We don't share them with anyone outside the job.</p>",
                ],
            ],
            'aftercare' => [
                [
                    'heading' => 'Melamine and wrapped doors',
                    'dos' => [
                        'Wipe with a soft cloth and warm water with a drop of mild dish soap, then dry.',
                        'Wipe spills straight away, especially along joins and edging tape.',
                        'Keep kettles, air fryers and steam away from the underside of wall units.',
                    ],
                    'donts' => [
                        'Avoid scourers, abrasive powders, bleach, solvents and leaving water to stand.',
                    ],
                ],
                [
                    'heading' => 'Spray-painted and lacquered',
                    'dos' => [
                        'Wipe with a damp microfibre cloth and dry immediately.',
                        'Leave the paint [X weeks] to cure fully before anything heavier than a damp cloth.',
                        'Test any new cleaner on the inside of a door first.',
                    ],
                    'donts' => [
                        'Avoid magic erasers, abrasive pads, ammonia and alcohol-based sprays. They dull the sheen.',
                    ],
                ],
                [
                    'heading' => 'Solid wood and veneer',
                    'dos' => [
                        'Dust regularly and wipe with a barely damp cloth along the grain.',
                        'Use coasters and trivets. Keep pieces out of direct sun where you can.',
                        'Re-oil oiled finishes every [X months] with the oil we leave you.',
                    ],
                    'donts' => [
                        "Don't oil a sealed or painted surface, soak veneer edges or use silicone furniture polish.",
                    ],
                ],
                [
                    'heading' => 'Hinges, runners and daily use',
                    'dos' => [
                        'Close doors and drawers gently and keep loads within what the runners are rated for.',
                        'Keep sink and vanity bases dry. Report a plumbing leak as soon as you see it.',
                        "A dropped door is usually a hinge adjustment. Call us if you're not sure.",
                    ],
                    'donts' => [
                        "Don't stand on drawers or hang weight from open doors.",
                    ],
                ],
            ],
            'enquiry_retention_months' => 24,
        ];
    }

    public static function persist(): void
    {
        $repository = SettingsRepositoryFactory::create();

        foreach (['business' => self::business(), 'content' => self::content()] as $group => $values) {
            foreach ($values as $name => $value) {
                self::write($repository, $group, $name, $value);
            }
        }

        foreach (app(SettingsCacheFactory::class)->all() as $cache) {
            if ($cache->isEnabled()) {
                $cache->clear();
            }
        }
    }

    private static function write(SettingsRepository $repository, string $group, string $name, mixed $value): void
    {
        if ($repository->checkIfPropertyExists($group, $name)) {
            $repository->updatePropertiesPayload($group, [$name => $value]);

            return;
        }

        $repository->createProperty($group, $name, $value);
    }
}
