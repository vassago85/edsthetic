<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BusinessSettings extends Settings
{
    public string $business_name;

    public string $phone;

    public string $whatsapp_number;

    public string $email;

    public string $notification_email;

    public string $address;

    public string $hours;

    public string $service_area;

    public string $registration_number;

    public ?string $vat_number;

    public string $facebook_url;

    public string $instagram_url;

    public string $meta_title;

    public string $meta_description;

    public string $callback_days;

    public ?string $og_image;

    public static function group(): string
    {
        return 'business';
    }
}
