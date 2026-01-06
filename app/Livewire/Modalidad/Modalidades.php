<?php

namespace App\Livewire\Modalidad;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Modalidad;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;

class Modalidades extends Component
{
    use WithPagination;
     public function hideError()
    {
        $this->showErrorModal = false;
    }
    
    public $isEditing = false;
    public $isOpen = false;
    public $showDeleteModal = false;
    public $showErrorModal = false;

    public $nombre;
    public $descripcion;
    public $estado = true;

    public $modalidad;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $IdAEliminar;
    public $nombreAEliminar;
    public $errorMessage;

    public function render()
    {
        $modalidades = Modalidad::withTrashed()
            ->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('descripcion', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.modalidad.modalidades', compact('modalidades'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isOpen = true;
    }

    public function edit(Modalidad $modalidad)
    {
        $this->modalidad = $modalidad;
        $this->nombre = $modalidad->nombre;
        $this->descripcion = $modalidad->descripcion;
        $this->estado = $modalidad->estado;
        $this->isEditing = true;
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate([
            'nombre' => 'required|unique:modalidades,nombre' . ($this->isEditing && $this->modalidad ? ',' . $this->modalidad->id : ''),
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'required|boolean',
        ]);

        try {
            if ($this->isEditing) {
                $this->updateModalidad();
            } else {
                $this->createModalidad();
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    private function createModalidad()
    {
        $modalidad = Modalidad::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'created_by' => Auth::id(),
        ]);

        LogService::activity(
            'crear',
            'Modalidades',
            "Se creó la modalidad {$modalidad->nombre}",
            [
                'Creado por' => Auth::user()->email,
                'Modalidad' => $modalidad->nombre,
                'Descripción' => $modalidad->descripcion ?? 'Sin descripción',
            ]
        );

        session()->flash('message', 'Modalidad creada correctamente');
        $this->closeModal();
    }

    private function updateModalidad()
    {
        $modalidad = Modalidad::findOrFail($this->modalidad->id);

        $modalidad->update([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'updated_by' => Auth::id(),
        ]);

        LogService::activity(
            'actualizar',
            'Modalidades',
            "Se actualizó la modalidad {$modalidad->nombre}",
            [
                'Actualizado por' => Auth::user()->email,
                'Modalidad' => $modalidad->nombre,
                'Descripción' => $modalidad->descripcion ?? 'Sin descripción',
            ]
        );

        session()->flash('message', 'Modalidad actualizada correctamente');
        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $modalidad = Modalidad::findOrFail($id);
        $this->IdAEliminar = $id;
        $this->nombreAEliminar = $modalidad->nombre;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $modalidad = Modalidad::findOrFail($this->IdAEliminar);
        $modalidad->delete();

        LogService::activity(
            'eliminar',
            'Modalidades',
            "Se eliminó la modalidad {$modalidad->nombre}",
            [
                'Eliminado por' => Auth::user()->email,
                'Modalidad' => $modalidad->nombre,
            ]
        );

        session()->flash('message', 'Modalidad eliminada');
        $this->closeDeleteModal();
    }

    public function restore($id)
    {
        $modalidad = Modalidad::withTrashed()->findOrFail($id);
        $modalidad->restore();

        LogService::activity(
            'restaurar',
            'Modalidades',
            "Se restauró la modalidad {$modalidad->nombre}",
            [
                'Restaurado por' => Auth::user()->email,
                'Modalidad' => $modalidad->nombre,
            ]
        );

        session()->flash('message', 'Modalidad restaurada correctamente');
    }

    public function forceDelete($id)
    {
        $modalidad = Modalidad::withTrashed()->findOrFail($id);
        $modalidadName = $modalidad->nombre;
        $modalidad->forceDelete();

        LogService::activity(
            'eliminar_permanentemente',
            'Modalidades',
            "Se eliminó permanentemente la modalidad {$modalidadName}",
            [
                'Eliminado por' => Auth::user()->email,
            ]
        );

        session()->flash('message', 'Modalidad eliminada permanentemente');
    }

    public function closeModal()
    {
        $this->resetInputFields();
        $this->isOpen = false;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->IdAEliminar = null;
        $this->nombreAEliminar = null;
    }

    private function resetInputFields()
    {
        $this->reset([
            'nombre',
            'descripcion',
            'estado',
            'modalidad',
            'errorMessage',
        ]);
        $this->estado = true;
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';

        $this->sortField = $field;
    }

    public function toggleEstado($id)
    {
        $modalidad = Modalidad::findOrFail($id);
        $modalidad->estado = !$modalidad->estado;
        $modalidad->save();

        $accion = $modalidad->estado ? 'activada' : 'desactivada';
        
        LogService::activity(
            'cambiar_estado',
            'Modalidades',
            "Se {$accion} la modalidad {$modalidad->nombre}",
            [
                'Modificado por' => Auth::user()->email,
                'Modalidad' => $modalidad->nombre,
                'Nuevo estado' => $modalidad->estado ? 'Activo' : 'Inactivo',
            ]
        );

        session()->flash('message', "Modalidad {$accion} correctamente");
    }
}