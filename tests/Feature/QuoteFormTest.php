<?php

namespace Tests\Feature;

use App\Enums\EnquiryStatus;
use App\Enums\JobType;
use App\Livewire\QuoteForm;
use App\Mail\EnquiryReceived;
use App\Models\Enquiry;
use App\Settings\BusinessSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class QuoteFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('quote|127.0.0.1');

        $settings = app(BusinessSettings::class);
        $settings->notification_email = 'jobs@esthetic.test';
        $settings->callback_days = '[X]';
        $settings->save();
    }

    public function test_name_and_phone_are_required(): void
    {
        Mail::fake();

        $component = Livewire::test(QuoteForm::class);

        $this->travel(2)->seconds();

        $component->call('submit')
            ->assertHasErrors(['name', 'phone'])
            ->assertSee('Add your name and phone number so we can call you back.')
            ->assertDontSee('Quote request sent');

        $this->assertDatabaseCount('enquiries', 0);
        Mail::assertNothingQueued();
    }

    public function test_email_must_be_valid_when_it_is_present(): void
    {
        $component = Livewire::test(QuoteForm::class);

        $this->travel(2)->seconds();

        $component
            ->set('name', 'Ada')
            ->set('phone', '0825550101')
            ->set('email', 'not-an-email')
            ->call('submit')
            ->assertHasErrors(['email'])
            ->assertSee('Enter a valid email address, or leave it blank.');

        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_a_valid_request_is_stored_and_the_notification_is_queued(): void
    {
        Mail::fake();

        $component = Livewire::test(QuoteForm::class);

        $this->travel(2)->seconds();

        $component
            ->set('name', 'Ada Lovelace')
            ->set('phone', '0825550101')
            ->set('email', 'ada@example.com')
            ->set('jobType', JobType::Vanity->value)
            ->set('message', 'A double vanity.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSee('Quote request sent')
            ->assertSee('within [X] working days');

        $enquiry = Enquiry::query()->first();

        $this->assertNotNull($enquiry);
        $this->assertSame('Ada Lovelace', $enquiry->name);
        $this->assertSame('0825550101', $enquiry->phone);
        $this->assertSame('ada@example.com', $enquiry->email);
        $this->assertSame(JobType::Vanity, $enquiry->job_type);
        $this->assertSame('A double vanity.', $enquiry->message);
        $this->assertSame('127.0.0.1', $enquiry->ip_address);
        $this->assertNull($enquiry->read_at);

        Mail::assertQueued(EnquiryReceived::class, function (EnquiryReceived $mail) use ($enquiry): bool {
            return $mail->hasTo('jobs@esthetic.test') && $mail->enquiry->is($enquiry);
        });
    }

    public function test_a_filled_honeypot_is_rejected(): void
    {
        Mail::fake();

        $component = Livewire::test(QuoteForm::class)
            ->set('name', 'Bot')
            ->set('phone', '0820000000')
            ->set('extraFields.my_name', 'spam-bot');

        $this->travel(2)->seconds();

        $component->call('submit')->assertStatus(403);

        $this->assertDatabaseCount('enquiries', 0);
        Mail::assertNothingQueued();
    }

    public function test_quote_requests_are_rate_limited_per_ip(): void
    {
        config([
            'site.quote_rate_limit' => 2,
            'site.quote_rate_decay_seconds' => 3600,
        ]);

        $component = Livewire::test(QuoteForm::class);

        $this->travel(2)->seconds();

        foreach (['One', 'Two'] as $name) {
            $component
                ->set('name', $name)
                ->set('phone', '0825550199')
                ->call('submit')
                ->assertHasNoErrors();
        }

        $component
            ->set('name', 'Three')
            ->set('phone', '0825550199')
            ->call('submit')
            ->assertHasErrors(['form'])
            ->assertSee('Too many quote requests');

        $this->assertSame(2, Enquiry::query()->count());
    }

    public function test_an_unknown_job_type_is_rejected(): void
    {
        $component = Livewire::test(QuoteForm::class);

        $this->travel(2)->seconds();

        $component
            ->set('name', 'Ada')
            ->set('phone', '0825550101')
            ->set('jobType', 'roofing')
            ->call('submit')
            ->assertHasErrors(['jobType']);

        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_a_blank_email_is_stored_as_null_and_the_enquiry_starts_as_new(): void
    {
        Mail::fake();

        $component = Livewire::test(QuoteForm::class);

        $this->travel(2)->seconds();

        $component
            ->set('name', 'Ada')
            ->set('phone', '0825550101')
            ->set('email', '')
            ->set('message', '')
            ->call('submit')
            ->assertHasNoErrors();

        $enquiry = Enquiry::query()->first();

        $this->assertNotNull($enquiry);
        $this->assertNull($enquiry->email);
        $this->assertNull($enquiry->message);
        $this->assertSame(EnquiryStatus::New, $enquiry->status);
        $this->assertInstanceOf(ShouldQueue::class, new EnquiryReceived($enquiry));
    }

    public function test_a_placeholder_notification_address_does_not_send_mail(): void
    {
        Mail::fake();

        $settings = app(BusinessSettings::class);
        $settings->notification_email = '[email]';
        $settings->save();

        $component = Livewire::test(QuoteForm::class);

        $this->travel(2)->seconds();

        $component
            ->set('name', 'Ada')
            ->set('phone', '0825550101')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSee('Quote request sent');

        $this->assertDatabaseCount('enquiries', 1);
        Mail::assertNothingQueued();
    }

    public function test_a_message_over_5000_characters_is_rejected(): void
    {
        $component = Livewire::test(QuoteForm::class);

        $this->travel(2)->seconds();

        $component
            ->set('name', 'Ada')
            ->set('phone', '0825550101')
            ->set('message', str_repeat('a', 5001))
            ->call('submit')
            ->assertHasErrors(['message'])
            ->assertSee('That message is too long');

        $this->assertDatabaseCount('enquiries', 0);
    }
}
