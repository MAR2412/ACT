<?php

namespace App\Livewire\PagosTutoria;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Estudiante;
use App\Models\Tutoria;
use App\Models\PagoTutoria;
use App\Models\MatriculaTutoria;
use Carbon\Carbon;

class HistorialPagosTutorias extends Component
{
    use WithPagination;
    
    public $tipo_busqueda = 'estudiante';
    public $dni_busqueda = '';
    public $tutoria_busqueda = '';
    public $estudiante_info = null;
    public $tutoria_info = null;
    public $matriculas_tutoria = [];
    public $matricula_seleccionada = null;
    public $resumen_pagos = null;
    public $tutorias_disponibles = [];
    
    public function mount()
    {
        $this->tutorias_disponibles = Tutoria::where('estado', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'precio_hora']);
    }
    
    public function buscar()
    {
        if ($this->tipo_busqueda == 'estudiante') {
            $this->buscarEstudiante();
        } else {
            $this->buscarTutoria();
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
            $this->matriculas_tutoria = $this->obtenerMatriculasEstudiante($estudiante->id);
            $this->tutoria_info = null;
            $this->matricula_seleccionada = null;
            $this->resumen_pagos = null;
        } else {
            $this->estudiante_info = null;
            $this->matriculas_tutoria = [];
            $this->matricula_seleccionada = null;
            $this->resumen_pagos = null;
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "Estudiante con DNI {$this->dni_busqueda} no encontrado o está inactivo."
            ]);
        }
    }
    
    public function buscarTutoria()
    {
        $this->validate([
            'tutoria_busqueda' => 'required|exists:tutorias,id'
        ]);
        
        $tutoria = Tutoria::find($this->tutoria_busqueda);
        
        if ($tutoria) {
            $this->tutoria_info = $tutoria;
            $this->matriculas_tutoria = $this->obtenerMatriculasTutoria($tutoria->id);
            $this->estudiante_info = null;
            $this->matricula_seleccionada = null;
            $this->resumen_pagos = null;
        } else {
            $this->tutoria_info = null;
            $this->matriculas_tutoria = [];
            $this->matricula_seleccionada = null;
            $this->resumen_pagos = null;
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "Tutoría no encontrada."
            ]);
        }
    }
    
    private function obtenerMatriculasEstudiante($estudianteId)
    {
        return MatriculaTutoria::with(['tutoria'])
            ->where('estudiante_id', $estudianteId)
            ->where(function($query) {
                $query->whereNull('deleted_at')
                    ->orWhere('estado', 'completada');
            })
            ->orderBy('fecha_inicio', 'desc')
            ->get()
            ->map(function($matricula) {
                // Calculamos el total pagado de manera consistente
                $totalPagado = $this->calcularTotalPagado($matricula->id);
                
                $matricula->pagos_count = $this->calcularPagosCompletados($matricula->id);
                $matricula->total_pagado = $totalPagado;

                // Calculamos el costo total basado en tutorías registradas
                $totalCosto = $matricula->tutorias_registradas * $matricula->precio_hora_aplicado;
                $matricula->saldo_real = max(0, $totalCosto - $totalPagado);

                return $matricula;
            });
    }
    
    private function obtenerMatriculasTutoria($tutoriaId)
    {
        return MatriculaTutoria::with(['estudiante', 'tutoria'])
            ->where('tutoria_id', $tutoriaId)
            ->where(function($query) {
                $query->whereNull('deleted_at')
                    ->orWhere('estado', 'completada');
            })
            ->orderBy('fecha_inicio', 'desc')
            ->get()
            ->map(function($matricula) {
                // Calculamos el total pagado de manera consistente
                $totalPagado = $this->calcularTotalPagado($matricula->id);
                
                $matricula->pagos_count = $this->calcularPagosCompletados($matricula->id);
                $matricula->total_pagado = $totalPagado;

                // Calculamos el saldo real
                $totalCosto = $matricula->tutorias_registradas * $matricula->precio_hora_aplicado;
                $matricula->saldo_real = max(0, $totalCosto - $totalPagado);

                return $matricula;
            });
    }
    
    private function calcularTotalPagado($matriculaId)
    {
        return PagoTutoria::where('matricula_tutoria_id', $matriculaId)
            ->where('estado', 'completado')
            ->sum('monto');
    }
    
    private function calcularPagosCompletados($matriculaId)
    {
        return PagoTutoria::where('matricula_tutoria_id', $matriculaId)
            ->where('estado', 'completado')
            ->count();
    }
    
    public function seleccionarMatricula($matriculaId)
    {
        $this->matricula_seleccionada = MatriculaTutoria::with(['tutoria', 'estudiante'])->find($matriculaId);
        
        // Aseguramos que la matrícula seleccionada tenga los mismos cálculos
        if ($this->matricula_seleccionada) {
            $this->matricula_seleccionada->total_pagado = $this->calcularTotalPagado($matriculaId);
            $this->matricula_seleccionada->pagos_count = $this->calcularPagosCompletados($matriculaId);
            
            $totalCosto = $this->matricula_seleccionada->tutorias_registradas * $this->matricula_seleccionada->precio_hora_aplicado;
            $this->matricula_seleccionada->saldo_real = max(0, $totalCosto - $this->matricula_seleccionada->total_pagado);
        }
        
        $this->resumen_pagos = $this->obtenerResumenPagos($matriculaId);
    }
    
    private function obtenerResumenPagos($matriculaId)
    {
        $matricula = MatriculaTutoria::with(['tutoria'])->find($matriculaId);
        
        // Usamos la misma función de cálculo
        $totalPagado = $this->calcularTotalPagado($matriculaId);
        
        $pagos = PagoTutoria::where('matricula_tutoria_id', $matriculaId)
            ->orderBy('fecha_pago', 'asc')
            ->get();
        
        $pagosCompletados = $pagos->where('estado', 'completado');
        
        $totalCosto = $matricula->tutorias_registradas * $matricula->precio_hora_aplicado;
        $saldoReal = max(0, $totalCosto - $totalPagado);

        $pagosPorTipo = $pagosCompletados->sortBy('fecha_pago')->values();
        
        $horasAsistidas = 0;
        $precioHora = $matricula->precio_hora_aplicado ?? 0;
        if ($precioHora > 0) {
            $horasAsistidas = floor($totalPagado / $precioHora);
        }
        
        return [
            'matricula' => $matricula,
            'pagos_totales' => $pagos->count(),
            'pagos_completados' => $pagosCompletados->count(),
            'pagos_pendientes' => $pagos->where('estado', 'pendiente')->count(),
            'pagos_anulados' => $pagos->where('estado', 'anulado')->count(),
            'pagos_reembolsados' => $pagos->where('estado', 'reembolsado')->count(),
            'total_pagado' => $totalPagado, // Mismo cálculo que en la lista
            'saldo_pendiente' => $saldoReal, // Mismo cálculo que en la lista
            'pagos_por_tipo' => $pagosPorTipo,
            'horas_asistidas' => $horasAsistidas,
            'costo_total' => $totalCosto, // Para referencia
            'precio_hora_aplicado' => $precioHora,
            'tutorias_registradas' => $matricula->tutorias_registradas,
            'tutorias_pagadas' => $matricula->tutorias_pagadas,
            'estadisticas_metodos' => $pagosCompletados
                ->groupBy('metodo_pago')
                ->map(fn($p) => ['count' => $p->count(), 'total' => $p->sum('monto')]),
            'estadisticas_tipos' => $pagosCompletados
                ->groupBy('tipo')
                ->map(fn($p) => ['count' => $p->count(), 'total' => $p->sum('monto')])
        ];
    }
    
    public function limpiarBusqueda()
    {
        $this->reset([
            'tipo_busqueda',
            'dni_busqueda',
            'tutoria_busqueda',
            'estudiante_info',
            'tutoria_info',
            'matriculas_tutoria',
            'matricula_seleccionada',
            'resumen_pagos'
        ]);
    }
    
    public function render()
    {
        return view('livewire.pagos-tutoria.historial-pagos-tutorias');
    }
}