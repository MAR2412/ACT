<div>
    <div class="mx-auto rounded-lg mt-8 sm:mt-6 lg:mt-4 mb-6">
        <div class="bg-white dark:bg-white/5 overflow-hidden shadow sm:rounded-lg p-4 sm:p-6">
            @if (session()->has('message'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-md" role="alert">
                    <p class="font-medium">{{ session('message') }}</p>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md" role="alert">
                    <p class="font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <div class="mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                    <h2 class="text-xl font-semibold text-stone-800 dark:text-stone-200">
                        {{ __('Administración de Matrículas de Tutorías') }}
                    </h2>

                    <div class="flex flex-col sm:flex-row w-full sm:w-auto space-y-3 sm:space-y-0 sm:space-x-2">
                        <div class="relative w-full sm:w-auto">
                            <x-input wire:model.live="search" type="text" placeholder="Buscar estudiantes, tutorías, estados..."
                                class="w-full pl-10 pr-4 py-2" />
                            <div class="absolute left-3 top-2.5">
                                <svg class="h-5 w-5 text-stone-500 dark:text-stone-400" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="w-full sm:w-auto">
                            <x-select id="perPage" wire:model.live="perPage" :options="[
                                ['value' => '10', 'text' => '10 por página'],
                                ['value' => '25', 'text' => '25 por página'],
                                ['value' => '50', 'text' => '50 por página'],
                                ['value' => '100', 'text' => '100 por página'],
                            ]" class="w-full" />
                        </div>
                       <button onclick="confirmDownload()" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md shadow-sm transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                            Descargar Matrícula (PDF)
                        </button>
                        @can('matriculas.matriculas-tutorias.crear')
                            <x-spinner-button wire:click="openModal" loadingTarget="openModal" :loadingText="__('Abriendo...')">
                                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                {{ __('Nueva Matrícula') }}
                            </x-spinner-button>
                        @endcan
                    </div>
                </div>
            </div>

            <x-table
                sort-field="{{ $sortField }}"
                sort-direction="{{ $sortDirection }}"
                :columns="[
                    ['key' => 'id', 'label' => 'ID', 'sortable' => true],
                    ['key' => 'estudiante', 'label' => 'Estudiante', 'sortable' => true],
                    ['key' => 'tutoria', 'label' => 'Tutoría'],
                    ['key' => 'tutorias', 'label' => 'Tutorías'],
                    ['key' => 'financiero', 'label' => 'Financiero'],
                    ['key' => 'estado', 'label' => 'Estado'],
                    ['key' => 'actions', 'label' => 'Acciones', 'class' => 'text-right'],
                ]"
                empty-message="No se encontraron matrículas"
                class="mt-6"
            >
                <x-slot name="desktop">
                    @forelse($matriculas as $matricula)
                        <tr class="hover:bg-stone-50 dark:hover:bg-stone-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-stone-900 dark:text-stone-300">
                                {{ $matricula->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-stone-900 dark:text-stone-300">
                                <div class="font-medium">{{ $matricula->estudiante->nombre }} {{ $matricula->estudiante->apellido }}</div>
                                <div class="text-sm text-stone-500 dark:text-stone-400">
                                    DNI: {{ $matricula->estudiante->dni }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-stone-900 dark:text-stone-300">
                                <div class="font-medium">{{ $matricula->tutoria->nombre }}</div>
                                <div class="text-sm text-stone-500 dark:text-stone-400">
                                    Precio/hora: L. {{ number_format($matricula->precio_hora_aplicado, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-stone-900 dark:text-stone-300">
                                <div class="text-sm">
                                    <div>Registradas: {{ $matricula->tutorias_registradas }}</div>
                                    <div>Pagadas: {{ $matricula->tutorias_pagadas }}</div>
                                    <div>Pendientes: {{ $matricula->tutorias_registradas - $matricula->tutorias_pagadas }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-stone-900 dark:text-stone-300">
                                <div class="text-sm">
                                    <div class="{{ $matricula->saldo_pendiente > 0 ? 'text-red-600 dark:text-red-400 font-medium' : 'text-emerald-600 dark:text-emerald-400 font-medium' }}">
                                        Saldo: L. {{ number_format($matricula->saldo_pendiente, 2) }}
                                    </div>
                                    <button wire:click="openRegistrarTutoriaModal({{ $matricula->id }})"
                                            class="mt-1 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        + Registrar Tutoría
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($matricula->trashed())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Eliminado
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                        $matricula->estado == 'activa' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                        ($matricula->estado == 'completada' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300' :
                                        ($matricula->estado == 'cancelada' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' :
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'))
                                    }}">
                                        {{ ucfirst($matricula->estado) }}
                                    </span>
                                    @if($matricula->aprobado)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                                                Aprobado
                                            </span>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @if($matricula->trashed())
                                        @can('matriculas.matriculas-tutorias.restaurar')
                                            <button wire:click="restore({{ $matricula->id }})"
                                                class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300"
                                                title="Restaurar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        @endcan
                                    @else
                                        @can('matriculas.matriculas-tutorias.editar')
                                            <button wire:click="edit({{ $matricula->id }})"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                                title="Editar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                    <path fill-rule="evenodd"
                                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        @endcan
                                        @can('matriculas.matriculas-tutorias.eliminar')
                                            <button onclick="confirmDelete({{ $matricula->id }}, '{{ $matricula->estudiante->nombre }} {{ $matricula->estudiante->apellido }}')"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    title="Eliminar">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-stone-500 dark:text-stone-400">
                                No se encontraron matrículas
                            </td>
                        </tr>
                    @endforelse
                </x-slot>

                <x-slot name="mobile">
                    @forelse($matriculas as $matricula)
                        <div class="bg-white dark:bg-stone-800 p-4 rounded-lg shadow-sm border border-stone-200 dark:border-stone-700 mb-3">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <span class="bg-stone-100 dark:bg-stone-700 text-stone-800 dark:text-stone-300 px-2 py-1 rounded-full text-xs">
                                        ID: {{ $matricula->id }}
                                    </span>
                                    @if($matricula->trashed())
                                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded-full text-xs ml-1">
                                            Eliminado
                                        </span>
                                    @endif
                                </div>
                                <div class="flex space-x-2">
                                    @if($matricula->trashed())
                                        @can('matriculas.matriculas-tutorias.restaurar')
                                            <button wire:click="restore({{ $matricula->id }})"
                                                class="text-emerald-600 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        @endcan
                                    @else
                                        @can('matriculas.matriculas-tutorias.editar')
                                            <button wire:click="edit({{ $matricula->id }})"
                                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                    <path fill-rule="evenodd"
                                                        d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        @endcan
                                        @can('matriculas.matriculas-tutorias.eliminar')
                                            <button onclick="confirmDelete({{ $matricula->id }}, '{{ $matricula->estudiante->nombre }} {{ $matricula->estudiante->apellido }}')"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        @endcan
                                    @endif
                                </div>
                            </div>
                            
                            <h3 class="font-semibold text-stone-900 dark:text-stone-200 text-lg mb-1">
                                {{ $matricula->estudiante->nombre }} {{ $matricula->estudiante->apellido }}
                            </h3>
                            <div class="text-sm text-stone-500 dark:text-stone-400 mb-2">
                                DNI: {{ $matricula->estudiante->dni }}
                            </div>
                            
                            <div class="mb-3">
                                <h4 class="font-medium text-stone-900 dark:text-stone-200">Tutoría:</h4>
                                <div class="text-sm text-stone-600 dark:text-stone-400">
                                    <div>{{ $matricula->tutoria->nombre }}</div>
                                    <div>Precio/hora: L. {{ number_format($matricula->precio_hora_aplicado, 2) }}</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <h4 class="font-medium text-stone-900 dark:text-stone-200">Tutorías:</h4>
                                <div class="text-sm text-stone-600 dark:text-stone-400">
                                    <div>Registradas: {{ $matricula->tutorias_registradas }}</div>
                                    <div>Pagadas: {{ $matricula->tutorias_pagadas }}</div>
                                    <div>Pendientes: {{ $matricula->tutorias_registradas - $matricula->tutorias_pagadas }}</div>
                                </div>
                                <button wire:click="openRegistrarTutoriaModal({{ $matricula->id }})"
                                        class="mt-2 text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    + Registrar Tutoría
                                </button>
                            </div>
                            
                            <div class="mb-3">
                                <h4 class="font-medium text-stone-900 dark:text-stone-200">Financiero:</h4>
                                <div class="text-sm text-stone-600 dark:text-stone-400">
                                    <div class="{{ $matricula->saldo_pendiente > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                        Saldo: L. {{ number_format($matricula->saldo_pendiente, 2) }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $matricula->estado == 'activa' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                    ($matricula->estado == 'completada' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300' :
                                    ($matricula->estado == 'cancelada' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' :
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'))
                                }}">
                                    {{ ucfirst($matricula->estado) }}
                                </span>
                                
                                @if($matricula->aprobado)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                                        Aprobado
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-stone-800 p-4 rounded-lg shadow text-center text-stone-500 dark:text-stone-400">
                            No se encontraron matrículas
                        </div>
                    @endforelse
                </x-slot>

                <x-slot name="footer">
                    {{ $matriculas->links() }}
                </x-slot>
            </x-table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id, nombre) {
            Swal.fire({
                title: '¿Está seguro?',
                html: `¿Está seguro de eliminar la matrícula de <strong>${nombre}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                customClass: {
                    popup: 'dark:bg-stone-800 dark:text-stone-200',
                    title: 'dark:text-stone-200',
                    htmlContainer: 'dark:text-stone-300',
                    confirmButton: 'dark:bg-red-600 dark:hover:bg-red-700',
                    cancelButton: 'dark:bg-stone-600 dark:hover:bg-stone-700'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.delete(id);
                }
            });
        }
       
        function confirmDownload() {
    
            const modulos = @json(\App\Models\Tutoria::where('estado', true)->get(['id', 'nombre']));
            
            let options = {};
            modulos.forEach(m => options[m.id] = m.nombre);

            Swal.fire({
                title: 'Generar Reporte PDF',
                text: 'Seleccione el módulo que desea descargar:',
                input: 'select',
                inputOptions: options,
                inputPlaceholder: '--- Seleccionar Tutoría ---',
                showCancelButton: true,
                confirmButtonText: 'Generar PDF',
                confirmButtonColor: '#dc2626',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    // Llamada al método del componente
                    @this.call('downloadPdf', result.value);
                }
            });
        }

    </script>

    @include('livewire.matricula.create-tutoria')
    @include('livewire.matricula.registrar-tutoria')
    @include('livewire.matricula.error-modal')
</div>