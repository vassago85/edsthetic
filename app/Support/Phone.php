<?php

namespace App\Support;

class Phone
{
    public static function tel(string $number): string
    {
        $cleaned = preg_replace('/[^\d+]/', '', trim($number)) ?? '';

        return 'tel:'.$cleaned;
    }

    public static function whatsapp(string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', $number) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '27'.substr($digits, 1);
        }

        return 'https://wa.me/'.$digits;
    }

    public static function whatsappContact(string $whatsappNumber, string $phone): ?string
    {
        return self::whatsapp($whatsappNumber) ?? self::whatsapp($phone);
    }

    public static function whatsappChat(string $whatsappNumber, string $phone, string $message): ?string
    {
        $url = self::whatsappContact($whatsappNumber, $phone);

        if ($url === null) {
            return null;
        }

        return $url.'?text='.rawurlencode($message);
    }
}
