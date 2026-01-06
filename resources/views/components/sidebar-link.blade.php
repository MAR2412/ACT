@props(['active'])

@php
    // Si no se proporciona el estado "active", mantener el comportamiento existente
    $isActive = $active ?? false;
@endphp

<a {{ $attributes->merge(['class' =>
    ($isActive  ? 'bg-[#4CAF50] flex transition duration-100 ease-in-out dark:bg-[#6C9A3B] items-center p-1 text-white rounded-lg dark:text-[#B5D948] border-[#A3C653] border dark:border-stone-800 dark:hover:text-[#B5D948] dark:hover:bg-[#6C9A3B] group' 
    : 'bg-stone-0 flex transition duration-100 ease-in-out items-center p-1 text-stone-500 hover:text-stone-800 rounded-lg dark:text-stone-400 dark:hover:text-stone-300 hover:bg-[#8DC63F]/10 dark:hover:bg-[#6C9A3B] group')]) }}>
    {{ $slot }}
</a>
