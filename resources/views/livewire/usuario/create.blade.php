<x-dialog-modal wire:model="isOpen" maxWidth="md">
    <x-slot name="title">
        <h3 class="text-lg font-semibold">
            {{ $isEditing ? 'Editar Usuario' : 'Nuevo Usuario' }}
        </h3>
    </x-slot>

    <x-slot name="content">
        <form class="space-y-4">
            <div>
                <x-label value="Nombre" />
                <x-input wire:model.defer="name" class="w-full" />
            </div>

            <div>
                <x-label value="Correo electrónico" />
                <x-input wire:model.defer="email" type="email" class="w-full" />
            </div>

            <div>
                <x-label value="Contraseña" />
                <x-input wire:model.defer="password" type="password" class="w-full" />
            </div>

            <div>
                <x-label value="Confirmar contraseña" />
                <x-input wire:model.defer="password_confirmation" type="password" class="w-full" />
            </div>

            <div>
                <x-label value="Roles" />
                <div class="border rounded-md p-3 space-y-2 max-h-56 overflow-y-auto">
                    @foreach($roles as $role)
                        <label class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                value="{{ $role->id }}"
                                wire:model.defer="selectedRoles"
                                class="rounded text-emerald-600"
                            >
                            {{ $role->name }}
                        </label>
                    @endforeach
                </div>
            </div>
        </form>
    </x-slot>

    <x-slot name="footer">
        <x-secondary-button wire:click="closeModal">
            Cancelar
        </x-secondary-button>

        <x-button wire:click="store">
            {{ $isEditing ? 'Actualizar' : 'Crear' }}
        </x-button>
    </x-slot>
</x-dialog-modal>
