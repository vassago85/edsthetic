@extends('layouts.site')

@section('content')
@php
    $includeGallery = $showGallery || auth()->check();
    $order = ['work', 'owner'];
    if ($includeGallery) {
        $order[] = 'gallery';
    }
    $order = array_merge($order, ['process', 'aftercare', 'terms', 'contact']);
    $sectionNumbers = [];
    foreach ($order as $index => $key) {
        $sectionNumbers[$key] = $index + 1;
    }
@endphp
<main id="main">
<x-site.hero :content="$content" :business="$business" :heroImage="$ogImage" :show-gallery="$showGallery" />
<div class="rule" aria-hidden="true"></div>
<x-site.services :number="$sectionNumbers['work']" :has-gallery="$showGallery" />
<x-site.owner :content="$content" :number="$sectionNumbers['owner']" />
@if ($includeGallery)
<livewire:gallery-grid :number="$sectionNumbers['gallery']" />
@endif
<x-site.process :content="$content" :number="$sectionNumbers['process']" />
<x-site.aftercare :content="$content" :business="$business" :number="$sectionNumbers['aftercare']" />
<x-site.terms :content="$content" :number="$sectionNumbers['terms']" />
<x-site.contact :business="$business" :number="$sectionNumbers['contact']" />
</main>
@endsection
