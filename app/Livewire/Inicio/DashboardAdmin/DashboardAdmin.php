<?php

namespace App\Livewire\Inicio\DashboardAdmin;

use Livewire\Component;
use App\Models\Matricula;
use App\Models\MatriculaTutoria;
use App\Models\Pago;
use App\Models\PagoTutoria;
use Carbon\Carbon;

class DashboardAdmin extends Component
{
    public $tab = 'hoy';
    public $tipo = 'todos';
    public $busqueda = '';
    public $telefonoDestino = '';
    public $mensajePersonalizado = '';
    public $mostrarModalWhatsApp = false;
    public $datosModal = [];
    
    public function getMetricasProperty()
    {
        $pendientes = $this->getPendientesCompletos();
        $hoy = Carbon::now();
        
        $pendientesHoy = $pendientes->filter(function($item) use ($hoy) {
            return $item['fecha']->isToday();
        });
        
        $pendientesSemana = $pendientes->filter(function($item) use ($hoy) {
            return $item['fecha']->between($hoy, $hoy->copy()->addWeek());
        });
        
        $pendientesMes = $pendientes->filter(function($item) use ($hoy) {
            return $item['fecha']->between($hoy, $hoy->copy()->addMonth());
        });
        
        $contarPorTipo = function($coleccion) {
            return [
                'modulos' => $coleccion->where('tipo', 'modulo')->count(),
                'tutorias' => $coleccion->where('tipo', 'tutoria')->count(),
                'total' => $coleccion->count(),
            ];
        };
        
        $inicioMes = Carbon::now()->startOfMonth();
        $ingresosMes = Pago::where('estado', 'completado')
            ->whereBetween('fecha_pago', [$inicioMes, $hoy])
            ->sum('monto') + 
            PagoTutoria::where('estado', 'completado')
            ->whereBetween('fecha_pago', [$inicioMes, $hoy])
            ->sum('monto');
        
        return [
            'hoy' => $contarPorTipo($pendientesHoy),
            'semana' => $contarPorTipo($pendientesSemana),
            'mes' => $contarPorTipo($pendientesMes),
            'ingresos_mes' => $ingresosMes,
        ];
    }
    
