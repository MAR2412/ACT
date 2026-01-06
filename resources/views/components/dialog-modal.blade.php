@props(['id' => null, 'maxWidth' => null])

<x-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }}>
    <div class="px-6 py-4 border-b border-stone-200 dark:border-stone-700">
        <div class="text-lg font-medium text-stone-900 dark:text-stone-100">
            {{ $title }}
        </div>
    </div>

    <div class="px-6 py-4">
        {{ $content }}
    </div>

    <div class="px-6 py-4 bg-stone-50 dark:bg-stone-800 text-right border-t border-stone-200 dark:border-stone-700">
        {{ $footer }}
    </div>
</x-modal>