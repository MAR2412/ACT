<x-elegant-delete-modal 
    wire:model="showDeleteModal"
    title="Confirmar Eliminación"
    message="¿Estás seguro de que deseas eliminar este pago?"
    entity-name=""
    entity-details="Todos los datos del pago serán eliminados permanentemente"
    confirm-method="delete"
    cancel-method="closeDeleteModal"
    confirm-text="Eliminar Pago"
    cancel-text="Cancelar"
/>