<x-dialog-modal wire:model="isOpen" maxWidth="2xl">
    <x-slot name="title">
        <h3 class="text-lg font-semibold">
            {{ $isEditing ? 'Editar Matrícula' : 'Nueva Matrícula' }}
        </h3>
    </x-slot>

    <x-slot name="content">
        <form class="space-y-4">
            <div>
                <x-label value="Buscar Estudiante por DNI *" />
                <div class="flex space-x-2">
                    <x-input 
                        wire:model.defer="dni_busqueda" 
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
                <x-label value="Módulo *" />
                <select 
                    wire:model.live="modulo_id" 
                    class="w-full border-stone-300 dark:border-stone-600 dark:bg-stone-700 dark:text-white rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="">Seleccione un módulo</option>
                    @foreach($modulos as $modulo)
                        <option value="{{ $modulo->id }}">
                            {{ $modulo->nombre }} - {{ $modulo->nivel }} ({{ $modulo->modalidad->nombre ?? 'Sin modalidad' }})
                        </option>
                    @endforeach
                </select>
                @error('modulo_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                @if($modulo_info)
                    <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                        <p class="font-semibold text-blue-800 dark:text-blue-300">
                            {{ $modulo_info->nombre }}
                        </p>
                        <div class="grid grid-cols-2 gap-2 text-sm text-blue-600 dark:text-blue-400 mt-1">
                            <div>
                                <span class="font-medium">Nivel:</span> {{ $modulo_info->nivel }}
                            </div>
                            <div>
                                <span class="font-medium">Modalidad:</span> {{ $modulo_info->modalidad->nombre ?? 'N/A' }}
                            </div>
                            <div>
                                <span class="font-medium">Duración:</span> {{ $modulo_info->duracion_meses }} meses
                            </div>
                            <div>
                                <span class="font-medium">Precio:</span> L.{{ number_format($modulo_info->precio_mensual, 2) }}/mes
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            @if($examenSuficienciaHabilitado)
                <div class="border border-yellow-300 dark:border-yellow-600 rounded-md p-3 bg-yellow-50 dark:bg-yellow-900/20">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium text-yellow-800 dark:text-yellow-300">Examen de Suficiencia</span>
                    </div>
                    <p class="text-sm text-yellow-700 dark:text-yellow-400">
                        Este módulo requiere haber aprobado el módulo anterior. Puede habilitar el examen de suficiencia.
                    </p>
                    <label class="flex items-center space-x-2 mt-2">
                        <x-checkbox wire:model.live="examenSuficienciaHabilitado" />
                        <span class="text-sm text-stone-600 dark:text-stone-400">Habilitar examen de suficiencia</span>
                    </label>
                </div>
            @endif

            <!-- SECCIÓN DE DESCUENTO - INTEGRADA DIRECTAMENTE -->
            <div class="border border-stone-200 dark:border-stone-700 rounded-md p-4 space-y-3">
                <h4 class="font-semibold text-stone-700 dark:text-stone-300">Configuración de Descuento</h4>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2">
                        <x-checkbox wire:model.live="descuentoHabilitado" />
                        <span class="text-sm font-medium text-stone-700 dark:text-stone-300">Aplicar descuento</span>
                    </label>
                    
                    @if($descuentoHabilitado)
                    <button type="button" wire:click="deshabilitarDescuento" class="text-red-600 hover:text-red-800 text-sm">
                        Quitar descuento
                    </button>
                    @endif
                </div>
                
                @if($descuentoHabilitado)
                <div class="mt-3 space-y-3">
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" wire:model.live="descuento_primer_mes" value="1" 
                                   class="mr-2 text-green-600 focus:ring-green-500">
                            <span class="text-sm text-stone-700 dark:text-stone-300">Solo primer mes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" wire:model.live="descuento_primer_mes" value="0" 
                                   class="mr-2 text-green-600 focus:ring-green-500">
                            <span class="text-sm text-stone-700 dark:text-stone-300">Total del módulo</span>
                        </label>
                    </div>
                    
                    <div>
                        <x-label value="Porcentaje de Descuento (%)" />
                        <div class="flex items-center space-x-2">
                            <button type="button" 
                                    wire:click="decrementarDescuento"
                                    class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>
                            
                            <div class="flex-1 relative">
                                <x-input 
                                    type="number" 
                                    wire:model.live="porcentaje_descuento"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    class="text-center"
                                />
                                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                    <span class="text-stone-500">%</span>
                                </div>
                            </div>
                            
                            <button type="button" 
                                    wire:click="incrementarDescuento"
                                    class="w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mt-2 flex flex-wrap gap-1">
                            <button type="button" wire:click="setDescuento(5)" class="px-2 py-1 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 rounded">
                                5%
                            </button>
                            <button type="button" wire:click="setDescuento(10)" class="px-2 py-1 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 rounded">
                                10%
                            </button>
                            <button type="button" wire:click="setDescuento(15)" class="px-2 py-1 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 rounded">
                                15%
                            </button>
                            <button type="button" wire:click="setDescuento(20)" class="px-2 py-1 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 rounded">
                                20%
                            </button>
                            <button type="button" wire:click="setDescuento(30)" class="px-2 py-1 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 rounded">
                                30%
                            </button>
                        </div>
                        
                        @if($descuentoHabilitado && $porcentaje_descuento > 0)
                        <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-green-800 dark:text-green-300">Descuento Aplicado</span>
                                <span class="px-2 py-1 text-xs bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-300 rounded-full">
                                    {{ $porcentaje_descuento }}%
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div class="text-stone-600 dark:text-stone-400">
                                    <span class="font-medium">Precio Original:</span><br>
                                    L. {{ number_format($precio_original, 2) }}
                                </div>
                                @if($descuento_primer_mes)
                                <div class="text-blue-600 dark:text-blue-400">
                                    <span class="font-medium">1er mes con descuento:</span><br>
                                    L. {{ number_format($precio_con_descuento, 2) }}
                                </div>
                                @else
                                <div class="text-emerald-600 dark:text-emerald-400">
                                    <span class="font-medium">Nuevo Precio Total:</span><br>
                                    L. {{ number_format($precio_con_descuento, 2) }}
                                </div>
                                @endif
                                <div class="col-span-2 text-red-600 dark:text-red-400">
                                    <span class="font-medium">Descuento Total:</span>
                                    L. {{ number_format($monto_descuento, 2) }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <div class="border border-stone-200 dark:border-stone-700 rounded-md p-4 space-y-3">
                <h4 class="font-semibold text-stone-700 dark:text-stone-300">Información Financiera</h4>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-label value="Precio Total" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-stone-500 sm:text-sm">L.</span>
                            </div>
                            <x-input 
                                type="number" 
                                step="0.01"
                                wire:model.defer="precio_total_modulo" 
                                class="w-full pl-8"
                                readonly
                            />
                        </div>
                    </div>
                    <div>
                        <x-label value="Meses Pagados" />
                        <x-input 
                            type="number" 
                            wire:model.live="meses_pagados" 
                            class="w-full"
                            min="0"
                        />
                    </div>
                    <div>
                        <x-label value="Meses Pendientes" />
                        <x-input 
                            type="number" 
                            wire:model.live="meses_pendientes" 
                            class="w-full"
                            readonly
                        />
                    </div>
                    <div>
                        <x-label value="Saldo Pendiente" />
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-stone-500 sm:text-sm">L.</span>
                            </div>
                            <x-input 
                                type="number" 
                                step="0.01"
                                wire:model.live="saldo_pendiente" 
                                class="w-full pl-8"
                                readonly
                            />
                        </div>
                    </div>
                </div>

                <div class="flex space-x-2 pt-2">
                    <x-button type="button" wire:click="actualizarInformacionFinanciera" 
                             class="bg-blue-600 hover:bg-blue-700 text-xs">
                        Recalcular Financiero
                    </x-button>
                </div>
            </div>

            <div>
                <x-label value="Estado *" />
                <select 
                    wire:model.defer="estado" 
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
                <x-label value="Fecha de Matrícula *" />
                <x-input 
                    type="date" 
                    wire:model.defer="fecha_matricula" 
                    class="w-full"
                />
                @error('fecha_matricula') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <x-label value="Fecha Último Pago" />
                    <x-input 
                        type="date" 
                        wire:model.live="fecha_ultimo_pago" 
                        class="w-full"
                    />
                    @error('fecha_ultimo_pago') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <x-label value="Fecha Próximo Pago" />
                    <x-input 
                        type="date" 
                        wire:model.defer="fecha_proximo_pago" 
                        class="w-full"
                    />
                    @error('fecha_proximo_pago') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div>
                <x-label value="Matrícula Anterior (Opcional)" />
                <x-input 
                    type="number" 
                    wire:model.live="matricula_anterior_id" 
                    class="w-full"
                    placeholder="ID de matrícula anterior"
                />
                @error('matricula_anterior_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="flex items-center space-x-2">
                    <x-checkbox wire:model.defer="aprobado" />
                    <span class="text-sm text-stone-600 dark:text-stone-400">Aprobado</span>
                </label>
                @error('aprobado') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <x-label value="Observaciones" />
                <textarea 
                    wire:model.defer="observaciones" 
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