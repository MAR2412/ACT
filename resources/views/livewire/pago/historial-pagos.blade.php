<div>
    <div class="mx-auto rounded-lg mt-8 sm:mt-6 lg:mt-4 mb-6">
        <div class="bg-white dark:bg-white/5 overflow-hidden shadow sm:rounded-lg p-4 sm:p-6">
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                    <h2 class="text-xl font-semibold text-stone-800 dark:text-stone-200">
                        {{ __('Historial de Pagos por Estudiante') }}
                    </h2>
                    
                    <div class="flex space-x-2">
                        <x-button wire:click="limpiarBusqueda" class="bg-gray-600 hover:bg-gray-700">
                            <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Limpiar
                        </x-button>
                    </div>
                </div>
                
                <!-- Selector de tipo de búsqueda -->
                <div class="bg-stone-50 dark:bg-stone-800/50 p-4 rounded-lg mb-6">
                    <div class="mb-4">
                        <x-label value="Tipo de Búsqueda" />
                        <div class="flex space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" wire:model.live="tipo_busqueda" value="estudiante" class="form-radio text-blue-600">
                                <span class="ml-2 text-stone-700 dark:text-stone-300">Buscar por Estudiante</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" wire:model.live="tipo_busqueda" value="modulo" class="form-radio text-blue-600">
                                <span class="ml-2 text-stone-700 dark:text-stone-300">Buscar por Módulo</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Búsqueda por estudiante -->
                    @if($tipo_busqueda == 'estudiante')
                        <div>
                            <x-label value="Buscar por DNI del Estudiante" />
                            <div class="flex space-x-2">
                                <x-input 
                                    wire:model.live="dni_busqueda" 
                                    type="text" 
                                    placeholder="Ingrese DNI del estudiante"
                                    class="w-full"
                                />
                                <x-button wire:click="buscar" class="bg-blue-600 hover:bg-blue-700">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </x-button>
                            </div>
                        </div>
                    @else
                        <!-- Búsqueda por módulo -->
                        <div>
                            <x-label value="Seleccionar Módulo" />
                            <div class="flex space-x-2">
                                <select 
                                    wire:model.live="modulo_busqueda" 
                                    class="w-full border-stone-300 dark:border-stone-600 dark:bg-stone-700 dark:text-white rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                                >
                                    <option value="">Seleccione un módulo</option>
                                    @foreach($modulos_disponibles as $modulo)
                                        <option value="{{ $modulo->id }}">
                                            {{ $modulo->nombre }} - 
                                            @if($modulo->sede)
                                                {{ $modulo->sede->nombre }} | 
                                            @endif
                                            {{ $modulo->modalidad->nombre ?? '' }} - 
                                            {{ $modulo->duracion_meses }} meses
                                        </option>
                                    @endforeach
                                </select>
                                <x-button wire:click="buscar" class="bg-blue-600 hover:bg-blue-700">
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </x-button>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Información del estudiante o módulo -->
                @if($estudiante_info)
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-emerald-800 dark:text-emerald-300 text-lg">
                                    {{ $estudiante_info->nombre }} {{ $estudiante_info->apellido }}
                                </h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-2 text-sm">
                                    <div class="text-emerald-700 dark:text-emerald-400">
                                        <span class="font-medium">DNI:</span> {{ $estudiante_info->dni }}
                                    </div>
                                    <div class="text-emerald-700 dark:text-emerald-400">
                                        <span class="font-medium">Teléfono:</span> {{ $estudiante_info->telefono ?? 'No registrado' }}
                                    </div>
                                    <div class="text-emerald-700 dark:text-emerald-400">
                                        <span class="font-medium">Email:</span> {{ $estudiante_info->email ?? 'No registrado' }}
                                    </div>
                                    <div class="text-emerald-700 dark:text-emerald-400">
                                        <span class="font-medium">Matrículas:</span> {{ $matriculas_estudiante->count() }}
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Estudiante activo
                                </span>
                            </div>
                        </div>
                    </div>
                @elseif($modulo_info)
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                        <div class="flex flex-col md:flex-row md:items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-blue-800 dark:text-blue-300 text-lg">
                                    {{ $modulo_info->nombre }}
                                </h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-2 text-sm">
                                    <div class="text-blue-700 dark:text-blue-400">
                                        <span class="font-medium">Duración:</span> {{ $modulo_info->duracion_meses }} meses
                                    </div>
                                    <div class="text-blue-700 dark:text-blue-400">
                                        <span class="font-medium">Precio mensual:</span> L. {{ number_format($modulo_info->precio_mensual, 2) }}
                                    </div>
                                    <div class="text-blue-700 dark:text-blue-400">
                                        <span class="font-medium">Total:</span> L. {{ number_format($modulo_info->precio_mensual * $modulo_info->duracion_meses, 2) }}
                                    </div>
                                    <div class="text-blue-700 dark:text-blue-400">
                                        <span class="font-medium">Matrículas:</span> {{ $matriculas_estudiante->count() }}
                                    </div>
                                </div>
                                <div class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                                    @if($modulo_info->sede)
                                        <span class="font-medium">Sede:</span> {{ $modulo_info->sede->nombre }}
                                    @endif
                                    @if($modulo_info->modalidad)
                                        | <span class="font-medium">Modalidad:</span> {{ $modulo_info->modalidad->nombre }}
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Módulo activo
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-stone-300 dark:text-stone-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="text-lg font-medium text-stone-500 dark:text-stone-400 mb-2">
                            @if($tipo_busqueda == 'estudiante')
                                Buscar historial de pagos por estudiante
                            @else
                                Buscar historial de pagos por módulo
                            @endif
                        </h3>
                        <p class="text-stone-400 dark:text-stone-500">
                            @if($tipo_busqueda == 'estudiante')
                                Ingrese el DNI de un estudiante para ver su historial de pagos
                            @else
                                Seleccione un módulo para ver el historial de pagos de todos los estudiantes matriculados
                            @endif
                        </p>
                    </div>
                @endif
                
                <!-- Lista de matrículas -->
                @if(count($matriculas_estudiante) > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-stone-800 dark:text-stone-200 mb-4">
                            @if($estudiante_info)
                                Matrículas del Estudiante
                            @else
                                Matrículas del Módulo
                            @endif
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($matriculas_estudiante as $matricula)
                                <div class="bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer {{ $matricula_seleccionada && $matricula_seleccionada->id == $matricula->id ? 'ring-2 ring-blue-500' : '' }}"
                                     wire:click="seleccionarMatricula({{ $matricula->id }})">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            @if($estudiante_info)
                                                <h4 class="font-semibold text-stone-900 dark:text-stone-200">{{ $matricula->modulo->nombre }}</h4>
                                                <div class="text-xs text-stone-500 dark:text-stone-400 mt-1">
                                                    {{ $matricula->modulo->sede->nombre ?? 'Sede no disponible' }}
                                                </div>
                                            @else
                                                <h4 class="font-semibold text-stone-900 dark:text-stone-200">{{ $matricula->estudiante->nombre }} {{ $matricula->estudiante->apellido }}</h4>
                                                <div class="text-xs text-stone-500 dark:text-stone-400 mt-1">
                                                    DNI: {{ $matricula->estudiante->dni }}
                                                </div>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ 
                                            $matricula->estado == 'activa' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                            ($matricula->estado == 'completada' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300' :
                                            ($matricula->estado == 'cancelada' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' :
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'))
                                        }}">
                                            {{ ucfirst($matricula->estado) }}
                                        </span>
                                    </div>
                                    
                                    <div class="mt-3 text-sm text-stone-600 dark:text-stone-400">
                                        <div class="flex justify-between mb-1">
                                            <span>Fecha matrícula:</span>
                                            <span class="font-medium">{{ $matricula->fecha_matricula->format('d/m/Y') }}</span>
                                        </div>
                                        <div class="flex justify-between mb-1">
                                            <span>Total módulo:</span>
                                            <span class="font-medium">L. {{ number_format($matricula->precio_total_modulo, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between mb-1">
                                            <span>Saldo pendiente:</span>
                                            <span class="font-medium {{ $matricula->saldo_pendiente > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                                L. {{ number_format($matricula->saldo_pendiente, 2) }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between mb-1">
                                            <span>Meses:</span>
                                            <span>{{ $matricula->meses_pagados }}/{{ $matricula->meses_pagados + $matricula->meses_pendientes }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span>Pagos registrados:</span>
                                            <span class="font-medium">{{ $matricula->pagos_count }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <div class="flex items-center justify-between text-xs text-stone-500 dark:text-stone-400">
                                            <span>Progreso:</span>
                                            <span>{{ $matricula->meses_pagados }}/{{ $matricula->modulo->duracion_meses }}</span>
                                        </div>
                                        <div class="w-full bg-stone-200 dark:bg-stone-700 rounded-full h-2 mt-1">
                                            <div class="bg-blue-600 h-2 rounded-full" 
                                                 style="width: {{ $matricula->modulo->duracion_meses > 0 ? ($matricula->meses_pagados / $matricula->modulo->duracion_meses * 100) : 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($estudiante_info || $modulo_info)
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-stone-300 dark:text-stone-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-stone-500 dark:text-stone-400">
                            @if($estudiante_info)
                                No se encontraron matrículas para este estudiante
                            @else
                                No se encontraron matrículas para este módulo
                            @endif
                        </p>
                    </div>
                @endif
                
                <!-- Resumen de pagos de la matrícula seleccionada -->
                @if($matricula_seleccionada && $resumen_pagos)
                    <div class="mt-8 border-t border-stone-200 dark:border-stone-700 pt-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-stone-800 dark:text-stone-200">
                                    Historial de Pagos - 
                                    @if($estudiante_info)
                                        {{ $matricula_seleccionada->modulo->nombre }}
                                    @else
                                        {{ $matricula_seleccionada->estudiante->nombre }} {{ $matricula_seleccionada->estudiante->apellido }}
                                    @endif
                                </h3>
                                <p class="text-sm text-stone-500 dark:text-stone-400 mt-1">
                                    @if($estudiante_info)
                                        {{ $matricula_seleccionada->modulo->sede->nombre ?? 'Sede no disponible' }} | 
                                    @else
                                        DNI: {{ $matricula_seleccionada->estudiante->dni }} | 
                                    @endif
                                    Matrícula: {{ $matricula_seleccionada->fecha_matricula->format('d/m/Y') }}
                                </p>
                            </div>
                            
                            <div class="mt-4 md:mt-0 flex space-x-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                        {{ $resumen_pagos['porcentaje_completado'] }}%
                                    </div>
                                    <div class="text-xs text-stone-500 dark:text-stone-400">Completado</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                                        L. {{ number_format($resumen_pagos['total_pagado'], 2) }}
                                    </div>
                                    <div class="text-xs text-stone-500 dark:text-stone-400">Total pagado</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold {{ $resumen_pagos['saldo_pendiente'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                        L. {{ number_format($resumen_pagos['saldo_pendiente'], 2) }}
                                    </div>
                                    <div class="text-xs text-stone-500 dark:text-stone-400">Saldo pendiente</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Estadísticas resumen -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-lg bg-blue-100 dark:bg-blue-800 mr-3">
                                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-blue-800 dark:text-blue-300">{{ $resumen_pagos['pagos_completados'] }}</div>
                                        <div class="text-sm text-blue-600 dark:text-blue-400">Pagos completados</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-lg bg-yellow-100 dark:bg-yellow-800 mr-3">
                                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-yellow-800 dark:text-yellow-300">{{ $resumen_pagos['pagos_pendientes'] }}</div>
                                        <div class="text-sm text-yellow-600 dark:text-yellow-400">Pagos pendientes</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-lg bg-purple-100 dark:bg-purple-800 mr-3">
                                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-purple-800 dark:text-purple-300">{{ count($resumen_pagos['meses_pagados']) }}</div>
                                        <div class="text-sm text-purple-600 dark:text-purple-400">Meses pagados</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 rounded-lg bg-emerald-100 dark:bg-emerald-800 mr-3">
                                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-2xl font-bold text-emerald-800 dark:text-emerald-300">
                                            {{ $matricula_seleccionada->modulo->duracion_meses - count($resumen_pagos['meses_pagados']) }}
                                        </div>
                                        <div class="text-sm text-emerald-600 dark:text-emerald-400">Meses pendientes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Detalle de pagos mensuales -->
                        @if(count($resumen_pagos['meses_pagados']) > 0)
                            <div class="mb-6">
                                <h4 class="font-medium text-stone-800 dark:text-stone-200 mb-3">Meses Pagados</h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($resumen_pagos['meses_pagados'] as $mes)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ $mes }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Lista de pagos -->
                        <div class="space-y-6">
                            <!-- Pagos mensuales -->
                            @if($resumen_pagos['pagos_mensuales']->count() > 0)
                                <div>
                                    <h4 class="font-medium text-stone-800 dark:text-stone-200 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Pagos Mensuales ({{ $resumen_pagos['pagos_mensuales']->count() }})
                                    </h4>
                                    
                                    <div class="bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 rounded-lg overflow-hidden">
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-700">
                                                <thead class="bg-stone-50 dark:bg-stone-700/50">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Mes</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Fecha</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Monto</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Método</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Transacción</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-stone-200 dark:divide-stone-700">
                                                    @foreach($resumen_pagos['pagos_mensuales'] as $pago)
                                                        <tr class="hover:bg-stone-50 dark:hover:bg-stone-700/50">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-stone-900 dark:text-stone-300">
                                                                {{ \Carbon\Carbon::createFromFormat('Y-m', $pago->mes_pagado)->format('M Y') }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500 dark:text-stone-400">
                                                                {{ $pago->fecha_pago->format('d/m/Y') }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                                                L. {{ number_format($pago->monto, 2) }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500 dark:text-stone-400">
                                                                {{ ucfirst($pago->metodo_pago) }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-stone-500 dark:text-stone-400">
                                                                {{ $pago->numero_transaccion ?? 'N/A' }}
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                                                    $pago->estado == 'completado' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300' :
                                                                    ($pago->estado == 'pendiente' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' :
                                                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300')
                                                                }}">
                                                                    {{ ucfirst($pago->estado) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Otros pagos -->
                            @if($resumen_pagos['otros_pagos']->count() > 0)
                                <div>
                                    <h4 class="font-medium text-stone-800 dark:text-stone-200 mb-3 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Otros Pagos ({{ $resumen_pagos['otros_pagos']->count() }})
                                    </h4>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        @foreach($resumen_pagos['otros_pagos'] as $pago)
                                            <div class="bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 rounded-lg p-4">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div>
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ 
                                                            $pago->estado == 'completado' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300' :
                                                            ($pago->estado == 'pendiente' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' :
                                                            'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300')
                                                        }}">
                                                            {{ ucfirst($pago->estado) }}
                                                        </span>
                                                        <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                                            {{ ucfirst($pago->tipo) }}
                                                        </span>
                                                    </div>
                                                    <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                                        L. {{ number_format($pago->monto, 2) }}
                                                    </div>
                                                </div>
                                                
                                                <div class="text-sm text-stone-600 dark:text-stone-400">
                                                    <div class="flex justify-between mb-1">
                                                        <span>Fecha:</span>
                                                        <span>{{ $pago->fecha_pago->format('d/m/Y') }}</span>
                                                    </div>
                                                    <div class="flex justify-between mb-1">
                                                        <span>Método:</span>
                                                        <span>{{ ucfirst($pago->metodo_pago) }}</span>
                                                    </div>
                                                    @if($pago->numero_transaccion)
                                                    <div class="flex justify-between mb-1">
                                                        <span>Transacción:</span>
                                                        <span class="font-mono text-xs">{{ $pago->numero_transaccion }}</span>
                                                    </div>
                                                    @endif
                                                    @if($pago->observaciones)
                                                    <div class="mt-2 pt-2 border-t border-stone-100 dark:border-stone-700">
                                                        <span class="font-medium">Observación:</span>
                                                        <p class="text-xs mt-1">{{ $pago->observaciones }}</p>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            @if($resumen_pagos['pagos_mensuales']->count() == 0 && $resumen_pagos['otros_pagos']->count() == 0)
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 mx-auto text-stone-300 dark:text-stone-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-stone-500 dark:text-stone-400">No se encontraron pagos para esta matrícula</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>