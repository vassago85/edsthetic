@php
    $showSection = $projects->isNotEmpty() || auth()->check();
@endphp
<div @unless ($showSection) hidden @endunless>
@if ($showSection)
<section id="gallery" aria-labelledby="gallery-heading">
  <div class="wrap">
    <x-site.section-head
      :number="$number"
      title="Recent work"
      heading-id="gallery-heading"
      lede="Jobs we've finished in the last while. Tap a photo to see it larger."
    />
    @if ($projects->isNotEmpty())
    <div class="filters" role="group" aria-label="Filter recent work">
      <button type="button" wire:click="setFilter('all')" aria-pressed="{{ $filter === 'all' ? 'true' : 'false' }}">All</button>
      @foreach ($categories as $category)
      <button type="button" wire:click="setFilter('{{ $category->value }}')" aria-pressed="{{ $filter === $category->value ? 'true' : 'false' }}">{{ $category->getLabel() }}</button>
      @endforeach
    </div>
    <div class="grid" id="grid">
      @foreach ($projects as $project)
        @php($cover = $project->getFirstMedia('photos'))
        <button
          type="button"
          class="tile {{ $project->tile_size->cssClass() }}"
          @if ($filter !== 'all' && $project->category->value !== $filter) hidden @endif
          wire:click="open({{ $project->id }})"
          aria-label="View {{ $project->title }}"
        >
          @if ($cover)
          <span class="cover">
            <img
              src="{{ $cover->getUrl('thumb') }}"
              @if ($cover->getSrcset('thumb') !== '') srcset="{{ $cover->getSrcset('thumb') }}" sizes="(max-width: 860px) 50vw, 33vw" @endif
              alt="{{ $project->title }}, {{ $project->category->shortLabel() }}"
              loading="lazy"
              decoding="async"
            >
          </span>
          @endif
          <span class="cap">{{ $project->title }} · {{ $project->category->shortLabel() }}</span>
        </button>
      @endforeach
    </div>
    @elseif (auth()->check())
    <p class="note admin-note">Recent jobs will appear here once photos are added in the admin.</p>
    @endif
  </div>

  <dialog
    id="viewer"
    wire:key="viewer-{{ $viewing?->id ?? 'closed' }}-{{ $photo?->id ?? 0 }}"
    aria-label="Project photo"
    x-data
    x-effect="const dialog = $el; const open = {{ $viewing ? 'true' : 'false' }}; if (open && !dialog.open) dialog.showModal(); if (!open && dialog.open) dialog.close();"
    x-on:click="if ($event.target === $el) $wire.close()"
    x-on:cancel="$wire.close()"
  >
    <div class="big">
      @if ($photo)
      <img
        src="{{ $photo->getUrl('large') }}"
        @if ($photo->getSrcset('large') !== '') srcset="{{ $photo->getSrcset('large') }}" sizes="(max-width: 900px) 92vw, 900px" @endif
        alt="{{ $viewing->title }}"
      >
      @else
      Job photo
      @endif
    </div>
    <div class="meta">
      <div>
        <strong>{{ $viewing->title ?? '' }}</strong><br>
        <span class="cap-meta">{{ $viewing?->category->shortLabel() }}</span>
      </div>
      <div class="btns">
        @if ($photos->count() > 1)
        <button type="button" wire:click="previous">Previous</button>
        <button type="button" wire:click="next">Next</button>
        @endif
        <button type="button" wire:click="close">Close</button>
      </div>
    </div>
  </dialog>
</section>
@endif
</div>
