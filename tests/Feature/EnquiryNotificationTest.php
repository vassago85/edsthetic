<?php

namespace Tests\Feature;

use App\Livewire\QuoteForm;
use App\Models\User;
use App\Notifications\EnquiryReceivedNotification;
use App\Settings\BusinessSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class EnquiryNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('quote|127.0.0.1');
        Mail::fake();

        $settings = app(BusinessSettings::class);
        $settings->notification_email = 'jobs@esthetic.test';
        $settings->whatsapp_number = '0825550101';
        $settings->phone = '0825550101';
        $settings->save();
    }

    public function test_a_quote_notifies_every_admin_and_his_whatsapp_account(): void
    {
        config([
            'services.whatsapp.token' => 'test-token',
            'services.whatsapp.phone_number_id' => '109876543210',
            'services.whatsapp.graph_version' => 'v22.0',
        ]);

        Http::fake([
            'graph.facebook.com/*' => Http::response(['messages' => [['id' => 'wamid.1']]]),
        ]);

        $admin = User::factory()->create();

        $component = Livewire::test(QuoteForm::class);

        $this->travel(2)->seconds();

        $component
            ->set('name', 'Ada Lovelace')
            ->set('phone', '0831112233')
            ->set('message', 'A pantry.')
            ->call('submit')
            ->assertHasNoErrors();

        $admin->refresh();
        $this->assertCount(1, $admin->notifications);
        $this->assertSame(EnquiryReceivedNotification::class, $admin->notifications->first()->type);

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://graph.facebook.com/v22.0/109876543210/messages'
                && $request['to'] === '27825550101'
                && str_contains($request['text']['body'], 'Ada Lovelace')
                && str_contains($request['text']['body'], 'A pantry.');
        });
    }

    public function test_whatsapp_is_skipped_until_his_account_is_connected(): void
    {
        config([
            'services.whatsapp.token' => null,
            'services.whatsapp.phone_number_id' => null,
        ]);

        Http::fake();

        $component = Livewire::test(QuoteForm::class);

        $this->travel(2)->seconds();

        $component
            ->set('name', 'Ada')
            ->set('phone', '0831112233')
            ->call('submit')
            ->assertHasNoErrors();

        Http::assertNothingSent();
    }

    public function test_a_placeholder_number_does_not_call_whatsapp(): void
    {
        config([
            'services.whatsapp.token' => 'test-token',
            'services.whatsapp.phone_number_id' => '109876543210',
        ]);

        $settings = app(BusinessSettings::class);
        $settings->whatsapp_number = '[number]';
        $settings->phone = '[number]';
        $settings->save();

        Http::fake();
        Notification::fake();

        $component = Livewire::test(QuoteForm::class);

        $this->travel(2)->seconds();

        $component
            ->set('name', 'Ada')
            ->set('phone', '0831112233')
            ->call('submit')
            ->assertHasNoErrors();

        Http::assertNothingSent();
        Notification::assertNothingSent();
    }
}
