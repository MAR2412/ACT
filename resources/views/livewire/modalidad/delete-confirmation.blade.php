<x-elegant-delete-modal 
    wire:model="showDeleteModal"
    title="Confirmar Eliminación"
    message="¿Estás seguro de que deseas eliminar esta modalidad?"
    entity-name="{{ $nombreAEliminar }}"
    entity-details="Todos los datos de la modalidad serán eliminados permanentemente"
    confirm-method="delete"
    cancel-method="closeDeleteModal"
    confirm-text="Eliminar Modalidad"
    cancel-text="Cancelar"
/>