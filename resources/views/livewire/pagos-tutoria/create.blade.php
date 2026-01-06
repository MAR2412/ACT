<x-dialog-modal wire:model="isOpen" maxWidth="3xl">
    <x-slot name="title">
        <h3 class="text-lg font-semibold">
            {{ $isEditing ? 'Editar Pago de Tutoría' : 'Nuevo Pago de Tutoría' }}
        </h3>
    </x-slot>

    <x-slot name="content">
        <form class="space-y-6">
            @if(!$isEditing)
            <div class="border border-stone-200 dark:border-stone-700 rounded-md p-4">
                <div class="flex items-center justify-between mb-4">
                    <x-label value="Número de Pagos a Ingresar:" class="font-medium" />
                    <div class="flex items-center space-x-2">
                        <x-button type="button" wire:click="decrementarPagos" class="bg-stone-600 hover:bg-stone-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                        </x-button>
                        <x-input type="number" wire:model.live="numeroPagos" min="1" max="20" class="w-20 text-center" />
                        <x-button type="button" wire:click="incrementarPagos" class="bg-stone-600 hover:bg-stone-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </x-button>
                    </div>
                </div>
                <p class="text-sm text-stone-500 dark:text-stone-400">
                    Puede ingresar hasta 20 pagos a la vez. Cada pago se procesará individualmente.
                </p>
            </div>
            @endif

            <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2">
                @foreach($pagosForms as $index => $form)
                <div class="border border-stone-200 dark:border-stone-700 rounded-md p-4 {{ $index > 0 ? 'mt-4' : '' }}">
                    @if(!$isEditing)
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-medium text-stone-700 dark:text-stone-300">
                            Pago #{{ $index + 1 }}
                        </h4>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-label value="ID de Matrícula de Tutoría *" />
                            <div class="flex space-x-2">
                                <x-input 
                                    wire:model.live="pagosForms.{{ $index }}.matricula_tutoria_id" 
                                    type="number" 
                                    class="flex-grow" 
                                    placeholder="Ingrese ID de matrícula de tutoría"
                                />
                                <x-button 
                                    type="button" 
                                    wire:click="buscarMatricula({{ $index }})"
                                    class="bg-blue-600 hover:bg-blue-700"
                                >
                                    Buscar
                                </x-button>
                            </div>
                            @error("pagosForms.{$index}.matricula_tutoria_id") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="Tipo de Pago *" />
                            <select 
                                wire:model.live="pagosForms.{{ $index }}.tipo" 
                                class="w-full border-stone-300 dark:border-stone-600 dark:bg-stone-700 dark:text-white rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            >
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo }}">{{ ucfirst(str_replace('_', ' ', $tipo)) }}</option>
                                @endforeach
                            </select>
                            @error("pagosForms.{$index}.tipo") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="Método de Pago *" />
                            <select 
                                wire:model.live="pagosForms.{{ $index }}.metodo_pago" 
                                class="w-full border-stone-300 dark:border-stone-600 dark:bg-stone-700 dark:text-white rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            >
                                @foreach($metodosPago as $metodo)
                                    <option value="{{ $metodo }}">{{ ucfirst($metodo) }}</option>
                                @endforeach
                            </select>
                            @error("pagosForms.{$index}.metodo_pago") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="Monto *" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-stone-500 sm:text-sm">L.</span>
                                </div>
                                <x-input 
                                    type="number" 
                                    step="0.01"
                                    min="0.01"
                                    wire:model.live="pagosForms.{{ $index }}.monto" 
                                    class="w-full pl-8"
                                />
                            </div>
                            @error("pagosForms.{$index}.monto") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="Monto Pagado *" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-stone-500 sm:text-sm">L.</span>
                                </div>
                                <x-input 
                                    type="number" 
                                    step="0.01"
                                    min="0.01"
                                    wire:model.live="pagosForms.{{ $index }}.monto_pagado" 
                                    class="w-full pl-8"
                                />
                            </div>
                            @error("pagosForms.{$index}.monto_pagado") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="Cambio" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-stone-500 sm:text-sm">L.</span>
                                </div>
                                <x-input 
                                    type="number" 
                                    step="0.01"
                                    wire:model.live="pagosForms.{{ $index }}.cambio" 
                                    class="w-full pl-8 bg-stone-50 dark:bg-stone-800"
                                    readonly
                                />
                            </div>
                            @error("pagosForms.{$index}.cambio") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="Estado *" />
                            <select 
                                wire:model.live="pagosForms.{{ $index }}.estado" 
                                class="w-full border-stone-300 dark:border-stone-600 dark:bg-stone-700 dark:text-white rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                            >
                                @foreach($estados as $estado)
                                    <option value="{{ $estado }}">{{ ucfirst($estado) }}</option>
                                @endforeach
                            </select>
                            @error("pagosForms.{$index}.estado") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="Fecha de Pago *" />
                            <x-input 
                                type="date" 
                                wire:model.live="pagosForms.{{ $index }}.fecha_pago" 
                                class="w-full"
                            />
                            @error("pagosForms.{$index}.fecha_pago") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="Número de Transacción" />
                            <x-input 
                                wire:model.live="pagosForms.{{ $index }}.numero_transaccion" 
                                class="w-full"
                                placeholder="Ej: 00123456789"
                            />
                            @error("pagosForms.{$index}.numero_transaccion") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <x-label value="Referencia Bancaria" />
                            <x-input 
                                wire:model.live="pagosForms.{{ $index }}.referencia_bancaria" 
                                class="w-full"
                                placeholder="Ej: REF-00123456"
                            />
                            @error("pagosForms.{$index}.referencia_bancaria") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-label value="Observaciones" />
                        <textarea 
                            wire:model.live="pagosForms.{{ $index }}.observaciones" 
                            rows="2"
                            class="w-full border-stone-300 dark:border-stone-600 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-stone-700 dark:text-white"
                            placeholder="Observaciones adicionales sobre el pago..."
                        ></textarea>
                        @error("pagosForms.{$index}.observaciones") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if(!$isEditing && !empty($form['matricula_tutoria_id']) && !empty($form['matricula_info']))
                        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-md">
                            <div class="flex justify-between items-start">
                                <div class="flex-grow">
                                    <p class="font-semibold text-blue-800 dark:text-blue-300">
                                        {{ $form['matricula_info']->estudiante->nombre }} {{ $form['matricula_info']->estudiante->apellido }}
                                    </p>
                                    <div class="grid grid-cols-2 gap-2 text-sm text-blue-600 dark:text-blue-400 mt-2">
                                        <div>
                                            <span class="font-medium">Tutoría:</span> {{ $form['matricula_info']->tutoria->nombre }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Horas:</span> {{ $form['matricula_info']->tutoria->total_horas }} horas
                                        </div>
                                        <div>
                                            <span class="font-medium">Precio por hora:</span> L. {{ number_format($form['matricula_info']->tutoria->precio_hora, 2) }}
                                        </div>
                                        <div>
                                            <span class="font-medium">Total tutoría:</span> L. {{ number_format($form['matricula_info']->precio_total, 2) }}
                                        </div>
                                        <div class="col-span-2">
                                            <span class="font-medium">Horas:</span> 
                                            <span class="text-emerald-600 dark:text-emerald-400">{{ $form['matricula_info']->horas_asistidas }} asistidas</span> | 
                                            <span class="text-red-600 dark:text-red-400">{{ $form['matricula_info']->horas_pendientes }} pendientes</span>
                                        </div>
                                        <div class="col-span-2">
                                            <span class="font-medium">Saldo pendiente:</span> 
                                            <span class="{{ $form['matricula_info']->saldo_pendiente > 0 ? 'text-red-600 dark:text-red-400 font-bold' : 'text-emerald-600 dark:text-emerald-400 font-bold' }}">
                                                L. {{ number_format($form['matricula_info']->saldo_pendiente, 2) }}
                                            </span>
                                        </div>
                                        @if($form['matricula_info']->fecha_proxima_sesion)
                                        <div class="col-span-2">
                                            <span class="font-medium">Próxima sesión:</span> 
                                            {{ \Carbon\Carbon::parse($form['matricula_info']->fecha_proxima_sesion)->format('d/m/Y') }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <x-button 
                                    type="button" 
                                    wire:click="procesarPago({{ $index }})"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-xs ml-4"
                                >
                                    Procesar este pago
                                </x-button>
                            </div>
                        </div>
                        @elseif(!$isEditing && !empty($form['matricula_tutoria_id']) && empty($form['matricula_info']))
                        <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md text-center">
                            <p class="text-yellow-600 dark:text-yellow-400 text-sm">
                                Matrícula ID: {{ $form['matricula_tutoria_id'] }} cargada
                            </p>
                            <x-button 
                                type="button" 
                                wire:click="buscarMatricula({{ $index }})"
                                class="bg-yellow-600 hover:bg-yellow-700 text-xs mt-2"
                            >
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Mostrar información
                            </x-button>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </form>
    </x-slot>

    <x-slot name="footer">
        <div class="flex justify-between w-full">
            <x-secondary-button wire:click="closeModal">
                Cancelar
            </x-secondary-button>
            
            @if($isEditing)
                <x-button wire:click="update">
                    Actualizar
                </x-button>
            @else
                <x-button wire:click="procesarTodosLosPagos" class="bg-emerald-600 hover:bg-emerald-700">
                    Procesar Todos los Pagos
                </x-button>
            @endif
        </div>
    </x-slot>
</x-dialog-modal>