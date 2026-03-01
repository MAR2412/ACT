<?php

namespace App\Livewire\Modulo;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Modulo;
use App\Models\Matricula;
use App\Models\Estudiante;

class EstudiantesMatriculados extends Component
{
    use WithPagination;

    public $modulo;
    public $showModal = false;
    public $moduloId;
    public $moduloNombre;
    public $search = '';
    public $perPage = 10;
    public $estadoFiltro = '';
    public $aprobadoFiltro = '';

    protected $listeners = ['showStudents' => 'loadStudents'];

    public function loadStudents($moduloId)
    {
        $this->modulo = Modulo::with(['sede', 'modalidad', 'seccion'])->find($moduloId);
        $this->moduloId = $moduloId;
        $this->moduloNombre = $this->modulo->nombre ?? 'Módulo';
        $this->resetPage();
        $this->showModal = true;
        
        // Forzar actualización
        $this->dispatch('$refresh');
    }
    

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['search', 'estadoFiltro', 'aprobadoFiltro', 'moduloId', 'moduloNombre']);
    }

    public function getEstudiantesProperty()
    {
        if (!$this->moduloId) {
            return collect([]);
        }

        return Matricula::with(['estudiante' => function($query) {
                $query->withTrashed();
            }])
            ->where('modulo_id', $this->moduloId)
            ->when($this->search, function($query) {
                $query->whereHas('estudiante', function($q) {
                    $q->where('nombres', 'like', "%{$this->search}%")
                      ->orWhere('apellidos', 'like', "%{$this->search}%")
                      ->orWhere('identidad', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->estadoFiltro, function($query) {
                $query->where('estado', $this->estadoFiltro);
            })
            ->when($this->aprobadoFiltro !== '', function($query) {
                $query->where('aprobado', $this->aprobadoFiltro);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
    }

    public function render()
    {
        $estadosMatricula = [
            'activa' => 'Activa',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada',
            'pendiente' => 'Pendiente'
        ];

        return view('livewire.modulo.estudiantes-matriculados', [
            'estudiantes' => $this->estudiantes,
            'totalMatriculados' => Matricula::where('modulo_id', $this->moduloId)->count(),
            'totalActivos' => Matricula::where('modulo_id', $this->moduloId)
                ->where('estado', 'activa')
                ->count(),
            'totalAprobados' => Matricula::where('modulo_id', $this->moduloId)
                ->where('aprobado', true)
                ->count(),
            'estadosMatricula' => $estadosMatricula
        ])->layout('layouts.app');
    }
}