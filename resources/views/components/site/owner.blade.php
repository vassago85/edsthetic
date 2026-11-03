@props(['content', 'ownerImage' => null, 'number' => 2])
@php
    $showPhotoColumn = filled($ownerImage) || auth()->check();
@endphp
<section id="owner" aria-labelledby="owner-heading">
  <div class="wrap owner {{ $showPhotoColumn ? '' : 'no-photo' }}">
    @if ($ownerImage)
    <div class="photo">
      <img src="{{ $ownerImage }}" alt="{{ $content->owner_signoff ?: 'Owner' }} in the workshop">
    </div>
    @elseif (auth()->check())
    <div class="photo">
      <span class="admin-note">Photo: {{ $content->owner_signoff ?: 'Owner' }} in the workshop</span>
    </div>
    @endif
    <div>
      <x-site.section-head
        :number="$number"
        :title="$content->owner_heading"
        heading-id="owner-heading"
        label="Who you'll deal with"
        :solo="true"
      />
      <div class="bio">{!! \App\Support\RichText::html($content->owner_bio) !!}</div>
      @if (filled($content->owner_signoff))
      <p class="sign">{{ $content->owner_signoff }}</p>
      @endif
    </div>
  </div>
</section>
