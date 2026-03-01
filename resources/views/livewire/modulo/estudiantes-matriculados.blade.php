<div>
    <!-- Modal de Estudiantes Matriculados -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Fondo oscuro -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <!-- Span para centrar modal -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal -->
                <div class="inline-block align-bottom bg-white dark:bg-stone-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    
                    <!-- Header -->
                    <div class="bg-white dark:bg-stone-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-stone-200 dark:border-stone-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-indigo-100 dark:bg-indigo-900/30 rounded-full p-2">
                                    <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg leading-6 font-medium text-stone-900 dark:text-stone-200" id="modal-title">
                                        Estudiantes Matriculados - {{ $moduloNombre }}
                                    </h3>
                                    <p class="text-sm text-stone-500 dark:text-stone-400">
                                        Sede: {{ $modulo->sede->nombre }} | 
                                        Modalidad: {{ $modulo->modalidad->nombre }} | 
                                        Sección: {{ $modulo->seccion->nombre }}
                                    </p>
                                </div>
                            </div>
                            <button wire:click="closeModal" class="text-stone-400 hover:text-stone-500 dark:hover:text-stone-300">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="bg-stone-50 dark:bg-stone-700/50 px-4 py-3 sm:px-6 border-b border-stone-200 dark:border-stone-700">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-white dark:bg-stone-800 rounded-lg p-3 shadow-sm border border-stone-200 dark:border-stone-700">
                                <p class="text-sm text-stone-500 dark:text-stone-400">Total Matriculados</p>
                                <p class="text-2xl font-semibold text-stone-900 dark:text-stone-200">{{ $totalMatriculados }}</p>
                            </div>
                            <div class="bg-white dark:bg-stone-800 rounded-lg p-3 shadow-sm border border-stone-200 dark:border-stone-700">
                                <p class="text-sm text-stone-500 dark:text-stone-400">Activos</p>
                                <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400">{{ $totalActivos }}</p>
                            </div>
                            <div class="bg-white dark:bg-stone-800 rounded-lg p-3 shadow-sm border border-stone-200 dark:border-stone-700">
                                <p class="text-sm text-stone-500 dark:text-stone-400">Aprobados</p>
                                <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $totalAprobados }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros -->
                    <div class="bg-white dark:bg-stone-800 px-4 py-3 sm:px-6 border-b border-stone-200 dark:border-stone-700">
                        <div class="flex flex-col sm:flex-row gap-3">
                            
                            <div class="w-full sm:w-48">
                                <select 
                                    wire:model.live="estadoFiltro" 
                                    class="w-full rounded-md border-stone-300 dark:border-stone-600 dark:bg-stone-700 dark:text-stone-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Todos los estados</option>
                                    @foreach($estadosMatricula as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-full sm:w-40">
                                <select 
                                    wire:model.live="aprobadoFiltro" 
                                    class="w-full rounded-md border-stone-300 dark:border-stone-600 dark:bg-stone-700 dark:text-stone-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">Todos</option>
                                    <option value="1">Aprobados</option>
                                    <option value="0">No aprobados</option>
                                </select>
                            </div>
                            <div class="w-full sm:w-32">
                                <select 
                                    wire:model.live="perPage" 
                                    class="w-full rounded-md border-stone-300 dark:border-stone-600 dark:bg-stone-700 dark:text-stone-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de estudiantes -->
                    <div class="bg-white dark:bg-stone-800 px-4 py-3 sm:px-6 max-h-96 overflow-y-auto">
                        @if($estudiantes->count() > 0)
                            <table class="min-w-full divide-y divide-stone-200 dark:divide-stone-700">
                                <thead class="bg-stone-50 dark:bg-stone-700 sticky top-0">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Estudiante</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Identidad</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Contacto</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Estado</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Aprobado</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Pagos</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Fecha Matrícula</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-stone-800 divide-y divide-stone-200 dark:divide-stone-700">
                                    @foreach($estudiantes as $matricula)
                                        <tr class="hover:bg-stone-50 dark:hover:bg-stone-700/50 transition-colors">
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                <div class="text-sm font-medium text-stone-900 dark:text-stone-200">
                                                    {{ $matricula->estudiante->nombre }} {{ $matricula->estudiante->apellido }}
                                                </div>
                                                @if($matricula->estudiante->trashed())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        Eliminado
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-stone-600 dark:text-stone-400">
                                                {{ $matricula->estudiante->dni?? 'N/A' }}
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                <div class="text-sm text-stone-600 dark:text-stone-400">
                                                    {{ $matricula->estudiante->email }}
                                                </div>
                                                <div class="text-xs text-stone-500 dark:text-stone-500">
                                                    {{ $matricula->estudiante->telefono ?? 'Sin teléfono' }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($matricula->estado == 'activa') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                                    @elseif($matricula->estado == 'completada') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                                    @elseif($matricula->estado == 'cancelada') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                                    @endif">
                                                    {{ ucfirst($matricula->estado) }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                @if($matricula->aprobado)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                                                        Sí
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                        No
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap">
                                                <div class="text-sm text-stone-600 dark:text-stone-400">
                                                    Pagados: {{ $matricula->meses_pagados }}/{{ $matricula->modulo->duracion_meses }}
                                                </div>
                                                <div class="text-xs text-stone-500 dark:text-stone-500">
                                                    Saldo: L. {{ number_format($matricula->saldo_pendiente, 2) }}
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 whitespace-nowrap text-sm text-stone-600 dark:text-stone-400">
                                                {{ \Carbon\Carbon::parse($matricula->fecha_matricula)->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Paginación -->
                            <div class="mt-4">
                                {{ $estudiantes->links() }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-stone-400 dark:text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-stone-900 dark:text-stone-200">No hay estudiantes</h3>
                                <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
                                    No se encontraron estudiantes matriculados en este módulo.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="bg-stone-50 dark:bg-stone-700/50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-stone-200 dark:border-stone-700">
                        <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-stone-300 dark:border-stone-600 shadow-sm px-4 py-2 bg-white dark:bg-stone-800 text-base font-medium text-stone-700 dark:text-stone-300 hover:bg-stone-50 dark:hover:bg-stone-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>