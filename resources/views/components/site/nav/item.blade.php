@props(['top' => false])
<li @class(['block', $top ? 'md:inline-block md:mr-30' : 'md:mr-0 md:w-full'])>{{ $slot }}</li>
