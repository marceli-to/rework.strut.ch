
@props([
  'href', 
  'label', 
  'active' => false
])

<li>
  <a 
    href="{{ $href }}" 
    aria-label="{{ $label }}"
    class="text-2xl leading-none hover:text-accent transition-colors {{ $active ? ' text-accent' : '' }}">
    {{ $label }}
  </a>
</li>