<x-elegant-delete-modal 
    wire:model="showDeleteModal"
    title="Confirmar Eliminación"
    message="¿Estás seguro de que deseas eliminar esta matricula?"
    entity-name=""
    entity-details="Todos los datos de la matricula serán eliminados permanentemente"
    confirm-method="delete"
    cancel-method="closeDeleteModal"
    confirm-text="Eliminar matricula"
    cancel-text="Cancelar"
/>