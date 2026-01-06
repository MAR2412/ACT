<x-elegant-delete-modal 
    wire:model="showDeleteModal"
    title="Confirmar Eliminación"
    message="¿Estás seguro de que deseas eliminar esta sección?"
    entity-name="{{ $nombreAEliminar }}"
    entity-details="Todos los datos de la sección serán eliminados permanentemente"
    confirm-method="delete"
    cancel-method="closeDeleteModal"
    confirm-text="Eliminar Sección"
    cancel-text="Cancelar"
/>