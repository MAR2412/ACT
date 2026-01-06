<?php

namespace App\Livewire\Matricula;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Estudiante;
use App\Models\Modulo;
use App\Models\Pago;
use App\Models\Matricula;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\LogService;
use Barryvdh\DomPDF\Facade\Pdf;

class Matriculas extends Component
{
    use WithPagination;
    
    public $isEditing = false;
    public $isOpen = false;
    public $showDeleteModal = false;
    public $showErrorModal = false;
    public $showFinancialInfo = false;
    public $showExamenModal = false;
    public $showDescuentoModal = false;
    public $showCamisetaModal = false;
    public $showGraduacionModal = false;
    public $errorMessage;
    public $matricula_id = null;
    public $estudiante_id = '';
    public $modulo_id = '';
    public $estado = 'activa';
    public $precio_total_modulo = 0;
    public $saldo_pendiente = 0;
    public $meses_pendientes = 0;
    public $meses_pagados = 0;
    public $fecha_matricula;
    public $fecha_ultimo_pago = null;
    public $fecha_proximo_pago = null;
    public $aprobado = false;
    public $observaciones = '';
    public $matricula_anterior_id = null;
    
    public $dni_busqueda = '';
    public $estudiante_info = null;
    public $modulo_info = null;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $IdAEliminar;
    public $matriculaAEliminar;
    public $modulos = [];
    public $estados = ['activa', 'completada', 'cancelada', 'pendiente'];
    
    public $examen_nota = '';
    public $examen_fecha = '';
    public $examen_observaciones = '';
    
    public $porcentaje_descuento = 0;
    public $descuento_observaciones = '';
    public $precio_con_descuento = 0;
    public $monto_descuento = 0;
    public $precio_original = 0;
    public $precio_mensual = 0;
    public $descuento_primer_mes = true;
    
    public $examenSuficienciaHabilitado = false;
    public $descuentoHabilitado = false;
    
    public $matriculaSeleccionada = null;
    public $monto_camiseta = '';
    public $monto_gastos_graduacion = '';

    
    public $editarCamiseta = false;
    public $editarGraduacion = false;

    public function mount()
    {
        $this->fecha_matricula = now()->format('Y-m-d');
        $this->examen_fecha = now()->format('Y-m-d');
        
        $fechaLimiteInicio = now()->subWeeks(2);
        
        $this->modulos = Modulo::where('estado', true)
            ->with(['sede', 'modalidad', 'seccion'])
            ->where(function ($query) use ($fechaLimiteInicio) {
                $query->where(function ($subQuery) {
                    $subQuery->whereNull('fecha_inicio')
                           ->orWhere('fecha_inicio', '>=', now());
                })
                ->orWhere(function ($subQuery) use ($fechaLimiteInicio) {
                    $subQuery->whereNotNull('fecha_inicio')
                           ->where('fecha_inicio', '>=', $fechaLimiteInicio)
                           ->where('fecha_inicio', '<', now());
                });
            })
            ->orderBy('nivel')
            ->orderBy('orden')
            ->get();
    }

    public function abrirModalCamiseta($matriculaId)
    {
        $this->matriculaSeleccionada = Matricula::with('estudiante')->find($matriculaId);
        $this->editarCamiseta = false;
        
        if ($this->matriculaSeleccionada->pago_camiseta) {
            $this->monto_camiseta = $this->matriculaSeleccionada->monto_camiseta;
            $this->editarCamiseta = true;
            $this->showEditCamisetaModal = true;
        } else {
            $this->monto_camiseta = '';
            $this->showCamisetaModal = true;
        }
    }
    public $showEditGraduacionModal;
    public $showEditCamisetaModal;
    public function abrirModalGraduacion($matriculaId)
    {
        $this->matriculaSeleccionada = Matricula::with('estudiante')->find($matriculaId);
        $this->editarGraduacion = false;
        
        if ($this->matriculaSeleccionada->pago_gastos_graduacion) {
            $this->monto_gastos_graduacion = $this->matriculaSeleccionada->monto_gastos_graduacion;
            $this->editarGraduacion = true;
            $this->showEditGraduacionModal = true;
        } else {
            $this->monto_gastos_graduacion = '';
            $this->showGraduacionModal = true;
        }
    }

