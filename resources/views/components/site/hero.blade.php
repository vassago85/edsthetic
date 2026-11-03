@props(['content', 'business', 'heroImage' => null, 'showGallery' => false])
@php
    $heroClasses = 'hero'.($heroImage ? ' has-photo' : '');
    $style = $heroImage ? '--hero-image: url('.$heroImage.')' : null;
@endphp
<div class="{{ $heroClasses }}" id="top" @if ($style) style="{{ $style }}" @endif>
  <div class="wrap">
    <div class="hero-copy">
      <h1>{{ $content->hero_heading }}</h1>
      <p>{{ $content->hero_text }}</p>
      <div class="btns">
        <a class="btn btn-primary" href="#contact">Book a site measure</a>
        @if ($showGallery)
        <a class="btn btn-secondary" href="#gallery">See recent work</a>
        @endif
      </div>
      <div class="facts">
        <div class="fact">
          <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M2 8h3M7 8h7" stroke="currentColor" stroke-width="2"/><path d="M2 8l3 3 3-5" fill="none" stroke="currentColor" stroke-width="2"/></svg>
          <span><strong>Working in</strong> {{ $business->service_area }}</span>
        </div>
        <div class="fact">
          <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M2 8h3M7 8h7" stroke="currentColor" stroke-width="2"/><path d="M2 8l3 3 3-5" fill="none" stroke="currentColor" stroke-width="2"/></svg>
          <span><strong>Written, itemised</strong> quotes</span>
        </div>
        <div class="fact">
          <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M2 8h3M7 8h7" stroke="currentColor" stroke-width="2"/><path d="M2 8l3 3 3-5" fill="none" stroke="currentColor" stroke-width="2"/></svg>
          <span><strong>Measured and fitted</strong> by our own team</span>
        </div>
      </div>
      @auth
        @unless ($heroImage)
        <span class="stand-in">Stand-in. Replace by selecting a hero photo in Site settings.</span>
        @endunless
      @endauth
    </div>
  </div>
</div>
