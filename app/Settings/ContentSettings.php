<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ContentSettings extends Settings
{
    public string $hero_heading;

    public string $hero_text;

    public string $owner_heading;

    public string $owner_bio;

    public string $owner_signoff;

    public array $lead_times;

    public array $terms;

    public array $aftercare;

    public int $enquiry_retention_months;

    public static function group(): string
    {
        return 'content';
    }
}
