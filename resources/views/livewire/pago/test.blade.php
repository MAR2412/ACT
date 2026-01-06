<x-dialog-modal wire:model="isOpen" maxWidth="3xl">
    {{-- TÍTULO --}}
    <x-slot name="title">
        <h3 class="text-lg font-semibold">
            {{ $isEditing ? 'Editar Pago' : 'Nuevo Pago' }}
        </h3>
    </x-slot>

    {{-- CONTENIDO --}}
    <x-slot name="content">
        <form class="space-y-6">

            {{-- SELECTOR DE CANTIDAD --}}
            @if(!$isEditing)
                <div class="border border-stone-200 dark:border-stone-700 rounded-md p-4">
                    <div class="flex items-center justify-between mb-4">
                        <x-label value="Número de Pagos a Ingresar:" />

                        <div class="flex items-center space-x-2">
                            <x-button type="button"
                                      wire:click="decrementarPagos"
                                      class="bg-stone-600 hover:bg-stone-700"
                                      {{ $numeroPagos <= 1 ? 'disabled' : '' }}>
                                −
                            </x-button>

                            <x-input type="number"
                                     wire:model.live="numeroPagos"
                                     min="1"
                                     max="20"
                                     class="w-20 text-center"/>

                            <x-button type="button"
                                      wire:click="incrementarPagos"
                                      class="bg-stone-600 hover:bg-stone-700">
                                +
                            </x-button>
                        </div>
                    </div>

                    <p class="text-sm text-stone-500">
                        Puede ingresar hasta 20 pagos a la vez.
                    </p>
                </div>
            @endif

            {{-- FORMULARIOS --}}
            <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2">
                @foreach($pagosForms as $index => $form)
                    <div class="border border-stone-200 dark:border-stone-700 rounded-md p-4">

                        @if(!$isEditing)
                            <h4 class="font-medium mb-4">
                                Pago #{{ $index + 1 }}
                            </h4>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- MATRÍCULA --}}
                            <div class="md:col-span-2">
                                <x-label value="ID de Matrícula *"/>
                                <div class="flex space-x-2">
                                    <x-input type="number"
                                             wire:model.defer="pagosForms.{{ $index }}.matricula_id"
                                             class="flex-grow"/>

                                    <x-button type="button"
                                              wire:click="buscarMatricula({{ $index }})"
                                              class="bg-blue-600 hover:bg-blue-700">
                                        Buscar
                                    </x-button>
                                </div>
                                @error("pagosForms.$index.matricula_id")
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- TIPO --}}
                            <div>
                                <x-label value="Tipo de Pago *"/>
                                <select wire:model.defer="pagosForms.{{ $index }}.tipo" class="w-full rounded-md">
                                    @foreach($tipos as $tipo)
                                        <option value="{{ $tipo }}">{{ ucfirst($tipo) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- MÉTODO --}}
                            <div>
                                <x-label value="Método de Pago *"/>
                                <select wire:model.defer="pagosForms.{{ $index }}.metodo_pago" class="w-full rounded-md">
                                    @foreach($metodosPago as $metodo)
                                        <option value="{{ $metodo }}">{{ ucfirst($metodo) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- MONTO --}}
                            <div>
                                <x-label value="Monto *"/>
                                <x-input type="number" step="0.01"
                                         wire:model.defer="pagosForms.{{ $index }}.monto"/>
                            </div>

                            {{-- MONTO PAGADO --}}
                            <div>
                                <x-label value="Monto Pagado *"/>
                                <x-input type="number" step="0.01"
                                         wire:model.defer="pagosForms.{{ $index }}.monto_pagado"/>
                            </div>

                            {{-- CAMBIO --}}
                            <div>
                                <x-label value="Cambio"/>
                                <x-input type="number"
                                         readonly
                                         wire:model.defer="pagosForms.{{ $index }}.cambio"/>
                            </div>

                            {{-- MES --}}
                            <div>
                                <x-label value="Mes Pagado"/>
                                <x-input type="month"
                                         wire:model.defer="pagosForms.{{ $index }}.mes_pagado"/>
                            </div>

                            {{-- ESTADO --}}
                            <div>
                                <x-label value="Estado *"/>
                                <select wire:model.defer="pagosForms.{{ $index }}.estado" class="w-full rounded-md">
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado }}">{{ ucfirst($estado) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- FECHA --}}
                            <div>
                                <x-label value="Fecha de Pago *"/>
                                <x-input type="date"
                                         wire:model.defer="pagosForms.{{ $index }}.fecha_pago"/>
                            </div>
                        </div>

                        {{-- INFO MATRÍCULA --}}
                        @if(
                            !$isEditing &&
                            !empty($form['matricula_id']) &&
                            !empty($matricula_info[$index])
                        )
                            <div class="mt-4 p-3 bg-blue-50 border rounded-md">
                                <p class="font-semibold">
                                    {{ $matricula_info[$index]->estudiante->nombre }}
                                    {{ $matricula_info[$index]->estudiante->apellido }}
                                </p>
                                <p class="text-sm">
                                    Módulo: {{ $matricula_info[$index]->modulo->nombre }} |
                                    Saldo: L.
                                    {{ number_format($matricula_info[$index]->saldo_pendiente, 2) }}
                                </p>

                                <x-button type="button"
                                          wire:click="procesarPago({{ $index }})"
                                          class="mt-2 bg-emerald-600 hover:bg-emerald-700 text-xs">
                                    Procesar este pago
                                </x-button>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        </form>
    </x-slot>

    {{-- FOOTER --}}
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
                <x-button wire:click="procesarTodosLosPagos"
                          class="bg-emerald-600 hover:bg-emerald-700">
                    Procesar Todos los Pagos
                </x-button>
            @endif
        </div>
    </x-slot>
</x-dialog-modal>
