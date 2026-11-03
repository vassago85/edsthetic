@props(['business'])
@php
    $legal = array_values(array_filter([
        filled($business->registration_number) && $business->registration_number !== '[Registration number]'
            ? $business->registration_number
            : null,
        filled($business->vat_number) ? 'VAT '.$business->vat_number : null,
    ]));
@endphp
<footer>
  <div class="wrap">
    <span>{{ $business->business_name }}. Cabinet making and carpentry in {{ rtrim($business->service_area, '.') }}.</span>
    <span class="legal">
      @foreach ($legal as $item){{ $item }} · @endforeach<a href="#terms">Terms</a>
    </span>
  </div>
</footer>
