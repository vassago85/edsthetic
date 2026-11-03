@props(['business'])
@php
    $tel = \App\Support\Phone::tel($business->phone);
    $whatsapp = \App\Support\Phone::whatsappChat($business->whatsapp_number, $business->phone, 'Hello, I would like a quote.');
@endphp
@if ($tel !== 'tel:' || $whatsapp)
<nav
  class="mobile-cta"
  aria-label="Quick contact"
  x-bind:class="{ 'is-hidden': quoteInView }"
  x-bind:hidden="quoteInView"
>
  <a class="btn btn-secondary" href="{{ $tel }}">Call</a>
  @if ($whatsapp)
  <a class="btn btn-primary" href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>
  @else
  <a class="btn btn-primary" href="{{ $tel }}">Call</a>
  @endif
</nav>
@endif
