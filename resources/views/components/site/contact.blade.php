@props(['business', 'number' => 7])
@php
    $tel = \App\Support\Phone::tel($business->phone);
    $whatsapp = \App\Support\Phone::whatsappChat($business->whatsapp_number, $business->phone, 'Hello, I would like a quote.');
@endphp
<section id="contact" aria-labelledby="contact-heading">
  <div class="wrap contact">
    <div class="contact-copy">
      <x-site.section-head
        :number="$number"
        title="Get a quote"
        heading-id="contact-heading"
        lede="A rough size and a couple of photos of the space are enough to get started."
        :solo="true"
      />
      <div class="quick">
        <a class="btn btn-secondary ink" href="{{ $tel }}">Call {{ $business->phone }}</a>
        @if ($whatsapp)
        <a class="btn btn-primary" href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer">WhatsApp us</a>
        @endif
      </div>
      <dl>
        <div>
          <dt>Email</dt>
          <dd><a href="mailto:{{ $business->email }}">{{ $business->email }}</a></dd>
        </div>
        <div>
          <dt>Workshop</dt>
          <dd>{{ $business->address }}</dd>
        </div>
        <div>
          <dt>Hours</dt>
          <dd>{{ $business->hours }}</dd>
        </div>
        <div>
          <dt>Areas we work in</dt>
          <dd>{{ $business->service_area }}</dd>
        </div>
        @if (filled($business->whatsapp_number) || filled($business->phone))
        <div>
          <dt>WhatsApp</dt>
          <dd>{{ filled($business->whatsapp_number) ? $business->whatsapp_number : $business->phone }}</dd>
        </div>
        @endif
      </dl>
      <p class="helpful">Helpful to have: rough measurements, the finish you like and an idea of budget and timing.</p>
    </div>
    <livewire:quote-form />
  </div>
</section>
