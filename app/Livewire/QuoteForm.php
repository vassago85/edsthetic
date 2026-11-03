<?php

namespace App\Livewire;

use App\Enums\JobType;
use App\Jobs\SendEnquiryWhatsApp;
use App\Mail\EnquiryReceived;
use App\Models\Enquiry;
use App\Models\User;
use App\Notifications\EnquiryReceivedNotification;
use App\Settings\BusinessSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Spatie\Honeypot\Honeypot;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class QuoteForm extends Component
{
    use UsesSpamProtection;

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $suburb = '';

    public string $jobType = '';

    public string $message = '';

    public bool $sent = false;

    public HoneypotData $extraFields;

    public function mount(): void
    {
        $this->jobType = JobType::Kitchen->value;
        $this->extraFields = new HoneypotData;
    }

    public function submit(): void
    {
        $this->sent = false;
        $this->protectAgainstSpam();

        $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:255'],
            'suburb' => ['nullable', 'string', 'max:120'],
            'jobType' => ['required', Rule::enum(JobType::class)],
            'message' => ['nullable', 'string', 'max:5000'],
        ], [
            'name.required' => 'Add your name and phone number so we can call you back.',
            'phone.required' => 'Add your name and phone number so we can call you back.',
            'email.email' => 'Enter a valid email address, or leave it blank.',
            'jobType.required' => 'Choose the kind of work you need.',
            'message.max' => 'That message is too long. Keep it under 5000 characters.',
        ]);

        $key = 'quote|'.request()->ip();
        $max = max(1, (int) config('site.quote_rate_limit'));
        $decay = max(1, (int) config('site.quote_rate_decay_seconds'));

        if (RateLimiter::tooManyAttempts($key, $max)) {
            $this->addError('form', 'Too many quote requests from this network. Please call us, or try again later.');

            return;
        }

        RateLimiter::hit($key, $decay);

        $enquiry = Enquiry::query()->create([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email !== '' ? $this->email : null,
            'suburb' => $this->suburb !== '' ? $this->suburb : null,
            'job_type' => $this->jobType,
            'message' => $this->message !== '' ? $this->message : null,
            'ip_address' => request()->ip(),
        ]);

        $notificationEmail = app(BusinessSettings::class)->notification_email;

        if (filter_var($notificationEmail, FILTER_VALIDATE_EMAIL)) {
            Mail::to($notificationEmail)->queue(new EnquiryReceived($enquiry));
        }

        SendEnquiryWhatsApp::dispatch($enquiry);

        $admins = User::query()->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new EnquiryReceivedNotification($enquiry));
        }

        $this->reset('name', 'phone', 'email', 'suburb', 'message');
        $this->jobType = JobType::Kitchen->value;
        $this->sent = true;
    }

    public function render(): View
    {
        return view('livewire.quote-form', [
            'jobTypes' => JobType::forQuoteForm(),
            'callbackDays' => app(BusinessSettings::class)->callback_days,
            'honeypotName' => app(Honeypot::class)->unrandomizedNameFieldName(),
            'honeypotValidFrom' => app(Honeypot::class)->validFromFieldName(),
        ]);
    }
}
