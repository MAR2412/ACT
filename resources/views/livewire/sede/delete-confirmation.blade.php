<x-elegant-delete-modal 
    wire:model="showDeleteModal"
    title="Confirmar Eliminación"
    message="¿Estás seguro de que deseas eliminar esta sede?"
    entity-name="{{ $nombreAEliminar }}"
    entity-details="Todos los datos de esta sede serán eliminados permanentemente"
    confirm-method="delete"
    cancel-method="closeDeleteModal"
    confirm-text="Eliminar Sede"
    cancel-text="Cancelar"
/>