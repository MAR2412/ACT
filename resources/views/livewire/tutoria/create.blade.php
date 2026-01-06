<x-dialog-modal wire:model="isOpen" maxWidth="lg">
    <x-slot name="title">
        <h3 class="text-lg font-semibold">
            {{ $isEditing ? 'Editar Tutoría' : 'Nueva Tutoría' }}
        </h3>
    </x-slot>

    <x-slot name="content">
        <form class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <x-label value="Nombre de la Tutoría *" />
                    <x-input wire:model.defer="nombre" class="w-full" placeholder="Ej: Tutoría de Matemáticas, Refuerzo de Inglés..." />
                    @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-2">
                    <x-label value="Descripción" />
                    <textarea 
                        wire:model.defer="descripcion" 
                        rows="3"
                        placeholder="Descripción detallada de la tutoría, objetivos, metodología..."
                        class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white"
                    ></textarea>
                    @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-2">
                    <x-label value="Materia (opcional)" />
                    <x-input wire:model.defer="materia" class="w-full" placeholder="Ej: Matemáticas, Física, Química, Inglés..." />
                    @error('materia') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

              

                <div>
                    <x-label value="Precio por hora (Lps) *" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-stone-500 dark:text-stone-400">L.</span>
                        </div>
                        <x-input 
                            wire:model.defer="precio_hora" 
                            type="number" 
                            min="0" 
                            step="0.01" 
                            class="w-full pl-10" 
                            placeholder="0.00" 
                        />
                    </div>
                    @error('precio_hora') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-label value="Sede *" />
                    <select wire:model.defer="sede_id"
                        class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white">
                        <option value="">Seleccione una sede</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}">{{ $sede->nombre }} - {{ $sede->municipio }}</option>
                        @endforeach
                    </select>
                    @error('sede_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-label value="Modalidad *" />
                    <select wire:model.defer="modalidad_id"
                        class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white">
                        <option value="">Seleccione una modalidad</option>
                        @foreach($modalidades as $modalidad)
                            <option value="{{ $modalidad->id }}">{{ $modalidad->nombre }}</option>
                        @endforeach
                    </select>
                    @error('modalidad_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-2">
                    <x-label value="Sección *" />
                    <select wire:model.defer="seccion_id"
                        class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white">
                        <option value="">Seleccione una sección</option>
                        @foreach($secciones as $seccion)
                            <option value="{{ $seccion->id }}">
                                {{ $seccion->nombre }} - {{ $seccion->dia }}{{ $seccion->diaF ? ' a ' . $seccion->diaF : '' }}
                                ({{ \Carbon\Carbon::parse($seccion->HoraInicio)->format('h:i A') }} - {{ \Carbon\Carbon::parse($seccion->HoraFin)->format('h:i A') }})
                            </option>
                        @endforeach
                    </select>
                    @error('seccion_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="col-span-2">
                    <label class="flex items-center space-x-2">
                        <x-checkbox wire:model.defer="estado" />
                        <span class="text-sm text-stone-600 dark:text-stone-400">Tutoría activa</span>
                    </label>
                    @error('estado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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