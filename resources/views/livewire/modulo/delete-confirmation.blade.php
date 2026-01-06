<x-elegant-delete-modal 
    wire:model="showDeleteModal"
    title="Confirmar Eliminación"
    message="¿Estás seguro de que deseas eliminar este módulo?"
    entity-name="{{ $nombreAEliminar }}"
    entity-details="Todos los datos del módulo serán eliminados permanentemente"
    confirm-method="delete"
    cancel-method="closeDeleteModal"
    confirm-text="Eliminar Módulo"
    cancel-text="Cancelar"
/>