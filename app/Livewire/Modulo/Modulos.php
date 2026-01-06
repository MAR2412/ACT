<?php

namespace App\Livewire\Modulo;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Modulo;
use App\Models\Sede;
use App\Models\Modalidad;
use App\Models\Seccion;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;

class Modulos extends Component
{
    use WithPagination;
    
    public $isEditing = false;
    public $isOpen = false;
    public $showDeleteModal = false;
    public $showErrorModal = false;

    public $nombre;
    public $codigo;
    public $descripcion;
    public $duracion_meses;
    public $precio_mensual;
    public $fecha_inicio;
    public $fecha_fin;
    public $nivel = 'I';
    public $orden = 1;
    public $modulo_requerido_id;
    public $es_ultimo_modulo = false;
    public $estado = true;
    public $sede_id;
    public $modalidad_id;
    public $seccion_id;

    public $modulo;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $IdAEliminar;
    public $nombreAEliminar;
    public $errorMessage;

    public $sedes;
    public $modalidades;
    public $secciones;
    public $modulosDisponibles;
    public $niveles = ['I', 'II', 'III', 'IV', 'V', 'VI'];

    public function mount()
    {
        $this->duracion_meses = 1;
        $this->precio_mensual = 0;
        $this->fecha_inicio = date('Y-m-d');
        $this->cargarDatosFiltros();
    }
    
    public function render()
    {
        $modulos = Modulo::with(['sede', 'modalidad', 'seccion', 'moduloRequerido'])
            ->withTrashed()
            ->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('codigo', 'like', "%{$this->search}%")
                  ->orWhere('descripcion', 'like', "%{$this->search}%")
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

        return view('livewire.modulo.modulos', compact('modulos'))
            ->layout('layouts.app');
    }