    private function getPendientesCompletos()
    {
        $pendientes = collect();
        $hoy = Carbon::now();
        
        $modulos = Matricula::with(['estudiante', 'modulo', 'pagos' => function($query) {
            $query->where('tipo', 'mensualidad')->orderBy('mes_pagado', 'desc');
        }])
            ->whereIn('estado', ['activa', 'pendiente'])
            ->where('saldo_pendiente', '>', 0)
            ->get()
            ->map(function($matricula) use ($hoy) {
                $mesPendiente = $this->calcularMesPendienteModulo($matricula, $hoy);
                
                $precioMensual = $matricula->modulo->precio_mensual ?? 0;
                $saldoMesActual = $precioMensual;
                
                $fechaProximoPago = $matricula->fecha_proximo_pago ? 
                    Carbon::parse($matricula->fecha_proximo_pago) : 
                    $this->calcularFechaProximoPago($matricula, $mesPendiente['mes_numero']);
                
                $dias = $fechaProximoPago->diffInDays($hoy, false);
                
                $telefonoDestino = $matricula->estudiante->telefono;
                $destino = 'estudiante';
                
                if (!empty($matricula->estudiante->telefono_tutor)) {
                    $telefonoDestino = $matricula->estudiante->telefono_tutor;
                    $destino = 'tutor';
                }
                
                $estado = 'pendiente';
                if ($dias < 0) {
                    $estado = 'vencido';
                } elseif ($dias <= 3) {
                    $estado = 'urgente';
                }
                
                return [
                    'id' => $matricula->id,
                    'tipo' => 'modulo',
                    'nombre' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                    'curso' => $matricula->modulo->nombre,
                    'codigo_modulo' => $matricula->modulo->codigo,
                    'fecha' => $fechaProximoPago,
                    'dias' => abs($dias),
                    'dias_real' => $dias,
                    'saldo' => $saldoMesActual,
                    'mes_actual' => $mesPendiente['mes_numero'],
                    'mes_nombre' => $mesPendiente['mes_nombre'],
                    'precio_mensual' => $precioMensual,
                    'telefono' => $telefonoDestino,
                    'telefono_tutor' => $matricula->estudiante->telefono_tutor,
                    'telefono_estudiante' => $matricula->estudiante->telefono,
                    'nombre_tutor' => $matricula->estudiante->nombre_tutor,
                    'destino' => $destino,
                    'estado' => $estado,
                    'total_pendiente' => $matricula->saldo_pendiente,
                    'fecha_matricula' => $matricula->fecha_matricula,
                    'duracion_meses' => $matricula->modulo->duracion_meses,
                    'ultimo_mes_pagado' => $mesPendiente['ultimo_mes_pagado'],
                ];
            });
        
        $pendientes = $pendientes->merge($modulos);
        
        $tutorias = MatriculaTutoria::with(['estudiante', 'tutoria'])
            ->whereIn('estado', ['activa', 'pendiente'])
            ->where('saldo_pendiente', '>', 0)
            ->get()
            ->map(function($matricula) use ($hoy) {
                $fechaProximaSesion = $matricula->fecha_proxima_sesion ? 
                    Carbon::parse($matricula->fecha_proxima_sesion) : $hoy;
                
                $dias = $fechaProximaSesion->diffInDays($hoy, false);
                
                $telefonoDestino = $matricula->estudiante->telefono;
                $destino = 'estudiante';
                
                if (!empty($matricula->estudiante->telefono_tutor)) {
                    $telefonoDestino = $matricula->estudiante->telefono_tutor;
                    $destino = 'tutor';
                }
                
                $estado = 'pendiente';
                if ($dias < 0) {
                    $estado = 'vencido';
                } elseif ($dias <= 3) {
                    $estado = 'urgente';
                }
                
                return [
                    'id' => $matricula->id,
                    'tipo' => 'tutoria',
                    'nombre' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                    'curso' => $matricula->tutoria->nombre,
                    'materia' => $matricula->tutoria->materia,
                    'fecha' => $fechaProximaSesion,
                    'dias' => abs($dias),
                    'dias_real' => $dias,
                    'saldo' => $matricula->saldo_pendiente,
                    'horas_pendientes' => $matricula->horas_pendientes ?? 0,
                    'horas_asistidas' => $matricula->horas_asistidas ?? 0,
                    'precio_hora' => $matricula->tutoria->precio_hora ?? 0,
                    'telefono' => $telefonoDestino,
                    'telefono_tutor' => $matricula->estudiante->telefono_tutor,
                    'telefono_estudiante' => $matricula->estudiante->telefono,
                    'nombre_tutor' => $matricula->estudiante->nombre_tutor,
                    'destino' => $destino,
                    'estado' => $estado,
                    'total_pendiente' => $matricula->saldo_pendiente,
                    'fecha_inicio' => $matricula->fecha_inicio,
                    'total_horas' => $matricula->tutoria->total_horas ?? 1,
                ];
            });
        
        return $pendientes->merge($tutorias);
    }
    
    private function calcularMesPendienteModulo($matricula, $hoy)
    {
        $fechaMatricula = Carbon::parse($matricula->fecha_matricula);
        
        $ultimoPago = $matricula->pagos
            ->where('estado', 'completado')
            ->where('tipo', 'mensualidad')
            ->sortByDesc('mes_pagado')
            ->first();
        
        $mesSiguiente = null;
        $mesPendienteNumero = 1;
        
        if ($ultimoPago && $ultimoPago->mes_pagado) {
            try {
                $ultimoMesPagado = Carbon::createFromFormat('Y-m', $ultimoPago->mes_pagado);
                
                $mesSiguiente = $ultimoMesPagado->copy()->addMonth();
                
                if ($mesSiguiente->isPast() || $mesSiguiente->isCurrentMonth()) {
                    $mesPendienteNumero = $this->calcularNumeroMesDesdeInicio($fechaMatricula, $mesSiguiente);
                }
            } catch (\Exception $e) {
            }
        }
        
        if (!$mesSiguiente) {
            $mesesTranscurridos = $fechaMatricula->diffInMonths($hoy);
            
            if ($fechaMatricula->day > $hoy->day) {
                $mesesTranscurridos++;
            }
            
            $mesPendienteNumero = min($mesesTranscurridos + 1, $matricula->modulo->duracion_meses);
            $mesPendienteNumero = max(1, $mesPendienteNumero);
            
            $mesSiguiente = $fechaMatricula->copy()->addMonths($mesPendienteNumero - 1);
        }
        
        $mesNombre = $this->getNombreMes($mesSiguiente);
        
        return [
            'mes_numero' => $mesPendienteNumero,
            'mes_nombre' => $mesNombre,
            'ultimo_mes_pagado' => $ultimoPago->mes_pagado ?? null,
            'fecha_pendiente' => $mesSiguiente,
        ];
    }
        
    private function calcularNumeroMesDesdeInicio($fechaInicio, $fechaMes)
    {
        $meses = $fechaInicio->diffInMonths($fechaMes) + 1;
        return max(1, $meses);
    }
    
