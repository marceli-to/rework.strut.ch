@props(['member', 'showCv' => true])
<article>

  <h2 class="{{ $member->former ? 'text-lg' : 'text-2xl' }} leading-[1.174] mb-0">
    {{ $member->firstname }} {{ $member->name }}
  </h2>

  @if (! $member->former)
    <div>
      {{ $member->title }}<br>
      <a
        href="mailto:{{ $member->email }}"
        class="hover:text-accent transition-colors !no-underline"
        aria-label="E-Mail an {{ $member->firstname }} {{ $member->name }}">
        {{ $member->email }}
      </a>

      @if ($showCv && $member->cv)
        <div class="mt-18" x-data="{ open: false }">
          <x-buttons.toggle label="Lebenslauf" />
          <div
            x-cloak
            x-show="open"
            class="mt-18">
            {!! $member->cv !!}
          </div>
        </div>
      @endif
    </div>
  @endif
</article>
