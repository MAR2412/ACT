@props(['name' => '', 'value' => '', 'checked' => false, 'disabled' => false])

<label class="inline-flex items-center cursor-pointer {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
    <input 
        type="checkbox" 
        name="{{ $name }}" 
        value="{{ $value }}" 
        {{ $checked ? 'checked' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => 'sr-only peer']) }}
    >
    <div class="relative w-11 h-6 bg-stone-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-stone-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-stone-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-stone-600 peer-checked:bg-green-600"></div>
    @if($slot->isNotEmpty())
        <span class="ms-3 text-sm font-medium text-stone-900 dark:text-stone-300">{{ $slot }}</span>
    @endif
</label>
