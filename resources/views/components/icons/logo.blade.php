{{-- Single source: resources/svg/logo.svg (also used by the admin SPA) --}}
{!! str_replace('<svg ', '<svg class="' . ($class ?? '') . '" ', file_get_contents(resource_path('svg/logo.svg'))) !!}
