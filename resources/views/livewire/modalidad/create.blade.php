<x-dialog-modal wire:model="isOpen" maxWidth="md">
    <x-slot name="title">
        <h3 class="text-lg font-semibold">
            {{ $isEditing ? 'Editar Modalidad' : 'Nueva Modalidad' }}
        </h3>
    </x-slot>

    <x-slot name="content">
        <form class="space-y-4">
            <div>
                <x-label value="Nombre *" />
                <x-input wire:model.defer="nombre" class="w-full" />
                @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-label value="Descripción" />
                <textarea 
                    wire:model.defer="descripcion" 
                    rows="3"
                    class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white"
                ></textarea>
                @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="flex items-center space-x-2">
                    <x-checkbox wire:model.defer="estado" />
                    <span class="text-sm text-stone-600 dark:text-stone-400">Activo</span>
                </label>
            </div>
        </form>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="closeModal">
            Cancelar
        </x-secondary-button>

        <x-button wire:click="store">
            {{ $isEditing ? 'Actualizar' : 'Crear' }}
        </x-button>
    </x-slot>
</x-dialog-modal>