<x-dialog-modal wire:model="isOpen" maxWidth="md">
    <x-slot name="title">
        <h3 class="text-lg font-semibold">
            {{ $isEditing ? 'Editar Sección' : 'Nueva Sección' }}
        </h3>
    </x-slot>

    <x-slot name="content">
        <form class="space-y-4">
            <div>
                <x-label value="Nombre" />
                <x-input wire:model.defer="nombre" class="w-full" />
                @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-label value="Día" />
                <select wire:model.defer="dia"
                    class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white">
                    @foreach($diasSemana as $diaOption)
                        <option value="{{ $diaOption }}" {{ $diaOption == '' ? 'disabled selected' : '' }}>
                            {{ $diaOption ?: 'Seleccione un día' }}
                        </option>
                    @endforeach
                </select>
                @error('dia') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-label value="" />
                <select wire:model.defer="diaF"
                    class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white">
                    <option value="">Sin día final</option>
                    @foreach($diasSemana as $diaOption)
                        @if($diaOption)
                            <option value="{{ $diaOption }}">{{ $diaOption }}</option>
                        @endif
                    @endforeach
                </select>
                @error('diaF') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label value="Hora Inicio" />
                    <x-input wire:model.defer="HoraInicio" type="time" class="w-full" />
                    @error('HoraInicio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-label value="Hora Fin" />
                    <x-input wire:model.defer="HoraFin" type="time" class="w-full" />
                    @error('HoraFin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
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