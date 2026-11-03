@props(['content', 'number' => 6])
<section id="terms" aria-labelledby="terms-heading">
  <div class="wrap">
    <x-site.section-head
      :number="$number"
      title="Terms and conditions"
      label="Terms"
      heading-id="terms-heading"
      :solo="true"
    />
    @auth
    <span class="draft">Draft wording. Have it checked before the site goes live.</span>
    @endauth
    <div class="terms-list">
      @foreach ($content->terms as $term)
      <details>
        <summary>
          <span>{{ $term['heading'] }}</span>
          <span class="toggle" aria-hidden="true"></span>
        </summary>
        <div class="term-body">{!! \App\Support\RichText::html($term['body'] ?? '') !!}</div>
      </details>
      @endforeach
    </div>
  </div>
</section>
