<?php

namespace App\Livewire\Inicio\DashboardParticipante;

use App\Models\Estudiante;
use App\Models\Matricula;
use App\Models\MatriculaTutoria;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class DashboardParticipante extends Component
{
    use WithPagination;
    
    public $tab = 'activos';
    public $busqueda = '';
    
    public $estadisticas = [
        'total' => 0,
        'activos' => 0,
        'con_matriculas' => 0,
        'saldo_total' => 0,
    ];

    public $mostrarModalEliminar = false;
    public $estudianteAEliminar = null;
    public $estudianteDetalle = null;
    public $mostrarModalDetalle = false;

    public function mount()
    {
        $this->calcularEstadisticas();
    }

    public function calcularEstadisticas()
    {
        $this->estadisticas['total'] = Estudiante::count();
        $this->estadisticas['activos'] = Estudiante::where('estado', true)->count();
        
        // Estudiantes con matrículas activas
        $this->estadisticas['con_matriculas'] = Estudiante::whereHas('matriculas', function($query) {
            $query->where('estado', 'activa');
        })->orWhereHas('matriculasTutorias', function($query) {
            $query->where('estado', 'activa');
        })->count();
        
        // Saldo total pendiente
        $saldoModulos = Matricula::where('estado', 'activa')->sum('saldo_pendiente');
        $saldoTutorias = MatriculaTutoria::where('estado', 'activa')->sum('saldo_pendiente');
        $this->estadisticas['saldo_total'] = $saldoModulos + $saldoTutorias;
    }

    public function updatedBusqueda()
    {
        $this->resetPage();
    }

    public function updatedTab()
    {
        $this->resetPage();
    }

    public function getEstudiantesProperty()
    {
        return Estudiante::withCount(['matriculas', 'matriculasTutorias'])
            ->when($this->tab === 'activos', function($query) {
                return $query->where('estado', true);
            })
            ->when($this->tab === 'inactivos', function($query) {
                return $query->where('estado', false);
            })
            ->when($this->busqueda, function($query) {
                $query->where(function($q) {
                    $q->where('dni', 'like', "%{$this->busqueda}%")
                      ->orWhere('nombre', 'like', "%{$this->busqueda}%")
                      ->orWhere('apellido', 'like', "%{$this->busqueda}%")
                      ->orWhere('email', 'like', "%{$this->busqueda}%")
                      ->orWhere('telefono', 'like', "%{$this->busqueda}%");
                });
            })
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->paginate(15);
    }

    public function getMatriculasRecientesProperty()
    {
        $matriculasRecientes = collect();
        
        try {
            // Últimas matrículas de módulos
            if (class_exists(Matricula::class)) {
                $matriculasModulos = Matricula::with(['estudiante', 'modulo'])
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function($matricula) {
                        return [
                            'tipo' => 'modulo',
                            'estudiante' => $matricula->estudiante->apellido . ', ' . $matricula->estudiante->nombre,
                            'curso' => $matricula->modulo->nombre ?? 'Sin módulo',
                            'codigo' => $matricula->modulo->codigo ?? '',
                            'fecha' => $matricula->created_at,
                            'estado' => $matricula->estado,
                            'saldo' => $matricula->saldo_pendiente,
                            'estudiante_id' => $matricula->estudiante_id,
                            'id' => $matricula->id,
                        ];
                    });
                
                $matriculasRecientes = $matriculasRecientes->merge($matriculasModulos);
            }
            
            // Últimas matrículas de tutorías
            if (class_exists(MatriculaTutoria::class)) {
                $matriculasTutorias = MatriculaTutoria::with(['estudiante', 'tutoria'])
                    ->latest()
                    ->take(5)
                    ->get()
                    ->map(function($matricula) {
                        return [
                            'tipo' => 'tutoria',
                            'estudiante' => $matricula->estudiante->apellido . ', ' . $matricula->estudiante->nombre,
                            'curso' => $matricula->tutoria->nombre ?? 'Sin tutoría',
                            'materia' => $matricula->tutoria->materia ?? '',
                            'fecha' => $matricula->created_at,
                            'estado' => $matricula->estado,
                            'saldo' => $matricula->saldo_pendiente,
                            'estudiante_id' => $matricula->estudiante_id,
                            'id' => $matricula->id,
                        ];
                    });
                
                $matriculasRecientes = $matriculasRecientes->merge($matriculasTutorias);
            }
        } catch (\Exception $e) {
            // Si hay algún error con las relaciones, simplemente no mostramos las matrículas recientes
            logger()->error('Error al cargar matrículas recientes: ' . $e->getMessage());
        }
        
        return $matriculasRecientes->sortByDesc('fecha')->take(10);
    }

    public function verDetalle($estudianteId)
    {
        try {
            $this->estudianteDetalle = Estudiante::with([
                'matriculas.modulo',
                'matriculasTutorias.tutoria'
            ])->find($estudianteId);
            
            $this->mostrarModalDetalle = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar los detalles del estudiante: ' . $e->getMessage());
        }
    }

    public function confirmarEliminar($estudianteId)
    {
        $this->estudianteAEliminar = Estudiante::find($estudianteId);
        $this->mostrarModalEliminar = true;
    }

    public function eliminarEstudiante()
    {
        if ($this->estudianteAEliminar) {
            try {
                // Verificar si tiene matrículas activas
                $tieneMatriculas = $this->estudianteAEliminar->matriculas()
                    ->whereIn('estado', ['activa', 'pendiente'])
                    ->exists();
                
                $tieneTutorias = $this->estudianteAEliminar->matriculasTutorias()
                    ->whereIn('estado', ['activa', 'pendiente'])
                    ->exists();

                if ($tieneMatriculas || $tieneTutorias) {
                    session()->flash('error', 'No se puede eliminar el estudiante porque tiene matrículas activas');
                } else {
                    $this->estudianteAEliminar->delete();
                    session()->flash('success', 'Estudiante eliminado exitosamente');
                    $this->calcularEstadisticas();
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Error al eliminar el estudiante: ' . $e->getMessage());
            }
        }

        $this->mostrarModalEliminar = false;
        $this->estudianteAEliminar = null;
    }

    public function toggleEstado($estudianteId)
    {
        try {
            $estudiante = Estudiante::find($estudianteId);
            
            if ($estudiante) {
                $estudiante->estado = !$estudiante->estado;
                $estudiante->save();
                
                $this->calcularEstadisticas();
                
                $estado = $estudiante->estado ? 'activado' : 'desactivado';
                session()->flash('success', "Estudiante {$estado} exitosamente");
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cambiar el estado del estudiante: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.inicio.dashboard-participante.dashboard-participante', [
            'estudiantes' => $this->estudiantes,
            'matriculasRecientes' => $this->matriculasRecientes,
        ]);
    }
}