    private function getNombreMes($fecha)
    {
        $mesesNombre = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        
        return $mesesNombre[$fecha->month];
    }
    
    private function calcularFechaProximoPago($matricula, $mesNumero)
    {
        $fechaMatricula = Carbon::parse($matricula->fecha_matricula);
        $fechaPago = $fechaMatricula->copy()->addMonths($mesNumero - 1);
        return $fechaPago->endOfMonth();
    }
    
    public function getPendientesProperty()
    {
        $todosPendientes = $this->getPendientesCompletos();
        $hoy = Carbon::now();
        
        if ($this->tab === 'hoy') {
            $pendientesFiltrados = $todosPendientes->filter(function($item) use ($hoy) {
                return $item['fecha']->isToday();
            });
        } elseif ($this->tab === 'semana') {
            $pendientesFiltrados = $todosPendientes->filter(function($item) use ($hoy) {
                return $item['fecha']->between($hoy, $hoy->copy()->addWeek());
            });
        } elseif ($this->tab === 'mes') {
            $pendientesFiltrados = $todosPendientes->filter(function($item) use ($hoy) {
                return $item['fecha']->between($hoy, $hoy->copy()->addMonth());
            });
        } else {
            $pendientesFiltrados = $todosPendientes;
        }
        
        if ($this->tipo === 'modulos') {
            $pendientesFiltrados = $pendientesFiltrados->where('tipo', 'modulo');
        } elseif ($this->tipo === 'tutorias') {
            $pendientesFiltrados = $pendientesFiltrados->where('tipo', 'tutoria');
        }
        
        if ($this->busqueda) {
            $busqueda = strtolower($this->busqueda);
            $pendientesFiltrados = $pendientesFiltrados->filter(function($item) use ($busqueda) {
                return str_contains(strtolower($item['nombre']), $busqueda) ||
                       str_contains(strtolower($item['curso']), $busqueda) ||
                       str_contains(strtolower($item['materia'] ?? ''), $busqueda) ||
                       str_contains(strtolower($item['codigo_modulo'] ?? ''), $busqueda);
            });
        }
        
        return $pendientesFiltrados->sortBy([
            fn($a, $b) => $this->ordenarPorEstado($a['estado'], $b['estado']),
            'fecha'
        ])->values();
    }
    
    private function ordenarPorEstado($estadoA, $estadoB)
    {
        $prioridad = [
            'vencido' => 1,
            'urgente' => 2,
            'pendiente' => 3,
        ];
        
        return $prioridad[$estadoA] <=> $prioridad[$estadoB];
    }
    
    public function getPagosRecientesProperty()
{
    $hoy = Carbon::now();
    $ayer = $hoy->copy()->subDay();
    
    $pagosModulos = Pago::with(['matricula.estudiante', 'matricula.modulo'])
        ->where('estado', 'completado')
        ->whereDate('fecha_pago', '>=', $ayer)
        ->limit(5)
        ->get()
        ->map(function($pago) {
            return [
                'tipo' => 'modulo',
                'estudiante' => $pago->matricula->estudiante->nombre . ' ' . $pago->matricula->estudiante->apellido,
                'curso' => $pago->matricula->modulo->nombre,
                'monto' => $pago->monto,
                'fecha' => $pago->fecha_pago,
                'metodo' => $pago->metodo_pago,
            ];
        })->toArray();
    
    $pagosTutorias = PagoTutoria::with(['matriculaTutoria.estudiante', 'matriculaTutoria.tutoria'])
        ->where('estado', 'completado')
        ->whereDate('fecha_pago', '>=', $ayer)
        ->limit(5)
        ->get()
        ->map(function($pago) {
            return [
                'tipo' => 'tutoria',
                'estudiante' => $pago->matriculaTutoria->estudiante->nombre . ' ' . $pago->matriculaTutoria->estudiante->apellido,
                'curso' => $pago->matriculaTutoria->tutoria->nombre,
                'monto' => $pago->monto,
                'fecha' => $pago->fecha_pago,
                'metodo' => $pago->metodo_pago,
            ];
        })->toArray();

    $pagosCombinados = array_merge($pagosModulos, $pagosTutorias);
    usort($pagosCombinados, function($a, $b) {
        return $b['fecha'] <=> $a['fecha'];
    });

    return array_slice($pagosCombinados, 0, 10);
}
    
    private function generarEnlaceWhatsApp($telefono, $mensaje)
    {
        $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefono);
        
        if (strlen($telefonoLimpio) < 8) {
            return null;
        }
        
