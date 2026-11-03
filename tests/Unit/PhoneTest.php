<?php

namespace Tests\Unit;

use App\Support\Phone;
use PHPUnit\Framework\TestCase;

class PhoneTest extends TestCase
{
    public function test_tel_keeps_digits_and_a_leading_plus(): void
    {
        $this->assertSame('tel:+27825550101', Phone::tel('+27 82 555 0101'));
        $this->assertSame('tel:0825550101', Phone::tel('082 555 0101'));
    }

    public function test_whatsapp_rewrites_a_local_number_and_rejects_placeholders(): void
    {
        $this->assertSame('https://wa.me/27825550101', Phone::whatsapp('082 555 0101'));
        $this->assertSame('https://wa.me/27825550101', Phone::whatsapp('+27 82 555 0101'));
        $this->assertNull(Phone::whatsapp('[number]'));
        $this->assertNull(Phone::whatsapp(''));
    }

    public function test_whatsapp_contact_uses_the_phone_when_whatsapp_is_not_a_number(): void
    {
        $this->assertSame(
            'https://wa.me/27825550101?text=Hello',
            Phone::whatsappChat('[number]', '082 555 0101', 'Hello'),
        );
        $this->assertNull(Phone::whatsappChat('[number]', '[number]', 'Hello'));
    }
}
