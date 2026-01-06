<x-dialog-modal wire:model="isOpen" maxWidth="3xl">
    <x-slot name="title">
        <h3 class="text-lg font-semibold">
            {{ $isEditing ? 'Editar Estudiante' : 'Nuevo Estudiante' }}
        </h3>
    </x-slot>

    <x-slot name="content">
        <form class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-label value="Nombre *" />
                            <x-input wire:model.defer="nombre" class="w-full" placeholder="Ej: Juan Carlos" />
                            @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="Apellido *" />
                            <x-input wire:model.defer="apellido" class="w-full" placeholder="Ej: Pérez López" />
                            @error('apellido') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="DNI *" />
                            <x-input wire:model.defer="dni" class="w-full" placeholder="Ej: 0801199901234" maxlength="15" />
                            <p class="text-xs text-stone-500 dark:text-stone-400 mt-1">8-15 dígitos sin guiones</p>
                            @error('dni') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="Sexo *" />
                            <select wire:model.defer="sexo"
                                class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white">
                                @foreach($opcionesSexo as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('sexo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="Fecha de Nacimiento" />
                            <x-input wire:model.defer="fecha_nacimiento" type="date" class="w-full" max="{{ date('Y-m-d') }}" />
                            @error('fecha_nacimiento') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="flex items-center space-x-2">
                            <x-checkbox wire:model.defer="estado" />
                            <span class="text-sm text-stone-600 dark:text-stone-400">Estudiante activo</span>
                        </label>
                        @error('estado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-stone-50 dark:bg-stone-800 p-4 rounded-lg">
                        <h4 class="font-medium text-stone-700 dark:text-stone-300 mb-2">Información de Contacto</h4>
                        <div class="space-y-3">
                            <div>
                                <x-label value="Teléfono" />
                                <x-input wire:model.defer="telefono" class="w-full" placeholder="Ej: 9876-5432" />
                                @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <x-label value="Email" />
                                <x-input wire:model.defer="email" type="email" class="w-full" placeholder="ejemplo@correo.com" />
                                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <x-label value="Dirección" />
                                <textarea 
                                    wire:model.defer="direccion" 
                                    rows="2"
                                    class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white text-sm"
                                    placeholder="Dirección completa"
                                ></textarea>
                                @error('direccion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-stone-200 dark:border-stone-700 pt-4">
                <h4 class="font-medium text-stone-700 dark:text-stone-300 mb-3">Información del Tutor (Opcional)</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-label value="Nombre del Tutor" />
                        <x-input wire:model.defer="nombre_tutor" class="w-full" placeholder="Ej: María Elena Rodríguez" />
                        @error('nombre_tutor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label value="Teléfono del Tutor" />
                        <x-input wire:model.defer="telefono_tutor" class="w-full" placeholder="Ej: 9988-7766" />
                        @error('telefono_tutor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label value="Email del Tutor" />
                        <x-input wire:model.defer="email_tutor" type="email" class="w-full" placeholder="tutor@correo.com" />
                        @error('email_tutor') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
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