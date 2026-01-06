<x-elegant-delete-modal 
    wire:model="showDeleteModal"
    title="Confirmar Eliminación"
    message="¿Estás seguro de que deseas eliminar este estudiante?"
    entity-name="{{ $nombreAEliminar }}"
    entity-details="Todos los datos del estudiante serán eliminados permanentemente"
    confirm-method="delete"
    cancel-method="closeDeleteModal"
    confirm-text="Eliminar Estudiante"
    cancel-text="Cancelar"
/>