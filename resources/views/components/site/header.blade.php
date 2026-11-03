@props(['business', 'showGallery' => false])
@php
    $whatsapp = \App\Support\Phone::whatsappChat($business->whatsapp_number, $business->phone, 'Hello, I would like a quote.');
@endphp
<header x-data="{ open: false }" @keydown.escape.window="open = false">
  <div class="wrap nav">
    <a class="brand" href="#top">
      <svg width="30" height="30" viewBox="0 0 30 30" aria-hidden="true"><path d="M3 27V3h6v18h18v6z" fill="#B87A3D"/><path d="M13 3h14v14H13z" fill="none" stroke="#F2EFE8" stroke-width="2"/></svg>
      {{ $business->business_name }}
    </a>
    <button class="menu" type="button" aria-controls="links" :aria-expanded="open ? 'true' : 'false'" @click="open = !open">Menu</button>
    <ul class="links" id="links" :class="{ open: open }" @click="open = false">
      <li><a href="#work">What we build</a></li>
      @if ($showGallery)
      <li><a href="#gallery">Recent work</a></li>
      @endif
      <li><a href="#aftercare">Aftercare</a></li>
      <li><a href="#terms">Terms</a></li>
      <li><a href="#contact">Contact</a></li>
      @if ($whatsapp)
      <li><a class="wa" href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer">WhatsApp us</a></li>
      @endif
    </ul>
  </div>
</header>
