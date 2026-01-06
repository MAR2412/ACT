@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 dark:bg-stone-900 dark:text-stone-200 dark:border-stone-700 focus:border-yellow-500 focus:ring-yellow-500 dark:focus:border-stone-500 dark:focus:ring-stone-500  rounded-md shadow-sm']) !!}>