    private function cargarDatosFiltros()
    {
        $this->sedes = Sede::where('estado', true)->get();
        $this->modalidades = Modalidad::where('estado', true)->get();
        $this->secciones = Seccion::where('estado', true)->get();
        $this->modulosDisponibles = Modulo::where('estado', true)->get();
    }
    public function hideError()
    {
        $this->showErrorModal = false;
    }
    public function create()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isOpen = true;
        $this->cargarDatosFiltros();
    }

    public function edit(Modulo $modulo)
    {
        $this->modulo = $modulo;
        $this->nombre = $modulo->nombre;
        $this->codigo = $modulo->codigo;
        $this->descripcion = $modulo->descripcion;
        $this->duracion_meses = $modulo->duracion_meses;
        $this->precio_mensual = $modulo->precio_mensual;
        $this->fecha_inicio = $modulo->fecha_inicio ? $modulo->fecha_inicio->format('Y-m-d') : '';
        $this->fecha_fin = $modulo->fecha_fin ? $modulo->fecha_fin->format('Y-m-d') : '';
        $this->nivel = $modulo->nivel;
        $this->orden = $modulo->orden;
        $this->modulo_requerido_id = $modulo->modulo_requerido_id;
        $this->es_ultimo_modulo = $modulo->es_ultimo_modulo;
        $this->estado = $modulo->estado;
        $this->sede_id = $modulo->sede_id;
        $this->modalidad_id = $modulo->modalidad_id;
        $this->seccion_id = $modulo->seccion_id;
        $this->isEditing = true;
        $this->isOpen = true;
        $this->cargarDatosFiltros();
    }

    public function store()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'nullable|string|max:50',
            'descripcion' => 'nullable|string|max:500',
            'duracion_meses' => 'required|integer|min:1',
            'precio_mensual' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'nivel' => 'required|in:I,II,III,IV,V,VI',
            'orden' => 'required|integer|min:1',
            'modulo_requerido_id' => 'nullable|exists:modulos,id',
            'es_ultimo_modulo' => 'boolean',
            'estado' => 'required|boolean',
            'sede_id' => 'required|exists:sedes,id',
            'modalidad_id' => 'required|exists:modalidades,id',
            'seccion_id' => 'required|exists:secciones,id',
        ]);

        if ($this->modulo_requerido_id && $this->modulo_requerido_id == optional($this->modulo)->id) {
            throw new \Exception('Un módulo no puede ser requerido por sí mismo.');
        }

        $uniqueRule = 'unique:modulos,nombre,NULL,id,sede_id,' . $this->sede_id . 
                     ',modalidad_id,' . $this->modalidad_id . ',seccion_id,' . $this->seccion_id;
        
        if ($this->isEditing && $this->modulo) {
            $uniqueRule = 'unique:modulos,nombre,' . $this->modulo->id . 
                         ',id,sede_id,' . $this->sede_id . 
                         ',modalidad_id,' . $this->modalidad_id . 
                         ',seccion_id,' . $this->seccion_id;
        }

        $this->validate(['nombre' => $uniqueRule]);

        try {
            if ($this->isEditing) {
                $this->updateModulo();
            } else {
                $this->createModulo();
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    private function createModulo()
    {
        $fechaFin = $this->fecha_fin ?: date('Y-m-d', strtotime($this->fecha_inicio . " +{$this->duracion_meses} months"));

        $modulo = Modulo::create([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'descripcion' => $this->descripcion,
            'duracion_meses' => $this->duracion_meses,
            'precio_mensual' => $this->precio_mensual,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $fechaFin,
            'nivel' => $this->nivel,
            'orden' => $this->orden,
            'modulo_requerido_id' => $this->modulo_requerido_id,
            'es_ultimo_modulo' => $this->es_ultimo_modulo,
            'estado' => $this->estado,
            'sede_id' => $this->sede_id,
            'modalidad_id' => $this->modalidad_id,
            'seccion_id' => $this->seccion_id,
            'created_by' => Auth::id(),
        ]);

        LogService::activity(
            'crear',
            'Módulos',
            "Se creó el módulo {$modulo->nombre}",
            [
                'Creado por' => Auth::user()->email,
                'Módulo' => $modulo->nombre,
                'Código' => $modulo->codigo ?? 'N/A',
                'Nivel' => $modulo->nivel,
                'Sede' => $modulo->sede->nombre,
                'Modalidad' => $modulo->modalidad->nombre,
                'Sección' => $modulo->seccion->nombre,
                'Duración' => $modulo->duracion_meses . ' meses',
                'Precio mensual' => 'L. ' . number_format($modulo->precio_mensual, 2),
                'Precio total' => 'L. ' . number_format($modulo->precio_mensual * $modulo->duracion_meses, 2),
            ]
        );

        session()->flash('message', 'Módulo creado correctamente');
        $this->closeModal();
    }

    private function updateModulo()
    {
        $modulo = Modulo::findOrFail($this->modulo->id);

        $fechaFin = $this->fecha_fin ?: date('Y-m-d', strtotime($this->fecha_inicio . " +{$this->duracion_meses} months"));

        $modulo->update([
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'descripcion' => $this->descripcion,
            'duracion_meses' => $this->duracion_meses,
            'precio_mensual' => $this->precio_mensual,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $fechaFin,
            'nivel' => $this->nivel,
            'orden' => $this->orden,
            'modulo_requerido_id' => $this->modulo_requerido_id,
            'es_ultimo_modulo' => $this->es_ultimo_modulo,
            'estado' => $this->estado,
            'sede_id' => $this->sede_id,
            'modalidad_id' => $this->modalidad_id,
            'seccion_id' => $this->seccion_id,
            'updated_by' => Auth::id(),
        ]);

        LogService::activity(
            'actualizar',
            'Módulos',
            "Se actualizó el módulo {$modulo->nombre}",
            [
                'Actualizado por' => Auth::user()->email,
                'Módulo' => $modulo->nombre,
                'Código' => $modulo->codigo ?? 'N/A',
                'Nivel' => $modulo->nivel,
                'Sede' => $modulo->sede->nombre,
                'Modalidad' => $modulo->modalidad->nombre,
                'Sección' => $modulo->seccion->nombre,
                'Duración' => $modulo->duracion_meses . ' meses',
                'Precio mensual' => 'L. ' . number_format($modulo->precio_mensual, 2),
                'Precio total' => 'L. ' . number_format($modulo->precio_mensual * $modulo->duracion_meses, 2),
            ]
        );

        session()->flash('message', 'Módulo actualizado correctamente');
        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $modulo = Modulo::findOrFail($id);
        $this->IdAEliminar = $id;
        $this->nombreAEliminar = $modulo->nombre;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $modulo = Modulo::findOrFail($this->IdAEliminar);
        
        if ($modulo->matriculas()->exists()) {
            $this->errorMessage = 'No se puede eliminar el módulo porque tiene matrículas asociadas.';
            $this->showErrorModal = true;
            return;
        }

        $modulo->delete();

        LogService::activity(
            'eliminar',
            'Módulos',
            "Se eliminó el módulo {$modulo->nombre}",
            [
                'Eliminado por' => Auth::user()->email,
                'Módulo' => $modulo->nombre,
            ]
        );

        session()->flash('message', 'Módulo eliminado');
        $this->closeDeleteModal();
    }

    public function restore($id)
    {
        $modulo = Modulo::withTrashed()->findOrFail($id);
        $modulo->restore();

        LogService::activity(
            'restaurar',
            'Módulos',
            "Se restauró el módulo {$modulo->nombre}",
            [
                'Restaurado por' => Auth::user()->email,
                'Módulo' => $modulo->nombre,
            ]
        );

        session()->flash('message', 'Módulo restaurado correctamente');
    }

    public function forceDelete($id)
    {
        $modulo = Modulo::withTrashed()->findOrFail($id);
        
        if ($modulo->matriculas()->exists()) {
            $this->errorMessage = 'No se puede eliminar permanentemente el módulo porque tiene matrículas asociadas.';
            $this->showErrorModal = true;
            return;
        }

        $moduloName = $modulo->nombre;
        $modulo->forceDelete();

        LogService::activity(
            'eliminar_permanentemente',
            'Módulos',
            "Se eliminó permanentemente el módulo {$moduloName}",
            [
                'Eliminado por' => Auth::user()->email,
            ]
        );

        session()->flash('message', 'Módulo eliminado permanentemente');
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
            'codigo',
            'descripcion',
            'duracion_meses',
            'precio_mensual',
            'fecha_inicio',
            'fecha_fin',
            'nivel',
            'orden',
            'modulo_requerido_id',
            'es_ultimo_modulo',
            'estado',
            'sede_id',
            'modalidad_id',
            'seccion_id',
            'modulo',
            'errorMessage',
        ]);
        $this->duracion_meses = 1;
        $this->precio_mensual = 0;
        $this->fecha_inicio = date('Y-m-d');
        $this->nivel = 'I';
        $this->orden = 1;
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
        $modulo = Modulo::findOrFail($id);
        $modulo->estado = !$modulo->estado;
        $modulo->save();

        $accion = $modulo->estado ? 'activado' : 'desactivado';
        
        LogService::activity(
            'cambiar_estado',
            'Módulos',
            "Se {$accion} el módulo {$modulo->nombre}",
            [
                'Modificado por' => Auth::user()->email,
                'Módulo' => $modulo->nombre,
                'Nuevo estado' => $modulo->estado ? 'Activo' : 'Inactivo',
            ]
        );

        session()->flash('message', "Módulo {$accion} correctamente");
    }
}