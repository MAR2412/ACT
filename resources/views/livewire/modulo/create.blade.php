<x-dialog-modal wire:model="isOpen" maxWidth="4xl">
    <x-slot name="title">
        <h3 class="text-lg font-semibold">
            {{ $isEditing ? 'Editar Módulo' : 'Nuevo Módulo' }}
        </h3>
    </x-slot>

    <x-slot name="content">
        <form class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div class="col-span-2">
                        <x-label value="Nombre del Módulo *" />
                        <x-input wire:model.defer="nombre" class="w-full" placeholder="Ej: Matemáticas Básicas, Programación Web..." />
                        @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2">
                        <x-label value="Código (opcional)" />
                        <x-input wire:model.defer="codigo" class="w-full" placeholder="Ej: MOD-I, MAT-101, PROG-201..." />
                        @error('codigo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2">
                        <x-label value="Descripción" />
                        <textarea 
                            wire:model.defer="descripcion" 
                            rows="3"
                            placeholder="Descripción detallada del módulo, objetivos, contenido..."
                            class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white"
                        ></textarea>
                        @error('descripcion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="space-y-4">
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

                    <div>
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
                </div>
            </div>

            <div class="border-t border-stone-200 dark:border-stone-700 pt-4">
                <h4 class="font-medium text-stone-700 dark:text-stone-300 mb-4">Información Académica</h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <x-label value="Nivel *" />
                        <select wire:model.defer="nivel"
                            class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white">
                            @foreach($niveles as $nivelOption)
                                <option value="{{ $nivelOption }}">{{ $nivelOption }}</option>
                            @endforeach
                        </select>
                        @error('nivel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label value="Orden *" />
                        <x-input wire:model.defer="orden" type="number" min="1" class="w-full" />
                        <p class="text-xs text-stone-500 dark:text-stone-400 mt-1">Orden en la secuencia (1, 2, 3...)</p>
                        @error('orden') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label value="Duración (meses) *" />
                        <x-input wire:model.defer="duracion_meses" type="number" min="1" class="w-full" />
                        @error('duracion_meses') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label value="Módulo Requerido" />
                        <select wire:model.defer="modulo_requerido_id"
                            class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white">
                            <option value="">Ninguno (Primer módulo)</option>
                            @foreach($modulosDisponibles as $moduloReq)
                                @if(!$isEditing || $moduloReq->id !== $modulo->id)
                                    <option value="{{ $moduloReq->id }}">
                                        {{ $moduloReq->nombre }} ({{ $moduloReq->nivel }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('modulo_requerido_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-stone-200 dark:border-stone-700 pt-4">
                <h4 class="font-medium text-stone-700 dark:text-stone-300 mb-4">Información Financiera y Fechas</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <x-label value="Precio Mensual (Lps) *" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-stone-500 dark:text-stone-400">L.</span>
                            </div>
                            <x-input 
                                wire:model.defer="precio_mensual" 
                                type="number" 
                                min="0" 
                                step="0.01" 
                                class="w-full pl-10" 
                                placeholder="0.00" 
                            />
                        </div>
                        <p class="text-xs text-stone-500 dark:text-stone-400 mt-1">
                            @if($duracion_meses && $precio_mensual)
                                Total módulo: L. {{ number_format($precio_mensual * $duracion_meses, 2) }}
                            @endif
                        </p>
                        @error('precio_mensual') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label value="Fecha Inicio *" />
                        <x-input wire:model.defer="fecha_inicio" type="date" class="w-full" />
                        @error('fecha_inicio') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <x-label value="Fecha Fin (opcional)" />
                        <x-input wire:model.defer="fecha_fin" type="date" class="w-full" />
                        <p class="text-xs text-stone-500 dark:text-stone-400 mt-1">
                            Si no se especifica, se calculará automáticamente
                        </p>
                        @error('fecha_fin') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-stone-200 dark:border-stone-700 pt-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center space-x-2">
                            <x-checkbox wire:model.defer="es_ultimo_modulo" />
                            <span class="text-sm text-stone-600 dark:text-stone-400">Es último módulo</span>
                        </label>
                        @error('es_ultimo_modulo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                        <label class="flex items-center space-x-2">
                            <x-checkbox wire:model.defer="estado" />
                            <span class="text-sm text-stone-600 dark:text-stone-400">Módulo activo</span>
                        </label>
                        @error('estado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
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