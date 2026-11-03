<!doctype html>
<html lang="en-ZA">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>{{ $business->meta_title }}</title>
<meta name="description" content="{{ $business->meta_description }}">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<link rel="canonical" href="{{ url('/') }}">
<link rel="alternate" type="text/plain" href="{{ url('/llms.txt') }}" title="LLM-readable summary">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $business->business_name }}">
<meta property="og:locale" content="en_ZA">
<meta property="og:title" content="{{ $business->meta_title }}">
<meta property="og:description" content="{{ $business->meta_description }}">
<meta property="og:url" content="{{ url('/') }}">
@if ($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
<meta name="twitter:image" content="{{ $ogImage }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $business->meta_title }}">
<meta name="twitter:description" content="{{ $business->meta_description }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&family=Work+Sans:wght@400;500;600&family=Zilla+Slab:wght@500;600;700&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script type="application/ld+json">{!! $jsonLd !!}</script>
</head>
<body
  class="has-mobile-cta"
  x-data="{ quoteInView: false }"
  x-init="
    const target = document.getElementById('quote');
    if (!target || !('IntersectionObserver' in window)) return;
    const observer = new IntersectionObserver(([entry]) => { quoteInView = entry.isIntersecting; }, { threshold: 0.15 });
    observer.observe(target);
  "
>
<a class="skip" href="#main">Skip to content</a>
<x-site.header :business="$business" :show-gallery="$showGallery ?? false" />
@yield('content')
<div class="rule" aria-hidden="true"></div>
<x-site.footer :business="$business" />
<x-site.mobile-cta :business="$business" />
</body>
</html>
