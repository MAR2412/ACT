@props(['active'])

@php
    $classes = ($active ?? false)
        ? 'transition duration-100 ease-in-out w-5 h-5 ml-2 text-white dark:text-yellow-400 group'
        : 'transition duration-100 ease-in-out w-5 h-5 ml-2 text-stone-500 dark:text-stone-400 dark:group-hover:text-stone-300 group-hover:text-stone-800 group';
@endphp

<svg {{ $attributes->merge(['class' => $classes]) }} aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
    height="24" fill="none" viewBox="0 0 24 24">
    {{ $slot }}
</svg>