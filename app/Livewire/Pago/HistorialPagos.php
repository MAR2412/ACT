<?php

namespace App\Livewire\Pago;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Estudiante;
use App\Models\Pago;
use App\Models\Matricula;
use App\Models\Modulo;
use Carbon\Carbon;

class HistorialPagos extends Component
{
    use WithPagination;
    
    public $tipo_busqueda = 'estudiante';
    public $dni_busqueda = '';
    public $modulo_busqueda = '';
    public $estudiante_info = null;
    public $modulo_info = null;
    public $matriculas_estudiante = [];
    public $matricula_seleccionada = null;
    public $resumen_pagos = null;
    public $modulos_disponibles = [];
    
    public function mount()
    {
        $this->modulos_disponibles = Modulo::where('estado', true)
            ->with(['sede', 'modalidad'])
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'duracion_meses', 'precio_mensual', 'sede_id', 'modalidad_id']);
    }
    
    public function buscar()
    {
        if ($this->tipo_busqueda == 'estudiante') {
            $this->buscarEstudiante();
        } else {
            $this->buscarModulo();
        }
    }
    
    public function buscarEstudiante()
    {
        $this->validate([
            'dni_busqueda' => 'required|string|max:20'
        ]);
        
        $estudiante = Estudiante::where('dni', $this->dni_busqueda)
            ->where('estado', true)
            ->first();
        
        if ($estudiante) {
            $this->estudiante_info = $estudiante;
            $this->matriculas_estudiante = $this->obtenerMatriculasEstudiante($estudiante->id);
            $this->modulo_info = null;
            $this->matricula_seleccionada = null;
            $this->resumen_pagos = null;
        } else {
            $this->estudiante_info = null;
            $this->matriculas_estudiante = [];
            $this->matricula_seleccionada = null;
            $this->resumen_pagos = null;
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "Estudiante con DNI {$this->dni_busqueda} no encontrado o está inactivo."
            ]);
        }
    }
    
    public function buscarModulo()
    {
        $this->validate([
            'modulo_busqueda' => 'required|exists:modulos,id'
        ]);
        
        $modulo = Modulo::with(['sede', 'modalidad'])->find($this->modulo_busqueda);
        
        if ($modulo) {
            $this->modulo_info = $modulo;
            $this->matriculas_estudiante = $this->obtenerMatriculasModulo($modulo->id);
            $this->estudiante_info = null;
            $this->matricula_seleccionada = null;
            $this->resumen_pagos = null;
        } else {
            $this->modulo_info = null;
            $this->matriculas_estudiante = [];
            $this->matricula_seleccionada = null;
            $this->resumen_pagos = null;
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "Módulo no encontrado."
            ]);
        }
    }
    
    private function obtenerMatriculasEstudiante($estudianteId)
    {
        return Matricula::with(['modulo.sede', 'modulo.modalidad'])
            ->where('estudiante_id', $estudianteId)
            ->where(function($query) {
                $query->whereNull('deleted_at')
                    ->orWhere('estado', 'completada');
            })
            ->orderBy('fecha_matricula', 'desc')
            ->get()
            ->map(function($matricula) {
                $matricula->pagos_count = Pago::where('matricula_id', $matricula->id)
                    ->where('estado', 'completado')
                    ->count();
                $matricula->total_pagado = Pago::where('matricula_id', $matricula->id)
                    ->where('estado', 'completado')
                    ->sum('monto');
                return $matricula;
            });
    }
    
    private function obtenerMatriculasModulo($moduloId)
    {
        return Matricula::with(['estudiante', 'modulo.sede', 'modulo.modalidad'])
            ->where('modulo_id', $moduloId)
            ->where(function($query) {
                $query->whereNull('deleted_at')
                    ->orWhere('estado', 'completada');
            })
            ->orderBy('fecha_matricula', 'desc')
            ->get()
            ->map(function($matricula) {
                $matricula->pagos_count = Pago::where('matricula_id', $matricula->id)
                    ->where('estado', 'completado')
                    ->count();
                $matricula->total_pagado = Pago::where('matricula_id', $matricula->id)
                    ->where('estado', 'completado')
                    ->sum('monto');
                return $matricula;
            });
    }
    
    public function seleccionarMatricula($matriculaId)
    {
        $this->matricula_seleccionada = Matricula::with(['modulo.sede', 'modulo.modalidad', 'estudiante'])->find($matriculaId);
        $this->resumen_pagos = $this->obtenerResumenPagos($matriculaId);
    }
    
    private function obtenerResumenPagos($matriculaId)
    {
        $matricula = Matricula::with(['modulo'])->find($matriculaId);
        
        $pagos = Pago::where('matricula_id', $matriculaId)
            ->orderBy('fecha_pago', 'asc')
            ->get();
        
        $pagosMensuales = $pagos->where('tipo', 'mensualidad')
            ->where('estado', 'completado')
            ->sortBy('mes_pagado')
            ->values();
        
        $otrosPagos = $pagos->where('tipo', '!=', 'mensualidad')
            ->sortBy('fecha_pago')
            ->values();
        
        $mesesPagados = $pagosMensuales->pluck('mes_pagado')->unique()->toArray();
        $mesesFormateados = array_map(function($mes) {
            return Carbon::createFromFormat('Y-m', $mes)->format('M Y');
        }, $mesesPagados);
        
        $totalHoras = 0;
        $horasAsistidas = 0;
        
        // Si necesitas lógica de horas, agregar aquí
        // Ejemplo: $horasAsistidas = $pagosMensuales->count() * 20; // 20 horas por mes
        
        $porcentajeHoras = $totalHoras > 0 ? round(($horasAsistidas / $totalHoras) * 100, 2) : 0;
        
        return [
            'matricula' => $matricula,
            'pagos_totales' => $pagos->count(),
            'pagos_completados' => $pagos->where('estado', 'completado')->count(),
            'pagos_pendientes' => $pagos->where('estado', 'pendiente')->count(),
            'pagos_anulados' => $pagos->where('estado', 'anulado')->count(),
            'total_pagado' => $pagos->where('estado', 'completado')->sum('monto'),
            'saldo_pendiente' => $matricula->saldo_pendiente,
            'pagos_mensuales' => $pagosMensuales,
            'otros_pagos' => $otrosPagos,
            'meses_pagados' => $mesesFormateados,
            'porcentaje_completado' => $matricula->modulo->duracion_meses > 0 
                ? round(($matricula->meses_pagados / $matricula->modulo->duracion_meses) * 100, 2)
                : 0,
            'horas_asistidas' => $horasAsistidas,
            'horas_pendientes' => max(0, $totalHoras - $horasAsistidas),
            'porcentaje_horas' => $porcentajeHoras,
            'estadisticas_metodos' => $pagos->where('estado', 'completado')
                ->groupBy('metodo_pago')
                ->map(function($pagos) {
                    return [
                        'count' => $pagos->count(),
                        'total' => $pagos->sum('monto')
                    ];
                }),
            'estadisticas_tipos' => $pagos->where('estado', 'completado')
                ->groupBy('tipo')
                ->map(function($pagos) {
                    return [
                        'count' => $pagos->count(),
                        'total' => $pagos->sum('monto')
                    ];
                })
        ];
    }
    
    public function limpiarBusqueda()
    {
        $this->reset([
            'tipo_busqueda',
            'dni_busqueda',
            'modulo_busqueda',
            'estudiante_info',
            'modulo_info',
            'matriculas_estudiante',
            'matricula_seleccionada',
            'resumen_pagos'
        ]);
    }
    
    public function render()
    {
        return view('livewire.pago.historial-pagos');
    }
}