    public function registrarPagoCamiseta()
    {
        $this->validate([
            'monto_camiseta' => 'required|numeric|min:0',
        ]);

        try {
            if ($this->editarCamiseta) {
               
                $this->matriculaSeleccionada->update([
                    'monto_camiseta' => $this->monto_camiseta,
                ]);

                LogService::activity(
                    'editar_pago_camiseta',
                    'Matrículas',
                    "Se editó el pago de camiseta para matrícula #{$this->matriculaSeleccionada->id}",
                    [
                        'Editado por' => Auth::user()->email,
                        'Estudiante' => $this->matriculaSeleccionada->estudiante->nombre . ' ' . $this->matriculaSeleccionada->estudiante->apellido,
                        'Nuevo monto' => $this->monto_camiseta,
                    ]
                );

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Monto de camiseta actualizado correctamente.'
                ]);
            } else {
                // Registrar nuevo pago
                $this->matriculaSeleccionada->registrarPagoCamiseta($this->monto_camiseta);

                LogService::activity(
                    'pago_camiseta',
                    'Matrículas',
                    "Se registró pago de camiseta para matrícula #{$this->matriculaSeleccionada->id}",
                    [
                        'Registrado por' => Auth::user()->email,
                        'Estudiante' => $this->matriculaSeleccionada->estudiante->nombre . ' ' . $this->matriculaSeleccionada->estudiante->apellido,
                        'Monto' => $this->monto_camiseta,
                    ]
                );

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Pago de camiseta registrado correctamente.'
                ]);
            }
            
            $this->cerrarModalCamiseta();
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function registrarPagoGraduacion()
    {
        $this->validate([
            'monto_gastos_graduacion' => 'required|numeric|min:0',
        ]);

        try {
            if ($this->editarGraduacion) {
              
                $this->matriculaSeleccionada->update([
                    'monto_gastos_graduacion' => $this->monto_gastos_graduacion,
                ]);

                LogService::activity(
                    'editar_pago_graduacion',
                    'Matrículas',
                    "Se editó el pago de gastos de graduación para matrícula #{$this->matriculaSeleccionada->id}",
                    [
                        'Editado por' => Auth::user()->email,
                        'Estudiante' => $this->matriculaSeleccionada->estudiante->nombre . ' ' . $this->matriculaSeleccionada->estudiante->apellido,
                        'Nuevo monto' => $this->monto_gastos_graduacion,
                    ]
                );

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Monto de gastos de graduación actualizado correctamente.'
                ]);
            } else {
                $this->matriculaSeleccionada->registrarPagoGastosGraduacion($this->monto_gastos_graduacion);

                LogService::activity(
                    'pago_graduacion',
                    'Matrículas',
                    "Se registró pago de gastos de graduación para matrícula #{$this->matriculaSeleccionada->id}",
                    [
                        'Registrado por' => Auth::user()->email,
                        'Estudiante' => $this->matriculaSeleccionada->estudiante->nombre . ' ' . $this->matriculaSeleccionada->estudiante->apellido,
                        'Monto' => $this->monto_gastos_graduacion,
                    ]
                );

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Pago de gastos de graduación registrado correctamente.'
                ]);
            }
            
            $this->cerrarModalGraduacion();
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    
    public function cancelarPagoCamiseta($matriculaId)
    {
        try {
            $matricula = Matricula::find($matriculaId);
            
            LogService::activity(
                'cancelar_pago_camiseta',
                'Matrículas',
                "Se canceló el pago de camiseta para matrícula #{$matricula->id}",
                [
                    'Cancelado por' => Auth::user()->email,
                    'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                    'Monto cancelado' => $matricula->monto_camiseta,
                ]
            );
            
            $matricula->update([
                'pago_camiseta' => false,
                'monto_camiseta' => 0,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Pago de camiseta cancelado correctamente.'
            ]);
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function cancelarPagoGraduacion($matriculaId)
    {
        try {
            $matricula = Matricula::find($matriculaId);
            
            LogService::activity(
                'cancelar_pago_graduacion',
                'Matrículas',
                "Se canceló el pago de gastos de graduación para matrícula #{$matricula->id}",
                [
                    'Cancelado por' => Auth::user()->email,
                    'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                    'Monto cancelado' => $matricula->monto_gastos_graduacion,
                ]
            );
            
            $matricula->update([
                'pago_gastos_graduacion' => false,
                'monto_gastos_graduacion' => 0,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Pago de gastos de graduación cancelado correctamente.'
            ]);
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function cerrarModalCamiseta()
    {
        $this->showCamisetaModal = false;
        $this->showEditCamisetaModal = false;
        $this->matriculaSeleccionada = null;
        $this->monto_camiseta = '';
        $this->editarCamiseta = false;
    }

    public function cerrarModalGraduacion()
    {
        $this->showGraduacionModal = false;
        $this->showEditGraduacionModal = false;
        $this->matriculaSeleccionada = null;
        $this->monto_gastos_graduacion = '';
        $this->editarGraduacion = false;
    }    public function render()
    {
        $fechaLimite = now()->subWeeks(2);
        
        $matriculas = Matricula::withTrashed()
            ->with(['estudiante', 'modulo.sede', 'modulo.modalidad'])
            ->where(function ($q) {
                $q->whereHas('estudiante', function ($q2) {
                    $q2->where('nombre', 'like', "%{$this->search}%")
                       ->orWhere('apellido', 'like', "%{$this->search}%")
                       ->orWhere('dni', 'like', "%{$this->search}%");
                })
                ->orWhereHas('modulo', function ($q2) {
                    $q2->where('nombre', 'like', "%{$this->search}%");
                })
                ->orWhere('estado', 'like', "%{$this->search}%")
                ->orWhere('observaciones', 'like', "%{$this->search}%");
            })
            ->whereHas('modulo', function ($query) use ($fechaLimite) {
                $query->where(function ($subQuery) use ($fechaLimite) {
                    $subQuery->whereNull('fecha_fin')
                    ->orWhere('fecha_fin', '>=', now())
                    ->orWhere('fecha_fin', '>=', $fechaLimite);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.matricula.matriculas', compact('matriculas'));
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isOpen = true;
    }

    
    public function mostrarInformacionFinanciera($id)
    {
        $matricula = Matricula::with(['modulo', 'estudiante'])->findOrFail($id);
        
        $this->matricula_id = $matricula->id;
        $this->estudiante_info = $matricula->estudiante;
        $this->modulo_info = $matricula->modulo;
        
        $this->precio_total_modulo = $matricula->precio_total_modulo;
        $this->meses_pendientes = $matricula->meses_pendientes;
        $this->meses_pagados = $matricula->meses_pagados;
        $this->fecha_ultimo_pago = $matricula->fecha_ultimo_pago ? $matricula->fecha_ultimo_pago->format('Y-m-d') : null;
        $this->saldo_pendiente = $matricula->saldo_pendiente;
        $this->fecha_matricula = $matricula->fecha_matricula->format('Y-m-d');
        
        if ($matricula->fecha_ultimo_pago) {
            $this->fecha_proximo_pago = Carbon::parse($matricula->fecha_ultimo_pago)
                ->addMonth()
                ->format('Y-m-d');
        } else {
            $this->fecha_proximo_pago = Carbon::parse($matricula->fecha_matricula)
                ->addMonth()
                ->format('Y-m-d');
        }
        
        $this->showFinancialInfo = true;
    }

    public function openExamenModal($matriculaId = null)
    {
        if ($matriculaId) {
            $matricula = Matricula::find($matriculaId);
            if ($matricula) {
                $this->matricula_id = $matricula->id;
                $this->examen_nota = $matricula->examen_suficiencia_nota ?? '';
                $this->examen_fecha = $matricula->examen_suficiencia_fecha 
                    ? $matricula->examen_suficiencia_fecha->format('Y-m-d')
                    : now()->format('Y-m-d');
                $this->estudiante_info = $matricula->estudiante;
                $this->modulo_info = $matricula->modulo;
            }
        }
        $this->showExamenModal = true;
    }

    public function habilitarDescuento()
    {
        $this->descuentoHabilitado = true;
        $this->calcularDescuento();
    }

    public function deshabilitarDescuento()
    {
        $this->descuentoHabilitado = false;
        $this->porcentaje_descuento = 0;
        $this->monto_descuento = 0;
        $this->precio_con_descuento = 0;
        $this->actualizarTotalesSinDescuento();
    }

    private function actualizarTotalesSinDescuento()
    {
        if ($this->modulo_info) {
            $precioMensual = $this->modulo_info->precio_mensual;
            $duracion = $this->modulo_info->duracion_meses;
            
            $this->precio_total_modulo = $precioMensual * $duracion;
            $this->precio_original = $this->precio_total_modulo;
            
            if ($this->meses_pagados > 0) {
                $montoPagado = $precioMensual * $this->meses_pagados;
                $this->saldo_pendiente = $this->precio_total_modulo - $montoPagado;
            } else {
                $this->saldo_pendiente = $this->precio_total_modulo;
            }
            
            $this->meses_pendientes = $duracion - $this->meses_pagados;
        }
    }

    public function calcularDescuento()
    {
        if ($this->modulo_info) {
            $precioMensual = $this->modulo_info->precio_mensual;
            $duracion = $this->modulo_info->duracion_meses;
            $precioTotal = $precioMensual * $duracion;
            
            if ($this->descuento_primer_mes) {
                $this->monto_descuento = $precioMensual * ($this->porcentaje_descuento / 100);
                $this->precio_con_descuento = $precioMensual - $this->monto_descuento;
                $this->precio_total_modulo = ($this->precio_con_descuento) + ($precioMensual * ($duracion - 1));
                $this->precio_original = $precioTotal;
            } else {
                $this->monto_descuento = $precioTotal * ($this->porcentaje_descuento / 100);
                $this->precio_con_descuento = $precioTotal - $this->monto_descuento;
                $this->precio_total_modulo = $this->precio_con_descuento;
                $this->precio_original = $precioTotal;
            }
            
            $this->actualizarSaldoPendiente();
        }
    }

    private function actualizarSaldoPendiente()
    {
        if ($this->modulo_info) {
            $precioMensual = $this->modulo_info->precio_mensual;
            $duracion = $this->modulo_info->duracion_meses;
            
            $totalConDescuento = $this->precio_total_modulo;
            
            $montoPagado = 0;
            if ($this->meses_pagados > 0) {
                for ($i = 1; $i <= $this->meses_pagados; $i++) {
                    if ($i === 1 && $this->descuentoHabilitado && $this->descuento_primer_mes) {
                        $montoPagado += $this->precio_con_descuento;
                    } else {
                        $montoPagado += $precioMensual;
                    }
                }
            }
            
            if (!$this->descuento_primer_mes && $this->descuentoHabilitado) {
                $this->saldo_pendiente = max(0, $totalConDescuento - $montoPagado);
            } else {
                $this->saldo_pendiente = max(0, $totalConDescuento - $montoPagado);
            }
            
            $this->meses_pendientes = max(0, $duracion - $this->meses_pagados);
        }
    }

    public function updatedPorcentajeDescuento($value)
    {
        $this->calcularDescuento();
    }

    public function updatedDescuentoPrimerMes($value)
    {
        $this->calcularDescuento();
    }

    public function aplicarExamenSuficiencia()
    {
        $this->validate([
            'examen_nota' => 'required|numeric|min:0|max:100',
            'examen_fecha' => 'required|date',
            'examen_observaciones' => 'nullable|string|max:500',
        ]);

        try {
            $matricula = Matricula::findOrFail($this->matricula_id);
            
            $matricula->registrarExamenSuficiencia(
                $this->examen_nota,
                $this->examen_fecha
            );
            
            if (!empty($this->examen_observaciones)) {
                $matricula->update([
                    'observaciones' => $matricula->observaciones 
                        ? $matricula->observaciones . ' | Examen suficiencia: ' . $this->examen_observaciones
                        : 'Examen suficiencia: ' . $this->examen_observaciones
                ]);
            }

            LogService::activity(
                'registrar_examen',
                'Matrículas',
                "Se registró examen de suficiencia para matrícula #{$matricula->id}",
                [
                    'Registrado por' => Auth::user()->email,
                    'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                    'Nota' => $this->examen_nota,
                    'Fecha' => $this->examen_fecha,
                ]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Examen de suficiencia registrado correctamente.'
            ]);
            
            $this->closeExamenModal();
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function aplicarDescuento()
    {
        $this->validate([
            'porcentaje_descuento' => 'required|numeric|min:0|max:100',
            'descuento_observaciones' => 'nullable|string|max:500',
        ]);

        try {
            if ($this->matricula_id) {
                $matricula = Matricula::findOrFail($this->matricula_id);
                $matricula->aplicarDescuento(
                    $this->porcentaje_descuento,
                    $this->descuento_observaciones,
                    $this->descuento_primer_mes
                );
                
                LogService::activity(
                    'aplicar_descuento',
                    'Matrículas',
                    "Se aplicó descuento a matrícula #{$matricula->id}",
                    [
                        'Aplicado por' => Auth::user()->email,
                        'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                        'Porcentaje' => $this->porcentaje_descuento . '%',
                        'Tipo' => $this->descuento_primer_mes ? 'Primer mes' : 'Total módulo',
                        'Monto descuento' => 'L. ' . number_format($this->monto_descuento, 2),
                    ]
                );

                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Descuento aplicado correctamente.'
                ]);
            } else {
                $this->descuentoHabilitado = true;
                $this->monto_descuento = $this->monto_descuento;
                $this->precio_con_descuento = $this->precio_con_descuento;
                
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Descuento configurado. Se aplicará al crear la matrícula.'
                ]);
            }
            
            $this->closeDescuentoModal();
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function edit(Matricula $matricula)
    {
        $this->isEditing = true;
        $this->isOpen = true;
        
        $this->matricula_id = $matricula->id;
        $this->estudiante_id = $matricula->estudiante_id;
        $this->modulo_id = $matricula->modulo_id;
        $this->estado = $matricula->estado;
        $this->precio_total_modulo = $matricula->precio_total_modulo;
        $this->saldo_pendiente = $matricula->saldo_pendiente;
        $this->meses_pendientes = $matricula->meses_pendientes;
        $this->meses_pagados = $matricula->meses_pagados;
        $this->fecha_matricula = $matricula->fecha_matricula->format('Y-m-d');
        $this->fecha_ultimo_pago = $matricula->fecha_ultimo_pago ? $matricula->fecha_ultimo_pago->format('Y-m-d') : null;
        $this->fecha_proximo_pago = $matricula->fecha_proximo_pago ? $matricula->fecha_proximo_pago->format('Y-m-d') : null;
        $this->aprobado = $matricula->aprobado;
        $this->observaciones = $matricula->observaciones;
        $this->matricula_anterior_id = $matricula->matricula_anterior_id;
        
        $this->estudiante_info = $matricula->estudiante;
        $this->dni_busqueda = $matricula->estudiante->dni;
        
        $this->modulo_info = Modulo::with(['sede', 'modalidad', 'seccion'])->find($matricula->modulo_id);
        
        $this->examenSuficienciaHabilitado = $matricula->examen_suficiencia;
        $this->descuentoHabilitado = $matricula->descuento_aplicado;
        $this->porcentaje_descuento = $matricula->porcentaje_descuento;
        $this->monto_descuento = $matricula->monto_descuento;
        $this->descuento_primer_mes = $matricula->descuento_primer_mes;
    }

    public function hideError()
    {
        $this->showErrorModal = false;
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
            $this->estudiante_id = $estudiante->id;
            
            $fechaLimite = now()->subWeeks(2);
            
            $matriculaActiva = Matricula::where('estudiante_id', $estudiante->id)
                ->whereIn('estado', ['activa', 'pendiente'])
                ->whereHas('modulo', function ($query) use ($fechaLimite) {
                    $query->where(function ($subQuery) use ($fechaLimite) {
                        $subQuery->whereNull('fecha_fin')
                        ->orWhere('fecha_fin', '>=', $fechaLimite);
                    });
                })
                ->first();
            
            if ($matriculaActiva) {
                $fechaFin = $matriculaActiva->modulo->fecha_fin;
                $fechaFinFormatted = $fechaFin ? $fechaFin->format('d/m/Y') : 'sin fecha de fin';
                
                $this->errorMessage = "El estudiante ya está matriculado en el módulo '{$matriculaActiva->modulo->nombre}' ({$fechaFinFormatted}). No puede matricularse en otro módulo hasta que finalice este.";
                $this->showErrorModal = true;
                $this->estudiante_info = null;
                $this->estudiante_id = '';
                return;
            }
            
            $matriculasCompletadas = Matricula::where('estudiante_id', $estudiante->id)
                ->where('estado', 'completada')
                ->whereHas('modulo', function ($query) use ($fechaLimite) {
                    $query->where(function ($subQuery) use ($fechaLimite) {
                        $subQuery->whereNull('fecha_fin')
                        ->orWhere('fecha_fin', '>=', $fechaLimite);
                    });
                })
                ->with('modulo')
                ->orderBy('created_at', 'desc')
                ->get();
            
            $this->estudiante_info->matriculas_completadas = $matriculasCompletadas;
            
        } else {
            $this->estudiante_info = null;
            $this->estudiante_id = '';
            $this->errorMessage = "Estudiante con DNI {$this->dni_busqueda} no encontrado o está inactivo.";
            $this->showErrorModal = true;
        }
    }
    
    public function updatedModuloId($value)
    {
        if ($value) {
            $modulo = Modulo::with(['sede', 'modalidad', 'seccion'])->find($value);
            if ($modulo) {
                $this->modulo_info = $modulo;
                $this->precio_mensual = $modulo->precio_mensual;
                
                if ($this->descuentoHabilitado) {
                    $this->calcularDescuento();
                } else {
                    $this->actualizarTotalesSinDescuento();
                }
                
                if ($this->fecha_matricula) {
                    $this->fecha_proximo_pago = Carbon::parse($this->fecha_matricula)
                        ->addMonth()
                        ->format('Y-m-d');
                } else {
                    $this->fecha_proximo_pago = Carbon::now()->addMonth()->format('Y-m-d');
                }
                
                $verificarRequisito = true;
                
                if ($this->estudiante_info && $modulo->modulo_requerido_id) {
                    $matriculaPrevia = Matricula::where('estudiante_id', $this->estudiante_info->id)
                        ->where('modulo_id', $modulo->modulo_requerido_id)
                        ->where('estado', 'completada')
                        ->where('aprobado', true)
                        ->first();
                    
                    if ($matriculaPrevia) {
                        $this->matricula_anterior_id = $matriculaPrevia->id;
                    } else {
                        $this->examenSuficienciaHabilitado = true;
                        $verificarRequisito = false;
                    }
                }
                
                if ($verificarRequisito) {
                    $fechaLimite = now()->subWeeks(2);
                    
                    if ($this->estudiante_info) {
                        $matriculaExistente = Matricula::where('estudiante_id', $this->estudiante_info->id)
                            ->where('modulo_id', $modulo->id)
                            ->whereIn('estado', ['activa', 'pendiente'])
                            ->whereHas('modulo', function ($query) use ($fechaLimite) {
                                $query->where(function ($subQuery) use ($fechaLimite) {
                                    $subQuery->whereNull('fecha_fin')
                                    ->orWhere('fecha_fin', '>=', $fechaLimite);
                                });
                            })
                            ->first();
                        
                        if ($matriculaExistente) {
                            $this->errorMessage = 'El estudiante ya está matriculado en este módulo.';
                            $this->showErrorModal = true;
                        }
                    }
                }
            }
        } else {
            $this->resetModuloInfo();
        }
    }
    
    private function resetModuloInfo()
    {
        $this->modulo_info = null;
        $this->precio_total_modulo = 0;
        $this->saldo_pendiente = 0;
        $this->meses_pendientes = 0;
        $this->meses_pagados = 0;
        $this->fecha_proximo_pago = null;
        $this->matricula_anterior_id = null;
        $this->examenSuficienciaHabilitado = false;
        $this->descuentoHabilitado = false;
        $this->precio_mensual = 0;
    }
    
    public function updatedMesesPagados($value)
    {
        if ($this->modulo_info) {
            $duracion = $this->modulo_info->duracion_meses;
            $precioMensual = $this->modulo_info->precio_mensual;
            
            if ($value > $duracion) {
                $this->meses_pagados = $duracion;
                $value = $duracion;
            }
            
            if ($value < 0) {
                $this->meses_pagados = 0;
                $value = 0;
            }
            
            $this->meses_pendientes = $duracion - $value;
            
            if ($this->descuentoHabilitado) {
                $this->calcularDescuento();
            } else {
                $this->actualizarTotalesSinDescuento();
            }
            
            if ($value > 0) {
                $this->fecha_ultimo_pago = Carbon::parse($this->fecha_matricula)
                    ->addMonths($value - 1)
                    ->format('Y-m-d');
                    
                $this->fecha_proximo_pago = Carbon::parse($this->fecha_ultimo_pago)
                    ->addMonth()
                    ->format('Y-m-d');
            } else {
                $this->fecha_ultimo_pago = null;
                $this->fecha_proximo_pago = Carbon::parse($this->fecha_matricula)
                    ->addMonth()
                    ->format('Y-m-d');
            }
        }
    }

    public function actualizarInformacionFinanciera()
    {
        if ($this->modulo_info) {
            $precioPorMes = $this->modulo_info->precio_mensual;
            $duracion = $this->modulo_info->duracion_meses;
            
            $this->precio_total_modulo = $precioPorMes * $duracion;
            $this->precio_original = $this->precio_total_modulo;
            $this->saldo_pendiente = $this->precio_total_modulo;
            $this->meses_pendientes = $duracion;
            $this->meses_pagados = 0;
            $this->fecha_ultimo_pago = null;
            
            if ($this->fecha_matricula) {
                $this->fecha_proximo_pago = Carbon::parse($this->fecha_matricula)
                    ->addMonth()
                    ->format('Y-m-d');
            }
            
            if ($this->matricula_id && $this->isEditing) {
                try {
                    $matricula = Matricula::find($this->matricula_id);
                    if ($matricula) {
                        $matricula->update([
                            'precio_total_modulo' => $this->precio_total_modulo,
                            'saldo_pendiente' => $this->saldo_pendiente,
                            'meses_pendientes' => $this->meses_pendientes,
                            'meses_pagados' => $this->meses_pagados,
                            'fecha_ultimo_pago' => $this->fecha_ultimo_pago,
                            'fecha_proximo_pago' => $this->fecha_proximo_pago,
                        ]);
                        
                        session()->flash('message', 'Información financiera actualizada.');
                    }
                } catch (\Exception $e) {
                    $this->errorMessage = 'Error al actualizar: ' . $e->getMessage();
                    $this->showErrorModal = true;
                }
            }
        }
    }

    public function save()
    {
        if ($this->isEditing) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function store()
    {
        $this->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'modulo_id' => 'required|exists:modulos,id',
            'estado' => 'required|in:activa,completada,cancelada,pendiente',
            'fecha_matricula' => 'required|date',
            'fecha_ultimo_pago' => 'nullable|date',
            'fecha_proximo_pago' => 'nullable|date',
            'aprobado' => 'boolean',
            'observaciones' => 'nullable|string',
            'matricula_anterior_id' => 'nullable|exists:matriculas,id',
        ]);

        try {
            $fechaLimite = now()->subWeeks(2);
            
            $matriculaExistente = Matricula::where('estudiante_id', $this->estudiante_id)
                ->where('modulo_id', $this->modulo_id)
                ->whereIn('estado', ['activa', 'pendiente'])
                ->whereHas('modulo', function ($query) use ($fechaLimite) {
                    $query->where(function ($subQuery) use ($fechaLimite) {
                        $subQuery->whereNull('fecha_fin')
                        ->orWhere('fecha_fin', '>=', $fechaLimite);
                    });
                })
                ->first();
                
            if ($matriculaExistente) {
                throw new \Exception('El estudiante ya tiene una matrícula activa en este módulo.');
            }
            
            if ($this->modulo_info && $this->modulo_info->modulo_requerido_id && !$this->matricula_anterior_id && !$this->examenSuficienciaHabilitado) {
                $moduloRequerido = Modulo::find($this->modulo_info->modulo_requerido_id);
                throw new \Exception("Este módulo requiere haber completado y aprobado el módulo '{$moduloRequerido->nombre}'. Puede habilitar examen de suficiencia.");
            }
            
            $precioFinal = $this->precio_total_modulo;
            
            $matriculaData = [
                'estudiante_id' => $this->estudiante_id,
                'modulo_id' => $this->modulo_id,
                'estado' => $this->estado,
                'precio_total_modulo' => $precioFinal,
                'saldo_pendiente' => $this->saldo_pendiente,
                'meses_pendientes' => $this->meses_pendientes,
                'meses_pagados' => $this->meses_pagados,
                'fecha_matricula' => $this->fecha_matricula,
                'fecha_ultimo_pago' => $this->fecha_ultimo_pago,
                'fecha_proximo_pago' => $this->fecha_proximo_pago,
                'aprobado' => $this->aprobado,
                'observaciones' => $this->observaciones,
                'matricula_anterior_id' => $this->matricula_anterior_id,
                'examen_suficiencia' => $this->examenSuficienciaHabilitado,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ];
            
            if ($this->descuentoHabilitado) {
                $matriculaData['descuento_aplicado'] = true;
                $matriculaData['porcentaje_descuento'] = $this->porcentaje_descuento;
                $matriculaData['monto_descuento'] = $this->monto_descuento;
                $matriculaData['descuento_primer_mes'] = $this->descuento_primer_mes;
                $matriculaData['precio_original'] = $this->precio_original;
            }
            
            $matricula = Matricula::create($matriculaData);

            if ($this->meses_pagados > 0) {
                $precioMensual = $this->modulo_info->precio_mensual;
                
                for ($i = 1; $i <= $this->meses_pagados; $i++) {
                    $fechaPagoMes = Carbon::parse($this->fecha_matricula)
                        ->addMonths($i - 1)
                        ->format('Y-m-d');
                        
                    $mesPagado = Carbon::parse($this->fecha_matricula)
                        ->addMonths($i - 1)
                        ->format('Y-m');
                    
                    $montoPago = $precioMensual;
                    
                    if ($this->descuentoHabilitado) {
                        if ($this->descuento_primer_mes && $i === 1) {
                            $montoPago = $this->precio_con_descuento;
                        } elseif (!$this->descuento_primer_mes) {
                            $montoMensualConDescuento = $this->precio_con_descuento / $this->modulo_info->duracion_meses;
                            $montoPago = $montoMensualConDescuento;
                        }
                    }
                    
                    Pago::create([
                        'matricula_id' => $matricula->id,
                        'tipo' => 'mensualidad',
                        'metodo_pago' => 'efectivo',
                        'monto' => $montoPago,
                        'monto_pagado' => $montoPago,
                        'cambio' => 0,
                        'mes_pagado' => $mesPagado,
                        'numero_transaccion' => 'MAT-' . $matricula->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'referencia_bancaria' => null,
                        'estado' => 'completado',
                        'fecha_pago' => $fechaPagoMes,
                        'observaciones' => $this->descuentoHabilitado ? 
                                         'Pago mensual #' . $i . ' con descuento del ' . $this->porcentaje_descuento . '%' : 
                                         'Pago mensual #' . $i,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                    
                    $matricula->fecha_ultimo_pago = $fechaPagoMes;
                }
                
                if ($this->meses_pagados > 0) {
                    $matricula->fecha_proximo_pago = Carbon::parse($matricula->fecha_ultimo_pago)->addMonth();
                    $matricula->save();
                }
            }
            
            $logData = [
                'Creado por' => Auth::user()->email,
                'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                'Módulo' => $matricula->modulo->nombre,
                'Estado' => $matricula->estado,
                'Precio total' => $matricula->precio_total_modulo,
                'Meses pagados inicialmente' => $this->meses_pagados,
                'Examen suficiencia' => $this->examenSuficienciaHabilitado ? 'Sí' : 'No',
            ];
            
            if ($this->descuentoHabilitado) {
                $logData['Descuento aplicado'] = $this->porcentaje_descuento . '%';
                $logData['Tipo descuento'] = $this->descuento_primer_mes ? 'Primer mes' : 'Total módulo';
                $logData['Monto descuento'] = 'L. ' . number_format($this->monto_descuento, 2);
                $logData['Precio original'] = 'L. ' . number_format($this->precio_original, 2);
            }
            
            LogService::activity('crear', 'Matrículas', "Se creó la matrícula #{$matricula->id}", $logData);

            $mensaje = 'Matrícula creada exitosamente. ';
            if ($this->meses_pagados > 0) {
                $mensaje .= "Se registraron {$this->meses_pagados} pago(s) mensual(es).";
            } else {
                $mensaje .= "No se registraron pagos iniciales.";
            }
            
            if ($this->examenSuficienciaHabilitado) {
                $mensaje .= " Examen de suficiencia habilitado.";
            }
            
            if ($this->descuentoHabilitado) {
                $mensaje .= " Descuento del {$this->porcentaje_descuento}% aplicado " . 
                           ($this->descuento_primer_mes ? 'al primer mes.' : 'al total del módulo.');
                $mensaje .= " Monto total con descuento: L. " . number_format($precioFinal, 2);
            }
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $mensaje
            ]);
            
            $this->closeModal();
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function update()
    {
        $this->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'modulo_id' => 'required|exists:modulos,id',
            'estado' => 'required|in:activa,completada,cancelada,pendiente',
            'precio_total_modulo' => 'required|numeric|min:0',
            'saldo_pendiente' => 'required|numeric|min:0',
            'meses_pendientes' => 'required|integer|min:0',
            'meses_pagados' => 'required|integer|min:0',
            'fecha_matricula' => 'required|date',
            'fecha_ultimo_pago' => 'nullable|date',
            'fecha_proximo_pago' => 'nullable|date',
            'aprobado' => 'boolean',
            'observaciones' => 'nullable|string',
            'matricula_anterior_id' => 'nullable|exists:matriculas,id',
        ]);

        try {
            $matricula = Matricula::findOrFail($this->matricula_id);
            
            $moduloCambiado = $matricula->modulo_id != $this->modulo_id;
            
            if ($moduloCambiado) {
                $fechaLimite = now()->subWeeks(2);
                
                $matriculaExistente = Matricula::where('estudiante_id', $this->estudiante_id)
                    ->where('modulo_id', $this->modulo_id)
                    ->where('id', '!=', $matricula->id)
                    ->whereIn('estado', ['activa', 'pendiente'])
                    ->whereHas('modulo', function ($query) use ($fechaLimite) {
                        $query->where(function ($subQuery) use ($fechaLimite) {
                            $subQuery->whereNull('fecha_fin')
                            ->orWhere('fecha_fin', '>=', $fechaLimite);
                        });
                    })
                    ->first();
                    
                if ($matriculaExistente) {
                    throw new \Exception('El estudiante ya tiene otra matrícula activa en este módulo.');
                }
            }
            
            $matricula->update([
                'estudiante_id' => $this->estudiante_id,
                'modulo_id' => $this->modulo_id,
                'estado' => $this->estado,
                'precio_total_modulo' => $this->precio_total_modulo,
                'saldo_pendiente' => $this->saldo_pendiente,
                'meses_pendientes' => $this->meses_pendientes,
                'meses_pagados' => $this->meses_pagados,
                'fecha_matricula' => $this->fecha_matricula,
                'fecha_ultimo_pago' => $this->fecha_ultimo_pago,
                'fecha_proximo_pago' => $this->fecha_proximo_pago,
                'aprobado' => $this->aprobado,
                'observaciones' => $this->observaciones,
                'matricula_anterior_id' => $this->matricula_anterior_id,
                'updated_by' => Auth::id(),
            ]);

            if ($moduloCambiado && $this->meses_pagados > 0) {
                Pago::where('matricula_id', $matricula->id)->delete();
                
                $nuevoModulo = Modulo::find($this->modulo_id);
                $precioMensual = $nuevoModulo->precio_mensual;
                
                for ($i = 1; $i <= $this->meses_pagados; $i++) {
                    $fechaPagoMes = Carbon::parse($this->fecha_matricula)
                        ->addMonths($i - 1)
                        ->format('Y-m-d');
                        
                    $mesPagado = Carbon::parse($this->fecha_matricula)
                        ->addMonths($i - 1)
                        ->format('Y-m');
                    
                    Pago::create([
                        'matricula_id' => $matricula->id,
                        'tipo' => 'mensualidad',
                        'metodo_pago' => 'efectivo',
                        'monto' => $precioMensual,
                        'monto_pagado' => $precioMensual,
                        'cambio' => 0,
                        'mes_pagado' => $mesPagado,
                        'numero_transaccion' => 'MAT-' . $matricula->id . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                        'referencia_bancaria' => null,
                        'estado' => 'completado',
                        'fecha_pago' => $fechaPagoMes,
                        'observaciones' => 'Pago mensual #' . $i,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]);
                }
            }

            LogService::activity(
                'actualizar',
                'Matrículas',
                "Se actualizó la matrícula #{$matricula->id}",
                [
                    'Actualizado por' => Auth::user()->email,
                    'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                    'Módulo' => $matricula->modulo->nombre,
                    'Estado' => $matricula->estado,
                    'Precio total' => $matricula->precio_total_modulo,
                    'Meses pagados' => $this->meses_pagados,
                    'Saldo pendiente' => $this->saldo_pendiente,
                ]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Matrícula actualizada exitosamente.'
            ]);
            
            $this->closeModal();
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al actualizar: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function confirmDelete($id)
    {
        try {
            $this->IdAEliminar = $id;
            $this->showDeleteModal = true;
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function delete($id)
    {
        try {
            $matricula = Matricula::findOrFail($id);
            $estudianteNombre = $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido;
            
            Pago::where('matricula_id', $matricula->id)->delete();
            
            $matricula->forceDelete();

            LogService::activity(
                'eliminar',
                'Matrículas',
                "Se eliminó la matrícula #{$matricula->id}",
                [
                    'Eliminado por' => Auth::user()->email,
                    'Estudiante' => $estudianteNombre,
                    'Módulo' => $matricula->modulo->nombre,
                ]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Matrícula eliminada correctamente.'
            ]);
            
            $this->resetPage();
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al eliminar: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }
    
    public function restore($id)
    {
        $matricula = Matricula::withTrashed()->findOrFail($id);
        $matricula->restore();

        LogService::activity(
            'restaurar',
            'Matrículas',
            "Se restauró la matrícula #{$matricula->id}",
            [
                'Restaurado por' => Auth::user()->email,
                'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                'Módulo' => $matricula->modulo->nombre,
            ]
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Matrícula restaurada correctamente.'
        ]);
    }

    public function forceDelete($id)
    {
        $matricula = Matricula::withTrashed()->findOrFail($id);
        $matriculaId = $matricula->id;
        $estudianteNombre = $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido;
        $matricula->forceDelete();

        LogService::activity(
            'eliminar_permanentemente',
            'Matrículas',
            "Se eliminó permanentemente la matrícula #{$matriculaId}",
            [
                'Eliminado por' => Auth::user()->email,
                'Estudiante' => $estudianteNombre,
            ]
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Matrícula eliminada permanentemente.'
        ]);
    }

    public function closeModal()
    {
        $this->resetInputFields();
        $this->isOpen = false;
        $this->showFinancialInfo = false;
    }

    public function closeExamenModal()
    {
        $this->showExamenModal = false;
        $this->examen_nota = '';
        $this->examen_fecha = now()->format('Y-m-d');
        $this->examen_observaciones = '';
    }

    public function closeDescuentoModal()
    {
        $this->showDescuentoModal = false;
        $this->porcentaje_descuento = 0;
        $this->descuento_observaciones = '';
        $this->precio_con_descuento = 0;
        $this->monto_descuento = 0;
        $this->precio_original = 0;
        $this->precio_mensual = 0;
        $this->descuento_primer_mes = true;
    }

    public function closeFinancialInfo()
    {
        $this->showFinancialInfo = false;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->IdAEliminar = null;
        $this->matriculaAEliminar = null;
    }

    private function resetInputFields()
    {
        $this->reset([
            'matricula_id',
            'estudiante_id',
            'modulo_id',
            'estado',
            'precio_total_modulo',
            'saldo_pendiente',
            'meses_pendientes',
            'meses_pagados',
            'fecha_matricula',
            'fecha_ultimo_pago',
            'fecha_proximo_pago',
            'aprobado',
            'observaciones',
            'matricula_anterior_id',
            'dni_busqueda',
            'estudiante_info',
            'modulo_info',
            'errorMessage',
            'showFinancialInfo',
            'examenSuficienciaHabilitado',
            'descuentoHabilitado',
            'examen_nota',
            'examen_fecha',
            'examen_observaciones',
            'porcentaje_descuento',
            'descuento_observaciones',
            'precio_con_descuento',
            'monto_descuento',
            'precio_original',
            'precio_mensual',
            'descuento_primer_mes',
            'matriculaSeleccionada',
            'monto_camiseta',
            'monto_gastos_graduacion',
        ]);
        $this->fecha_matricula = now()->format('Y-m-d');
        $this->examen_fecha = now()->format('Y-m-d');
        $this->estado = 'activa';
        $this->isEditing = false;
        $this->descuento_primer_mes = true;
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';

        $this->sortField = $field;
    }
    
    public function downloadPdf($moduloId)
    {
        $modulo = Modulo::with(['sede', 'modalidad'])->findOrFail($moduloId);

        $matriculas = Matricula::where('modulo_id', $moduloId)
            ->with('estudiante')
            ->get()
            ->sort(function ($a, $b) {
                if ($a->estudiante->sexo !== $b->estudiante->sexo) {
                    return $a->estudiante->sexo <=> $b->estudiante->sexo;
                }
                return $a->estudiante->nombre <=> $b->estudiante->nombre;
            });

        if ($matriculas->isEmpty()) {
            $this->dispatch('swal', [
                'title' => 'Aviso',
                'text' => 'No hay alumnos matriculados en este módulo.',
                'icon' => 'info'
            ]);
            return;
        }

        $data = [
            'modulo'     => $modulo,
            'matriculas' => $matriculas,
            'fecha'      => now('America/Tegucigalpa')->format('d/m/Y h:i A') 
        ];

        $pdf = Pdf::loadView('pdf.reporte-matricula-modulo', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "Matricula_Modulo_" . str_replace(' ', '_', $modulo->nombre) . ".pdf");
    }
    
    public function toggleEstado($id)
    {
        $matricula = Matricula::findOrFail($id);
        
        $nuevoEstado = $matricula->estado === 'activa' ? 'completada' : 'activa';
        
        $matricula->update([
            'estado' => $nuevoEstado,
            'updated_by' => Auth::id(),
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Estado cambiado a {$nuevoEstado}."
        ]);
    }

    public function incrementarDescuento()
    {
        $this->porcentaje_descuento = min(100, $this->porcentaje_descuento + 1);
        $this->calcularDescuento();
    }

    public function decrementarDescuento()
    {
        $this->porcentaje_descuento = max(0, $this->porcentaje_descuento - 1);
        $this->calcularDescuento();
    }

    public function setDescuento($porcentaje)
    {
        $this->porcentaje_descuento = $porcentaje;
        $this->calcularDescuento();
    }
}