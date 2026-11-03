@props(['number', 'title', 'headingId', 'lede' => null, 'solo' => false, 'label' => null])
@php
    $eyebrow = str_pad((string) $number, 2, '0', STR_PAD_LEFT).' / '.($label ?? $title);
@endphp
<div class="head {{ $solo ? 'solo' : '' }}">
  <div>
    <p class="eyebrow">{{ $eyebrow }}</p>
    <h2 id="{{ $headingId }}">{{ $title }}</h2>
  </div>
  @if (filled($lede))
  <p>{{ $lede }}</p>
  @endif
</div>