        if (!str_starts_with($telefonoLimpio, '504') && strlen($telefonoLimpio) == 8) {
            $telefonoLimpio = '504' . $telefonoLimpio;
        }
        
        $mensajeCodificado = urlencode($mensaje);
        
        return "https://wa.me/{$telefonoLimpio}?text={$mensajeCodificado}";
    }
    
    public function abrirModalWhatsApp($datos)
    {
        $this->datosModal = $datos;
        $this->telefonoDestino = $datos['telefono'] ?? ($datos['telefono_estudiante'] ?? '');
        $this->mensajePersonalizado = $this->construirMensajeWhatsApp($datos);
        $this->mostrarModalWhatsApp = true;
    }
    
    private function construirMensajeWhatsApp($datos)
    {
        $fechaFormateada = Carbon::parse($datos['fecha'])->format('d/m/Y');
        $saldoFormateado = number_format($datos['saldo'], 2);
        
        if ($datos['tipo'] === 'modulo') {
            $ultimoMesPagado = $datos['ultimo_mes_pagado'] ?? null;
            
            $mensaje = "Hola {$datos['nombre']}, le informamos sobre su pago pendiente:\n\n";
            $mensaje .= " *Módulo:* {$datos['curso']}\n";
            
            if ($ultimoMesPagado) {
                $mensaje .= " *Último mes pagado:* " . Carbon::createFromFormat('Y-m', $ultimoMesPagado)->format('F Y') . "\n";
            }
            
            $mensaje .= " *Mes a pagar:* Mes {$datos['mes_actual']} (de {$datos['duracion_meses']} meses) - {$datos['mes_nombre']}\n";
            $mensaje .= " *Mensualidad:* L. " . number_format($datos['precio_mensual'], 2) . "\n";
            $mensaje .= " *Total a pagar:* L. {$saldoFormateado}\n\n";
            
            if ($datos['total_pendiente'] > $datos['saldo']) {
                $mensaje .= " *Saldo total pendiente:* L. " . number_format($datos['total_pendiente'], 2) . "\n\n";
            }
            
            $mensaje .= "Por favor, realice su pago lo antes posible.\n";
            $mensaje .= "¡Gracias por su atención!";
        } else {
            $mensaje = "Hola {$datos['nombre']}, le informamos sobre su pago pendiente:\n\n";
            $mensaje .= " *Tutoría:* {$datos['curso']}\n";
            if (!empty($datos['materia'])) {
                $mensaje .= " *Materia:* {$datos['materia']}\n";
            }
            $mensaje .= " *Próxima sesión:* {$fechaFormateada}\n";
            $mensaje .= " *Horas pendientes:* {$datos['horas_pendientes']}\n";
            $mensaje .= " *Precio por hora:* L. " . number_format($datos['precio_hora'], 2) . "\n";
            $mensaje .= " *Total a pagar:* L. {$saldoFormateado}\n\n";
            $mensaje .= "Por favor, realice su pago lo antes posible.\n";
            $mensaje .= "¡Gracias por su atención!";
        }
        
        return $mensaje;
    }
    
    public function enviarWhatsApp()
    {
        if (empty($this->telefonoDestino)) {
            session()->flash('error', 'Por favor, seleccione un número de teléfono');
            return;
        }
        
        $enlaceWhatsApp = $this->generarEnlaceWhatsApp(
            $this->telefonoDestino,
            $this->mensajePersonalizado
        );
        
        if (!$enlaceWhatsApp) {
            session()->flash('error', 'Número de teléfono inválido');
            return;
        }
        
        \Log::info('Recordatorio de pago enviado por WhatsApp', [
            'tipo' => $this->datosModal['tipo'],
            'id' => $this->datosModal['id'],
            'estudiante' => $this->datosModal['nombre'],
            'telefono' => $this->telefonoDestino,
            'monto' => $this->datosModal['saldo'],
            'fecha_envio' => now(),
        ]);
        
        $this->dispatch('abrirWhatsApp', url: $enlaceWhatsApp);
        
        $this->mostrarModalWhatsApp = false;
        $this->datosModal = [];
        
        session()->flash('mensaje', 'Enlace de WhatsApp generado. Se abrirá en una nueva pestaña.');
    }
    
    public function cambiarTelefonoDestino($telefono)
    {
        $this->telefonoDestino = $telefono;
    }
    
    public function render()
    {
        return view('livewire.inicio.dashboard-admin.dashboard-admin', [
            'metricas' => $this->metricas,
            'pendientes' => $this->pendientes,
            'pagosRecientes' => $this->pagosRecientes,
        ]);
    }
}