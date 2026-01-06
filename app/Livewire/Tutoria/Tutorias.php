<?php

namespace App\Livewire\Tutoria;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tutoria;
use App\Models\Sede;
use App\Models\Modalidad;
use App\Models\Seccion;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;

class Tutorias extends Component
{
    use WithPagination;
    
    public $isEditing = false;
    public $isOpen = false;
    public $showDeleteModal = false;
    public $showErrorModal = false;

    public $nombre;
    public $descripcion;
    public $materia;
    public $precio_hora;
    public $estado = true;
    public $sede_id;
    public $modalidad_id;
    public $seccion_id;

    public $tutoria;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $IdAEliminar;
    public $nombreAEliminar;
    public $errorMessage;

    // Para filtros
    public $sedes;
    public $modalidades;
    public $secciones;

    public function mount()
    {
        $this->precio_hora = 0;
        $this->cargarDatosFiltros();
    }
    
    public function render()
    {
        $tutorias = Tutoria::with(['sede', 'modalidad', 'seccion'])
            ->withTrashed()
            ->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('descripcion', 'like', "%{$this->search}%")
                  ->orWhere('materia', 'like', "%{$this->search}%")
                  ->orWhereHas('sede', function ($query) {
                      $query->where('nombre', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('modalidad', function ($query) {
                      $query->where('nombre', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('seccion', function ($query) {
                      $query->where('nombre', 'like', "%{$this->search}%");
                  });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.tutoria.tutorias', compact('tutorias'))
            ->layout('layouts.app');
    }

    private function cargarDatosFiltros()
    {
        $this->sedes = Sede::where('estado', true)->get();
        $this->modalidades = Modalidad::where('estado', true)->get();
        $this->secciones = Seccion::where('estado', true)->get();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isOpen = true;
        $this->cargarDatosFiltros();
    }

    public function edit(Tutoria $tutoria)
    {
        $this->tutoria = $tutoria;
        $this->nombre = $tutoria->nombre;
        $this->descripcion = $tutoria->descripcion;
        $this->materia = $tutoria->materia;
        $this->precio_hora = $tutoria->precio_hora;
        $this->estado = $tutoria->estado;
        $this->sede_id = $tutoria->sede_id;
        $this->modalidad_id = $tutoria->modalidad_id;
        $this->seccion_id = $tutoria->seccion_id;
        $this->isEditing = true;
        $this->isOpen = true;
        $this->cargarDatosFiltros();
    }

    public function store()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'materia' => 'nullable|string|max:255',
            'precio_hora' => 'required|numeric|min:0',
            'estado' => 'required|boolean',
            'sede_id' => 'required|exists:sedes,id',
            'modalidad_id' => 'required|exists:modalidades,id',
            'seccion_id' => 'required|exists:secciones,id',
        ]);

        // Validar unicidad compuesta
        $uniqueRule = 'unique:tutorias,nombre,NULL,id,sede_id,' . $this->sede_id . 
                     ',modalidad_id,' . $this->modalidad_id . ',seccion_id,' . $this->seccion_id;
        
        if ($this->isEditing && $this->tutoria) {
            $uniqueRule = 'unique:tutorias,nombre,' . $this->tutoria->id . 
                         ',id,sede_id,' . $this->sede_id . 
                         ',modalidad_id,' . $this->modalidad_id . 
                         ',seccion_id,' . $this->seccion_id;
        }

        $this->validate([
            'nombre' => $uniqueRule,
        ]);

        try {
            if ($this->isEditing) {
                $this->updateTutoria();
            } else {
                $this->createTutoria();
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    private function createTutoria()
    {
        $tutoria = Tutoria::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'materia' => $this->materia,
            'precio_hora' => $this->precio_hora,
            'estado' => $this->estado,
            'sede_id' => $this->sede_id,
            'modalidad_id' => $this->modalidad_id,
            'seccion_id' => $this->seccion_id,
            'created_by' => Auth::id(),
        ]);

        LogService::activity(
            'crear',
            'Tutorías',
            "Se creó la tutoría {$tutoria->nombre}",
            [
                'Creado por' => Auth::user()->email,
                'Tutoría' => $tutoria->nombre,
                'Materia' => $tutoria->materia ?? 'No especificada',
                'Sede' => $tutoria->sede->nombre,
                'Modalidad' => $tutoria->modalidad->nombre,
                'Sección' => $tutoria->seccion->nombre,
                'Precio por hora' => 'L. ' . number_format($tutoria->precio_hora, 2),
            ]
        );

        session()->flash('message', 'Tutoría creada correctamente');
        $this->closeModal();
    }

    private function updateTutoria()
    {
        $tutoria = Tutoria::findOrFail($this->tutoria->id);

        $tutoria->update([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'materia' => $this->materia,
            'precio_hora' => $this->precio_hora,
            'estado' => $this->estado,
            'sede_id' => $this->sede_id,
            'modalidad_id' => $this->modalidad_id,
            'seccion_id' => $this->seccion_id,
            'updated_by' => Auth::id(),
        ]);

        LogService::activity(
            'actualizar',
            'Tutorías',
            "Se actualizó la tutoría {$tutoria->nombre}",
            [
                'Actualizado por' => Auth::user()->email,
                'Tutoría' => $tutoria->nombre,
                'Materia' => $tutoria->materia ?? 'No especificada',
                'Sede' => $tutoria->sede->nombre,
                'Modalidad' => $tutoria->modalidad->nombre,
                'Sección' => $tutoria->seccion->nombre,
                'Precio por hora' => 'L. ' . number_format($tutoria->precio_hora, 2),
            ]
        );

        session()->flash('message', 'Tutoría actualizada correctamente');
        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $tutoria = Tutoria::findOrFail($id);
        $this->IdAEliminar = $id;
        $this->nombreAEliminar = $tutoria->nombre;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $tutoria = Tutoria::findOrFail($this->IdAEliminar);
        $tutoria->delete();

        LogService::activity(
            'eliminar',
            'Tutorías',
            "Se eliminó la tutoría {$tutoria->nombre}",
            [
                'Eliminado por' => Auth::user()->email,
                'Tutoría' => $tutoria->nombre,
            ]
        );

        session()->flash('message', 'Tutoría eliminada');
        $this->closeDeleteModal();
    }

    public function restore($id)
    {
        $tutoria = Tutoria::withTrashed()->findOrFail($id);
        $tutoria->restore();

        LogService::activity(
            'restaurar',
            'Tutorías',
            "Se restauró la tutoría {$tutoria->nombre}",
            [
                'Restaurado por' => Auth::user()->email,
                'Tutoría' => $tutoria->nombre,
            ]
        );

        session()->flash('message', 'Tutoría restaurada correctamente');
    }

    public function forceDelete($id)
    {
        $tutoria = Tutoria::withTrashed()->findOrFail($id);
        $tutoriaName = $tutoria->nombre;
        $tutoria->forceDelete();

        LogService::activity(
            'eliminar_permanentemente',
            'Tutorías',
            "Se eliminó permanentemente la tutoría {$tutoriaName}",
            [
                'Eliminado por' => Auth::user()->email,
            ]
        );

        session()->flash('message', 'Tutoría eliminada permanentemente');
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
            'materia',
            'precio_hora',
            'estado',
            'sede_id',
            'modalidad_id',
            'seccion_id',
            'tutoria',
            'errorMessage',
        ]);
        $this->precio_hora = 0;
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
        $tutoria = Tutoria::findOrFail($id);
        $tutoria->estado = !$tutoria->estado;
        $tutoria->save();

        $accion = $tutoria->estado ? 'activada' : 'desactivada';
        
        LogService::activity(
            'cambiar_estado',
            'Tutorías',
            "Se {$accion} la tutoría {$tutoria->nombre}",
            [
                'Modificado por' => Auth::user()->email,
                'Tutoría' => $tutoria->nombre,
                'Nuevo estado' => $tutoria->estado ? 'Activo' : 'Inactivo',
            ]
        );

        session()->flash('message', "Tutoría {$accion} correctamente");
    }
}