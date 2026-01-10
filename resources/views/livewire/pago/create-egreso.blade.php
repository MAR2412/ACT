<x-dialog-modal wire:model="isOpen" maxWidth="2xl">
    <x-slot name="title">
        <h3 class="text-lg font-semibold">
            {{ $isEditing ? 'Editar Egreso' : 'Nuevo Egreso' }}
        </h3>
    </x-slot>

    <x-slot name="content">
        <form class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-label for="monto_utilizado" value="Monto Utilizado *" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-stone-500">L.</span>
                        </div>
                        <x-input id="monto_utilizado" 
                            type="number" 
                            step="0.01" 
                            min="0.01"
                            wire:model.defer="monto_utilizado" 
                            class="w-full pl-10"
                            placeholder="0.00"
                        />
                    </div>
                    @error('monto_utilizado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-label for="fecha_egreso" value="Fecha de Egreso *" />
                    <x-input id="fecha_egreso" 
                        type="date" 
                        wire:model.defer="fecha_egreso" 
                        class="w-full"
                    />
                    @error('fecha_egreso') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <x-label for="descripcion" value="Descripción *" />
                <textarea id="descripcion" 
                    wire:model.defer="descripcion" 
                    rows="3"
                    class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white"
                    placeholder="Descripción detallada del egreso..."
                ></textarea>
                @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                <p class="text-xs text-stone-500 dark:text-stone-400 mt-1">
                    Máximo 500 caracteres
                </p>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" wire:model.defer="estado" class="rounded border-stone-300 text-emerald-600 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                    <span class="ml-2 text-sm text-stone-700 dark:text-stone-300">Egreso activo</span>
                </label>
                <p class="text-xs text-stone-500 dark:text-stone-400 mt-1">
                    Los egresos inactivos no se incluyen en los reportes totales
                </p>
            </div>
        </form>
    </x-slot>

    <x-slot name="footer">
        <div class="flex justify-between w-full">
            <x-secondary-button wire:click="closeModal">
                Cancelar
            </x-secondary-button>
            
            <x-button wire:click="save">
                {{ $isEditing ? 'Actualizar' : 'Guardar' }}
            </x-button>
        </div>
    </x-slot>
</x-dialog-modal>