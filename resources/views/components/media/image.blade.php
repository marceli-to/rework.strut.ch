<picture>
	@foreach($mobileSources as $source)
		<source
			srcset="{{ $source['srcset'] }}"
			type="{{ $source['type'] }}"
			sizes="{{ $source['sizes'] }}"
			media="{{ $source['media'] }}"
		>
	@endforeach

	@foreach($sources as $source)
		<source
			srcset="{{ $source['srcset'] }}"
			type="{{ $source['type'] }}"
			sizes="{{ $source['sizes'] }}"
			@if(isset($source['media'])) media="{{ $source['media'] }}" @endif
		>
	@endforeach

	<img
		src="{{ $fallbackUrl }}"
		alt="{{ $alt }}"
		width="{{ $width }}"
		height="{{ $height }}"
		@if($class) class="{{ $class }}" @endif
		loading="{{ $loading }}"
		{{ $attributes }}
	>
</picture>

