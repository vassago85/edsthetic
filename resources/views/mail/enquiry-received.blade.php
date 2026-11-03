<x-mail::message>
# New quote request

**Name:** {{ $enquiry->name }}

**Phone:** {{ $enquiry->phone }}

**Email:** {{ $enquiry->email ?: 'Not given' }}

**Job:** {{ $enquiry->job_type->getLabel() }}

**Message:**

{{ $enquiry->message ?: 'Not given' }}

Sent from {{ $enquiry->ip_address ?: 'an unknown address' }} at {{ $enquiry->created_at?->timezone(config('app.timezone'))->format('Y-m-d H:i') }}.
</x-mail::message>
