<x-dialog-modal wire:model="showRegistrarTutoriaModal" maxWidth="md">
    <x-slot name="title">
        <h3 class="text-lg font-semibold">Registrar Nueva Tutoría</h3>
    </x-slot>

    <x-slot name="content">
        <form class="space-y-4">
            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded mb-4 grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-blue-600 uppercase font-bold">Precio/Hora</p>
                    <p class="font-semibold text-blue-800 dark:text-blue-300">
                        L. {{ number_format($precio_hora_aplicado, 2) }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-blue-600 uppercase font-bold">Total a pagar</p>
                    <p class="font-bold text-lg text-blue-900 dark:text-blue-200">
                        L. {{ number_format($precio_hora_aplicado * ($cantidad_horas ?: 0), 2) }}
                    </p>
                </div>
            </div>
            
            <div>
                <x-label value="¿Cuántas horas fueron? *" />
                <x-input 
                    type="number" 
                    step="0.5" 
                    min="1" 
                    wire:model.live="cantidad_horas" 
                    class="w-full text-lg font-bold"
                />
                <x-input-error for="cantidad_horas" />
            </div>

            <hr class="border-stone-200 dark:border-stone-700">

            <div>
                <label class="flex items-center space-x-2 mb-4">
                    <input type="checkbox" wire:model.live="registrarPagada" class="rounded">
                    <span class="text-sm font-medium text-stone-700 dark:text-stone-300">
                        ¿La tutoría está pagada?
                    </span>
                </label>
                
                @if($registrarPagada)
                <div class="space-y-4 border-t border-stone-200 dark:border-stone-700 pt-4">
                    <div>
                        <x-label value="Método de Pago *" />
                        <select 
                            wire:model="metodo_pago" 
                            class="w-full border-stone-300 dark:border-stone-600 dark:bg-stone-700 dark:text-white rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                        >
                            <option value="efectivo">Efectivo</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="deposito">Depósito</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    
                    <div>
                        <x-label value="Fecha de Pago *" />
                        <x-input 
                            type="date" 
                            wire:model="fecha_pago" 
                            class="w-full"
                        />
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-label value="Número de Transacción" />
                            <x-input 
                                wire:model="numero_transaccion" 
                                class="w-full"
                                placeholder="Opcional"
                            />
                        </div>
                        <div>
                            <x-label value="Referencia Bancaria" />
                            <x-input 
                                wire:model="referencia_bancaria" 
                                class="w-full"
                                placeholder="Opcional"
                            />
                        </div>
                    </div>
                </div>
                @else
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded">
                    <p class="text-sm text-yellow-700 dark:text-yellow-300">
                        <strong>Nota:</strong> Si marca la tutoría como no pagada, el monto se acumulará al saldo pendiente y deberá pagarse posteriormente desde el módulo de pagos.
                    </p>
                </div>
                @endif
            </div>
        </form>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="closeRegistrarTutoriaModal">
            Cancelar
        </x-secondary-button>

        <x-button wire:click="registrarTutoria" class="bg-emerald-600 hover:bg-emerald-700">
            Registrar Tutoría
        </x-button>
    </x-slot>
</x-dialog-modal>