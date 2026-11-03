<?php

namespace App\Jobs;

use App\Models\Enquiry;
use App\Settings\BusinessSettings;
use App\Support\Phone;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendEnquiryWhatsApp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Enquiry $enquiry) {}

    public function handle(): void
    {
        $token = config('services.whatsapp.token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');

        if (! is_string($token) || $token === '' || ! is_string($phoneNumberId) || $phoneNumberId === '') {
            return;
        }

        $settings = app(BusinessSettings::class);
        $chat = Phone::whatsappContact($settings->whatsapp_number, $settings->phone);

        if ($chat === null) {
            return;
        }

        $version = config('services.whatsapp.graph_version', 'v22.0');

        Http::withToken($token)
            ->acceptJson()
            ->post('https://graph.facebook.com/'.$version.'/'.$phoneNumberId.'/messages', [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => str_replace('https://wa.me/', '', $chat),
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $this->body(),
                ],
            ])
            ->throw();
    }

    private function body(): string
    {
        $enquiry = $this->enquiry;

        return implode("\n", [
            'New quote request from '.$enquiry->name,
            'Phone: '.$enquiry->phone,
            'Email: '.($enquiry->email ?: 'Not given'),
            'Job: '.$enquiry->job_type->getLabel(),
            $enquiry->message ?: 'No message',
            url('/admin/enquiries/'.$enquiry->getKey()),
        ]);
    }
}
