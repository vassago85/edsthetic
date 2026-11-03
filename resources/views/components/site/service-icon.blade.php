@props(['name'])
<span class="service-icon" aria-hidden="true">
@switch($name)
  @case('kitchen')
    <svg viewBox="0 0 28 28"><path d="M5 24V10h18v14"/><path d="M5 14h18"/><path d="M10 10V6h8v4"/><path d="M12 18h4v6"/></svg>
    @break
  @case('builtin')
    <svg viewBox="0 0 28 28"><path d="M6 4h16v20H6z"/><path d="M14 4v20"/><path d="M10 12h.01M18 12h.01"/></svg>
    @break
  @case('vanity')
    <svg viewBox="0 0 28 28"><path d="M5 12h18v10H5z"/><path d="M9 12V9a5 5 0 0 1 10 0v3"/><path d="M14 16v3"/></svg>
    @break
  @case('shelving')
    <svg viewBox="0 0 28 28"><path d="M4 8h20M4 14h20M4 20h20"/><path d="M6 8v12M22 8v12"/></svg>
    @break
  @case('carpentry')
    <svg viewBox="0 0 28 28"><path d="M5 20l8-14 3 5 7 1-10 10H5z"/><path d="M11 14l3 5"/></svg>
    @break
  @default
    <svg viewBox="0 0 28 28"><path d="M6 22V8l8-4 8 4v14"/><path d="M10 22v-7h8v7"/><path d="M6 12h16"/></svg>
@endswitch
</span>
