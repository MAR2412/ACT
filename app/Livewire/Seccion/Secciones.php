<?php

namespace App\Livewire\Seccion;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Seccion;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;

class Secciones extends Component
{
    use WithPagination;
    
    public $isEditing = false;
    public $isOpen = false;
    public $showDeleteModal = false;
    public $showErrorModal = false;

    public $nombre;
    public $HoraInicio;
    public $HoraFin;
    public $dia;
    public $diaF;
    public $estado = 1;

    public $seccion;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $IdAEliminar;
    public $nombreAEliminar;
    public $errorMessage;

    public $diasSemana = [
        '',
        'Lunes', 
        'Martes', 
        'Miércoles', 
        'Jueves', 
        'Viernes', 
        'Sábado', 
        'Domingo'
    ];

    public function mount()
    {
        $this->dia = 'Lunes';
        $this->diaF = '';
    }
    
    public function render()
    {
        $secciones = Seccion::withTrashed()
            ->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('dia', 'like', "%{$this->search}%")
                  ->orWhere('diaF', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.seccion.secciones', compact('secciones'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isOpen = true;
    }

    public function edit(Seccion $seccion)
    {
        $this->seccion = $seccion;
        $this->nombre = $seccion->nombre;
        $this->HoraInicio = $seccion->HoraInicio;
        $this->HoraFin = $seccion->HoraFin;
        $this->dia = $seccion->dia;
        $this->diaF = $seccion->diaF ?? '';
        $this->estado = $seccion->estado;
        $this->isEditing = true;
        $this->isOpen = true;
    }

    public function store()
    {
        $diasValidos = array_filter($this->diasSemana, function($dia) {
            return $dia !== '';
        });

        $this->validate([
            'nombre' => 'required|unique:secciones,nombre' . ($this->isEditing && $this->seccion ? ',' . $this->seccion->id : ''),
            'HoraInicio' => 'required',
            'HoraFin' => 'required|after:HoraInicio',
            'dia' => 'required|in:' . implode(',', $diasValidos),
            'diaF' => 'nullable|in:' . implode(',', $diasValidos),
            'estado' => 'required|boolean',
        ]);

        try {
            if ($this->isEditing) {
                $this->updateSeccion();
            } else {
                $this->createSeccion();
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    private function createSeccion()
    {
        $seccion = Seccion::create([
            'nombre' => $this->nombre,
            'HoraInicio' => $this->HoraInicio,
            'HoraFin' => $this->HoraFin,
            'dia' => $this->dia,
            'diaF' => $this->diaF ?: null,
            'estado' => $this->estado,
            'created_by' => Auth::id(),
        ]);

        LogService::activity(
            'crear',
            'Secciones',
            "Se creó la sección {$seccion->nombre}",
            [
                'Creado por' => Auth::user()->email,
                'Sección' => $seccion->nombre,
                'Horario' => "{$seccion->HoraInicio} - {$seccion->HoraFin}",
                'Día' => $seccion->dia,
                'Día Final' => $seccion->diaF ?? 'No especificado',
            ]
        );

        session()->flash('message', 'Sección creada correctamente');
        $this->closeModal();
    }

    private function updateSeccion()
    {
        $seccion = Seccion::findOrFail($this->seccion->id);

        $seccion->update([
            'nombre' => $this->nombre,
            'HoraInicio' => $this->HoraInicio,
            'HoraFin' => $this->HoraFin,
            'dia' => $this->dia,
            'diaF' => $this->diaF ?: null,
            'estado' => $this->estado,
            'updated_by' => Auth::id(),
        ]);

        LogService::activity(
            'actualizar',
            'Secciones',
            "Se actualizó la sección {$seccion->nombre}",
            [
                'Actualizado por' => Auth::user()->email,
                'Sección' => $seccion->nombre,
                'Horario' => "{$seccion->HoraInicio} - {$seccion->HoraFin}",
                'Día' => $seccion->dia,
                'Día Final' => $seccion->diaF ?? 'No especificado',
            ]
        );

        session()->flash('message', 'Sección actualizada correctamente');
        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $seccion = Seccion::findOrFail($id);
        $this->IdAEliminar = $id;
        $this->nombreAEliminar = $seccion->nombre;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $seccion = Seccion::findOrFail($this->IdAEliminar);
        $seccion->delete();

        LogService::activity(
            'eliminar',
            'Secciones',
            "Se eliminó la sección {$seccion->nombre}",
            [
                'Eliminado por' => Auth::user()->email,
                'Sección' => $seccion->nombre,
            ]
        );

        session()->flash('message', 'Sección eliminada');
        $this->closeDeleteModal();
    }

    public function restore($id)
    {
        $seccion = Seccion::withTrashed()->findOrFail($id);
        $seccion->restore();

        LogService::activity(
            'restaurar',
            'Secciones',
            "Se restauró la sección {$seccion->nombre}",
            [
                'Restaurado por' => Auth::user()->email,
                'Sección' => $seccion->nombre,
            ]
        );

        session()->flash('message', 'Sección restaurada correctamente');
    }

    public function forceDelete($id)
    {
        $seccion = Seccion::withTrashed()->findOrFail($id);
        $seccionName = $seccion->nombre;
        $seccion->forceDelete();

        LogService::activity(
            'eliminar_permanentemente',
            'Secciones',
            "Se eliminó permanentemente la sección {$seccionName}",
            [
                'Eliminado por' => Auth::user()->email,
            ]
        );

        session()->flash('message', 'Sección eliminada permanentemente');
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
            'HoraInicio',
            'HoraFin',
            'dia',
            'diaF',
            'estado',
            'seccion',
            'errorMessage',
        ]);
        $this->dia = 'Lunes';
        $this->diaF = '';
        $this->estado = 1;
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
        $seccion = Seccion::findOrFail($id);
        $seccion->estado = !$seccion->estado;
        $seccion->save();

        $accion = $seccion->estado ? 'activada' : 'desactivada';
        
        LogService::activity(
            'cambiar_estado',
            'Secciones',
            "Se {$accion} la sección {$seccion->nombre}",
            [
                'Modificado por' => Auth::user()->email,
                'Sección' => $seccion->nombre,
                'Nuevo estado' => $seccion->estado ? 'Activo' : 'Inactivo',
            ]
        );

        session()->flash('message', "Sección {$accion} correctamente");
    }
}