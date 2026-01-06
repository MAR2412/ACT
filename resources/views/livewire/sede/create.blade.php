<x-dialog-modal wire:model="isOpen" maxWidth="md">
    <x-slot name="title">
        <h3 class="text-lg font-semibold">
            {{ $isEditing ? 'Editar Sede' : 'Nueva Sede' }}
        </h3>
    </x-slot>

    <x-slot name="content">
        <form class="space-y-4">
            <div>
                <x-label value="Nombre *" />
                <x-input wire:model.defer="nombre" class="w-full" placeholder="Ej: Sede Central, Campus Norte..." />
                @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-label value="Descripción" />
                <textarea 
                    wire:model.defer="descripcion" 
                    rows="3"
                    placeholder="Descripción de la sede, ubicación específica, características..."
                    class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white"
                ></textarea>
                @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label value="Departamento *" />
                    <select wire:model.live="departamento"
                        class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white">
                        @foreach($departamentos as $depto)
                            <option value="{{ $depto }}">{{ $depto }}</option>
                        @endforeach
                    </select>
                    @error('departamento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-label value="Municipio *" />
                    <select wire:model.defer="municipio"
                        class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white"
                        {{ empty($municipiosDisponibles) ? 'disabled' : '' }}>
                        <option value="">Seleccione municipio</option>
                        @foreach($municipiosDisponibles as $mun)
                            <option value="{{ $mun }}">{{ $mun }}</option>
                        @endforeach
                    </select>
                    @if(empty($municipiosDisponibles))
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                            Seleccione un departamento primero
                        </p>
                    @endif
                    @error('municipio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="flex items-center space-x-2">
                    <x-checkbox wire:model.defer="estado" />
                    <span class="text-sm text-stone-600 dark:text-stone-400">Sede activa</span>
                </label>
                @error('estado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </form>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="closeModal">
            Cancelar
        </x-secondary-button>

        <x-button wire:click="store" class="ml-2">
            {{ $isEditing ? 'Actualizar' : 'Crear' }}
        </x-button>
    </x-slot>
</x-dialog-modal>