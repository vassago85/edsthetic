@props(['number' => 1, 'hasGallery' => false])
<section id="work" aria-labelledby="work-heading">
  <div class="wrap">
    <x-site.section-head
      :number="$number"
      title="What we build"
      heading-id="work-heading"
      lede="Everything is made to measure for the room it goes into. No flat-pack carcasses squeezed into a space they weren't designed for."
    />
    <div class="services">
      @foreach (\App\Support\SiteProfile::services() as $service)
        @php
          $href = ($hasGallery && filled($service['category'] ?? null))
            ? url('/?category='.$service['category']).'#gallery'
            : null;
        @endphp
        @if ($href)
        <a class="service" href="{{ $href }}">
          <x-site.service-icon :name="$service['icon']" />
          <h3>{{ $service['name'] }}</h3>
          <p>{{ $service['description'] }}</p>
        </a>
        @else
        <div class="service">
          <x-site.service-icon :name="$service['icon']" />
          <h3>{{ $service['name'] }}</h3>
          <p>{{ $service['description'] }}</p>
        </div>
        @endif
      @endforeach
    </div>
  </div>
</section>
