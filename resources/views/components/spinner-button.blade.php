@props([
    'loadingTarget' => null,
    'loadingText' => 'Cargando...',
    'type' => 'submit',
    'disabled' => false,
    'icon' => null, // Slot para el icono
])

@php
    $classes = 'inline-flex justify-center items-center px-4 py-2 bg-green-400 dark:bg-green-400 dark:border-green-500 dark:text-stone-900 dark:hover:bg-green-500 border border-transparent rounded-md font-semibold text-xs text-stone-900 uppercase tracking-widest hover:bg-green-500 focus:bg-green-600 dark:focus:bg-green-700 active:bg-stone-900 dark:active:bg-green-600 focus:outline-none focus:ring-2 dark:focus:ring-green-400 focus:ring-green-400 focus:ring-offset-2 dark:focus:ring-offset-green-600 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed';
@endphp

<button 
    {{ $attributes->merge([
        'type' => $type,
        'class' => $classes,
        'wire:loading.attr' => $loadingTarget ? 'disabled' : null
    ]) }}
    @if($disabled) disabled @endif
>
    @if($loadingTarget)
        <!-- Estado normal - oculto durante loading -->
        <span wire:loading.remove wire:target="{{ $loadingTarget }}" class="flex flex-row items-center">
            @if($icon)
                <!-- Icono personalizado -->
                {{ $icon }}
            @endif
            {{ $slot }}
        </span>
        
        <!-- Estado loading - visible durante loading -->
        <span wire:loading wire:target="{{ $loadingTarget }}" class="flex flex-row items-center">
            <!-- Spinner reemplaza al icono -->
            
            <!-- Texto de carga -->
            {{ $loadingText }}
        </span>
    @else
        <!-- Sin loading target, mostrar contenido normal -->
        <span class="flex flex-row items-center">
            @if($icon)
                {{ $icon }}
            @endif
            {{ $slot }}
        </span>
    @endif
</button>
