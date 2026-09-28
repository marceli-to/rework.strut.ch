{{--
  Project caption of a homepage image: above the image below sm, from sm an
  overlay shown on hover of the surrounding link (`group`).
--}}
@props(['project'])
<figcaption class="block w-full mb-2 sm:absolute sm:top-0 sm:left-0 sm:h-full sm:mb-0 sm:overflow-hidden sm:text-center sm:bg-[rgba(255,255,255,.4)] sm:opacity-0 sm:transition-opacity sm:duration-120 sm:ease-out sm:group-hover:opacity-100">
  <span class="block w-full h-auto text-black text-xl leading-[1.21] sm:absolute sm:left-0 sm:top-1/2 sm:-translate-y-1/2 sm:px-16 sm:text-5xl sm:leading-[1.03] md:text-6xl">{{ $project->title ?: $project->name . ', ' . $project->location }}</span>
</figcaption>
