@props(['content', 'business', 'number' => 5])
@php
    $whatsapp = \App\Support\Phone::whatsappChat($business->whatsapp_number, $business->phone, 'Hello, I am not sure which finish I have.');
@endphp
<section id="aftercare" aria-labelledby="aftercare-heading">
  <div class="wrap">
    <x-site.section-head
      :number="$number"
      title="Looking after your cabinets"
      label="Aftercare"
      heading-id="aftercare-heading"
      lede="Most damage we get called out for comes from water, steam and the wrong cleaner, not from use. Find your finish below."
    />
    <div class="care">
      @foreach ($content->aftercare as $article)
      <article>
        <h3>{{ $article['heading'] }}</h3>
        @if (!empty($article['dos']))
        <ul>
          @foreach ($article['dos'] as $item)
          <li>{{ $item }}</li>
          @endforeach
        </ul>
        @endif
        @if (!empty($article['donts']))
        <p class="avoid">{{ implode(' ', (array) $article['donts']) }}</p>
        @endif
      </article>
      @endforeach
    </div>
    <div class="unsure">
      <div>
        <strong>Not sure which finish you have?</strong>
        <p>It's on your handover sheet, or send us a photo on WhatsApp. Until you know, stick to a dry, soft cloth and skip the cleaners and oils.</p>
      </div>
      @if ($whatsapp)
      <a class="btn btn-primary" href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer">WhatsApp us</a>
      @endif
    </div>
  </div>
</section>
