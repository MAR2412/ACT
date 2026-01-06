<x-dialog-modal wire:model="isOpen" maxWidth="md">
    <x-slot name="title">
        <h3 class="text-lg font-semibold">
            {{ $isEditing ? 'Editar Matrícula de Tutoría' : 'Nueva Matrícula de Tutoría' }}
        </h3>
    </x-slot>

    <x-slot name="content">
        <form class="space-y-4">
            <div>
                <x-label value="Buscar Estudiante por DNI *" />
                <div class="flex space-x-2">
                    <x-input 
                        wire:model.live="dni_busqueda" 
                        class="flex-grow" 
                        placeholder="Ingrese DNI del estudiante"
                    />
                    <x-button 
                        type="button" 
                        wire:click="buscarEstudiante"
                        wire:loading.attr="disabled"
                        class="bg-blue-600 hover:bg-blue-700"
                    >
                        <span wire:loading.remove>Buscar</span>
                        <span wire:loading>Buscando...</span>
                    </x-button>
                </div>
                @error('dni_busqueda') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                @if($estudiante_info)
                    <div class="mt-2 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-green-800 dark:text-green-300">
                                    {{ $estudiante_info->nombre }} {{ $estudiante_info->apellido }}
                                </p>
                                <p class="text-sm text-green-600 dark:text-green-400">
                                    DNI: {{ $estudiante_info->dni }}
                                </p>
                            </div>
                            <span class="px-2 py-1 text-xs bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-300 rounded-full">
                                Encontrado
                            </span>
                        </div>
                    </div>
                @endif
            </div>
            <div>
                <x-label value="Tutoría *" />
                <select 
                    wire:model.live="tutoria_id" 
                    class="w-full border-stone-300 dark:border-stone-600 dark:bg-stone-700 dark:text-white rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="">Seleccione una tutoría</option>
                    @foreach($tutorias as $tutoria)
                        <option value="{{ $tutoria->id }}">
                            {{ $tutoria->nombre }} - {{ $tutoria->sede->nombre ?? 'Sin sede' }}
                        </option>
                    @endforeach
                </select>
                @error('tutoria_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                @if($tutoria_info)
                    <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                        <p class="font-semibold text-blue-800 dark:text-blue-300">
                            {{ $tutoria_info->nombre }}
                        </p>
                        <div class="grid grid-cols-2 gap-2 text-sm text-blue-600 dark:text-blue-400 mt-1">
                            <div>
                                <span class="font-medium">Sede:</span> {{ $tutoria_info->sede->nombre ?? 'N/A' }}
                            </div>
                            <div>
                                <span class="font-medium">Modalidad:</span> {{ $tutoria_info->modalidad->nombre ?? 'N/A' }}
                            </div>
                            <div>
                                <span class="font-medium">Materia:</span> {{ $tutoria_info->materia ?? 'N/A' }}
                            </div>
                            <div>
                                <span class="font-medium">Precio por hora:</span> L.{{ number_format($tutoria_info->precio_hora, 2) }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label value="Estado *" />
                    <select 
                        wire:model.live="estado" 
                        class="w-full border-stone-300 dark:border-stone-600 dark:bg-stone-700 dark:text-white rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                    >
                        @foreach($estados as $estadoOption)
                            <option value="{{ $estadoOption }}">
                                {{ ucfirst($estadoOption) }}
                            </option>
                        @endforeach
                    </select>
                    @error('estado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <x-label value="Fecha de Inicio *" />
                    <x-input 
                        type="date" 
                        wire:model.live="fecha_inicio" 
                        class="w-full"
                    />
                    @error('fecha_inicio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div>
                <x-label value="Precio por Hora Aplicado (L.) *" />
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-stone-500 sm:text-sm">L.</span>
                    </div>
                    <x-input 
                        type="number" 
                        step="0.01"
                        wire:model.live="precio_hora_aplicado" 
                        class="w-full pl-8"
                        placeholder="Precio por hora para este estudiante"
                    />
                </div>
                @error('precio_hora_aplicado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                <p class="text-xs text-stone-500 dark:text-stone-400 mt-1">
                    Este es el precio por hora que se aplicará a cada tutoría registrada para este estudiante.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="flex items-center space-x-2">
                        <x-checkbox wire:model.live="aprobado" />
                        <span class="text-sm text-stone-600 dark:text-stone-400">Aprobado</span>
                    </label>
                    @error('aprobado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            <div>
                <x-label value="Observaciones" />
                <textarea 
                    wire:model.live="observaciones" 
                    rows="3"
                    class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white"
                    placeholder="Notas adicionales sobre la matrícula..."
                ></textarea>
                @error('observaciones') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </form>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="closeModal">
            Cancelar
        </x-secondary-button>

        <x-button wire:click="save">
            {{ $isEditing ? 'Actualizar' : 'Crear' }}
        </x-button>
    </x-slot>
</x-dialog-modal>