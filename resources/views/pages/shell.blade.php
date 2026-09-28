{{-- Temporary: renders the site shell until the page type is built. --}}
<x-layout.site :home="request()->routeIs('page.home')" />
