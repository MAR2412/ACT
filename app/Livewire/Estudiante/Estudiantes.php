<?php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Estudiante;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;

class Estudiantes extends Component
{
    use WithPagination;
    
    public $isEditing = false;
    public $isOpen = false;
    public $showDeleteModal = false;
    public $showErrorModal = false;

    public $nombre;
    public $apellido;
    public $dni;
    public $sexo = 'M';
    public $fecha_nacimiento;
    public $telefono;
    public $email;
    public $direccion;
    public $nombre_tutor;
    public $telefono_tutor;
    public $email_tutor;
    public $estado = true;

    public $estudiante;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $IdAEliminar;
    public $nombreAEliminar;
    public $errorMessage;

    // Opciones para el select de sexo
    public $opcionesSexo = [
        'M' => 'Masculino',
        'F' => 'Femenino'
    ];

    public function mount()
    {
        // No hay valores por defecto específicos
    }
    
    public function render()
    {
        $estudiantes = Estudiante::withTrashed()
            ->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('apellido', 'like', "%{$this->search}%")
                  ->orWhere('dni', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('nombre_tutor', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.estudiante.estudiantes', compact('estudiantes'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isOpen = true;
    }

    public function edit(Estudiante $estudiante)
    {
        $this->estudiante = $estudiante;
        $this->nombre = $estudiante->nombre;
        $this->apellido = $estudiante->apellido;
        $this->dni = $estudiante->dni;
        $this->sexo = $estudiante->sexo;
        $this->fecha_nacimiento = $estudiante->fecha_nacimiento ? $estudiante->fecha_nacimiento->format('Y-m-d') : '';
        $this->telefono = $estudiante->telefono;
        $this->email = $estudiante->email;
        $this->direccion = $estudiante->direccion;
        $this->nombre_tutor = $estudiante->nombre_tutor;
        $this->telefono_tutor = $estudiante->telefono_tutor;
        $this->email_tutor = $estudiante->email_tutor;
        $this->estado = $estudiante->estado;
        $this->isEditing = true;
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'dni' => 'required|digits_between:8,15|unique:estudiantes,dni' . ($this->isEditing && $this->estudiante ? ',' . $this->estudiante->id : ''),
            'sexo' => 'required|in:M,F',
            'fecha_nacimiento' => 'nullable|date|before_or_equal:today',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'nombre_tutor' => 'nullable|string|max:100',
            'telefono_tutor' => 'nullable|string|max:20',
            'email_tutor' => 'nullable|email|max:100',
            'estado' => 'required|boolean',
        ]);

        try {
            if ($this->isEditing) {
                $this->updateEstudiante();
            } else {
                $this->createEstudiante();
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    private function createEstudiante()
    {
        $estudiante = Estudiante::create([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'dni' => $this->dni,
            'sexo' => $this->sexo,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'nombre_tutor' => $this->nombre_tutor,
            'telefono_tutor' => $this->telefono_tutor,
            'email_tutor' => $this->email_tutor,
            'estado' => $this->estado,
            'created_by' => Auth::id(),
        ]);

        LogService::activity(
            'crear',
            'Estudiantes',
            "Se creó el estudiante {$estudiante->nombre_completo}",
            [
                'Creado por' => Auth::user()->email,
                'Estudiante' => $estudiante->nombre_completo,
                'DNI' => $estudiante->dni,
                'Edad' => $estudiante->edad ? $estudiante->edad . ' años' : 'No especificada',
                'Sexo' => $estudiante->sexo_formateado,
            ]
        );

        session()->flash('message', 'Estudiante creado correctamente');
        $this->closeModal();
    }

    private function updateEstudiante()
    {
        $estudiante = Estudiante::findOrFail($this->estudiante->id);

        $estudiante->update([
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'dni' => $this->dni,
            'sexo' => $this->sexo,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'nombre_tutor' => $this->nombre_tutor,
            'telefono_tutor' => $this->telefono_tutor,
            'email_tutor' => $this->email_tutor,
            'estado' => $this->estado,
            'updated_by' => Auth::id(),
        ]);

        LogService::activity(
            'actualizar',
            'Estudiantes',
            "Se actualizó el estudiante {$estudiante->nombre_completo}",
            [
                'Actualizado por' => Auth::user()->email,
                'Estudiante' => $estudiante->nombre_completo,
                'DNI' => $estudiante->dni,
                'Edad' => $estudiante->edad ? $estudiante->edad . ' años' : 'No especificada',
                'Sexo' => $estudiante->sexo_formateado,
            ]
        );

        session()->flash('message', 'Estudiante actualizado correctamente');
        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $estudiante = Estudiante::findOrFail($id);
        $this->IdAEliminar = $id;
        $this->nombreAEliminar = $estudiante->nombre_completo;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $estudiante = Estudiante::findOrFail($this->IdAEliminar);
        $estudiante->delete();

        LogService::activity(
            'eliminar',
            'Estudiantes',
            "Se eliminó el estudiante {$estudiante->nombre_completo}",
            [
                'Eliminado por' => Auth::user()->email,
                'Estudiante' => $estudiante->nombre_completo,
                'DNI' => $estudiante->dni,
            ]
        );

        session()->flash('message', 'Estudiante eliminado');
        $this->closeDeleteModal();
    }

    public function restore($id)
    {
        $estudiante = Estudiante::withTrashed()->findOrFail($id);
        $estudiante->restore();

        LogService::activity(
            'restaurar',
            'Estudiantes',
            "Se restauró el estudiante {$estudiante->nombre_completo}",
            [
                'Restaurado por' => Auth::user()->email,
                'Estudiante' => $estudiante->nombre_completo,
                'DNI' => $estudiante->dni,
            ]
        );

        session()->flash('message', 'Estudiante restaurado correctamente');
    }

    public function forceDelete($id)
    {
        $estudiante = Estudiante::withTrashed()->findOrFail($id);
        $estudianteName = $estudiante->nombre_completo;
        $estudiante->forceDelete();

        LogService::activity(
            'eliminar_permanentemente',
            'Estudiantes',
            "Se eliminó permanentemente el estudiante {$estudianteName}",
            [
                'Eliminado por' => Auth::user()->email,
            ]
        );

        session()->flash('message', 'Estudiante eliminado permanentemente');
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
            'apellido',
            'dni',
            'sexo',
            'fecha_nacimiento',
            'telefono',
            'email',
            'direccion',
            'nombre_tutor',
            'telefono_tutor',
            'email_tutor',
            'estado',
            'estudiante',
            'errorMessage',
        ]);
        $this->sexo = 'M';
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
        $estudiante = Estudiante::findOrFail($id);
        $estudiante->estado = !$estudiante->estado;
        $estudiante->save();

        $accion = $estudiante->estado ? 'activado' : 'desactivado';
        
        LogService::activity(
            'cambiar_estado',
            'Estudiantes',
            "Se {$accion} el estudiante {$estudiante->nombre_completo}",
            [
                'Modificado por' => Auth::user()->email,
                'Estudiante' => $estudiante->nombre_completo,
                'Nuevo estado' => $estudiante->estado ? 'Activo' : 'Inactivo',
            ]
        );

        session()->flash('message', "Estudiante {$accion} correctamente");
    }
     public function hideError()
    {
        $this->showErrorModal = false;
    }
}