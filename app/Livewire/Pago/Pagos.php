<?php

namespace App\Livewire\Pago;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pago;
use App\Models\Matricula;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\LogService;
use App\Models\Modulo;
use Barryvdh\DomPDF\Facade\Pdf;
class Pagos extends Component
{
    use WithPagination;
    
    public $isEditing = false;
    public $isOpen = false;
    public $showDeleteModal = false;
    public $showErrorModal = false;
    public $errorMessage;

    public $numeroPagos = 1;
    public $pagosForms = [];
    
    public $pagoTemplate = [
        'id' => null,
        'matricula_id' => '',
        'matricula_info' => null,
        'tipo' => 'mensualidad',
        'metodo_pago' => 'efectivo',
        'monto' => 0,
        'monto_pagado' => 0,
        'cambio' => 0,
        'mes_pagado' => '',
        'numero_transaccion' => '',
        'referencia_bancaria' => '',
        'estado' => 'completado',
        'fecha_pago' => '',
        'observaciones' => '',
    ];
    
    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'desc';
    
    public $IdAEliminar;
    public $pagoAEliminar;
    
    public $tipos = ['mensualidad', 'matricula', 'pago_unico', 'adelanto', 'otros'];
    public $metodosPago = ['efectivo', 'tarjeta', 'transferencia', 'deposito', 'cheque'];
    public $estados = ['pendiente', 'completado', 'anulado', 'reembolsado'];
    
    public function mount()
    {
        $this->resetPagosForms();
        $this->pagoTemplate['fecha_pago'] = now()->format('Y-m-d');
        $this->pagoTemplate['mes_pagado'] = now()->format('Y-m');
    }
    public function downloadReportePagosModulo($moduloId)
    {
        $modulo = Modulo::with(['sede', 'modalidad'])->findOrFail($moduloId);

        // Obtenemos los pagos cuya matrícula pertenezca a este módulo
        $pagos = Pago::whereHas('matricula', function ($query) use ($moduloId) {
                $query->where('modulo_id', $moduloId);
            })
            ->with(['matricula.estudiante'])
            ->orderBy('fecha_pago', 'asc')
            ->get();

        $data = [
            'titulo' => 'REPORTE DE INGRESOS POR MÓDULO',
            'subtitulo' => $modulo->nombre,
            'entidad' => $modulo,
            'pagos' => $pagos,
            'total' => $pagos->sum('monto_pagado'),
            'fecha' => now('America/Tegucigalpa')->format('d/m/Y h:i A')
        ];

        $pdf = Pdf::loadView('pdf.reporte-pagos-general', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "Reporte_Pagos_Modulo_" . str_replace(' ', '_', $modulo->nombre) . ".pdf");
    }
    public function downloadPagoPdf($pagoId)
    {
        $pago = Pago::with(['matricula.estudiante', 'matricula.modulo'])->findOrFail($pagoId);

        $data = [
            'pago'   => $pago,
            'tipo'   => 'MÓDULO',
            'nombre' => $pago->matricula->modulo->nombre,
            'fecha'  => now('America/Tegucigalpa')->format('d/m/Y h:i A')
        ];

        $pdf = Pdf::loadView('pdf.recibo-pago', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "Recibo_Pago_Modulo_" . $pago->id . ".pdf");
    }
    public function resetPagosForms()
    {
        $this->pagosForms = [];
        for ($i = 0; $i < $this->numeroPagos; $i++) {
            $this->pagosForms[$i] = array_merge([], $this->pagoTemplate);
            $this->pagosForms[$i]['fecha_pago'] = now()->format('Y-m-d');
            $this->pagosForms[$i]['mes_pagado'] = now()->format('Y-m');
            $this->pagosForms[$i]['matricula_info'] = null;
        }
    }

    public function render()
    {
        $pagos = Pago::withTrashed()
            ->with(['matricula.estudiante', 'matricula.modulo'])
            ->where(function ($q) {
                $q->whereHas('matricula.estudiante', function ($q2) {
                    $q2->where('nombre', 'like', "%{$this->search}%")
                       ->orWhere('apellido', 'like', "%{$this->search}%")
                       ->orWhere('dni', 'like', "%{$this->search}%");
                })
                ->orWhereHas('matricula.modulo', function ($q2) {
                    $q2->where('nombre', 'like', "%{$this->search}%");
                })
                ->orWhere('numero_transaccion', 'like', "%{$this->search}%")
                ->orWhere('referencia_bancaria', 'like', "%{$this->search}%")
                ->orWhere('tipo', 'like', "%{$this->search}%")
                ->orWhere('metodo_pago', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.pago.pagos', compact('pagos'));
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isOpen = true;
        $this->resetPagosForms();
    }
    
    public function incrementarPagos()
    {
        if ($this->numeroPagos < 20) {
            $this->numeroPagos++;
            $this->pagosForms[] = array_merge([], $this->pagoTemplate);
            $this->pagosForms[count($this->pagosForms)-1]['fecha_pago'] = now()->format('Y-m-d');
            $this->pagosForms[count($this->pagosForms)-1]['mes_pagado'] = now()->format('Y-m');
            $this->pagosForms[count($this->pagosForms)-1]['matricula_info'] = null;
        }
    }
    
    public function decrementarPagos()
    {
        if ($this->numeroPagos > 1) {
            array_pop($this->pagosForms);
            $this->numeroPagos--;
        }
    }
        
    public function updatedPagosForms($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];
            
            if ($field === 'matricula_id' && empty($value)) {
                $this->pagosForms[$index]['matricula_info'] = null;
            }
            
            if ($field === 'monto_pagado') {
                $monto = (float) ($this->pagosForms[$index]['monto'] ?? 0);
                $montoPagado = (float) $value;
                
                if ($montoPagado > $monto) {
                    $this->pagosForms[$index]['cambio'] = $montoPagado - $monto;
                } else {
                    $this->pagosForms[$index]['cambio'] = 0;
                }
            }
            
            if ($field === 'tipo' && $value !== 'mensualidad') {
                $this->pagosForms[$index]['mes_pagado'] = '';
            }
        }
    }
    
    public function procesarPago($index)
    {
        if (empty($this->pagosForms[$index]['matricula_id'])) {
            $this->errorMessage = "Debe seleccionar una matrícula";
            $this->showErrorModal = true;
            return;
        }

        $matricula = Matricula::with(['modulo'])->find($this->pagosForms[$index]['matricula_id']);
        
        if (!$matricula) {
            $this->errorMessage = "La matrícula seleccionada no existe";
            $this->showErrorModal = true;
            return;
        }
        
        // VERIFICAR SI YA ESTÁ PAGADA COMPLETAMENTE
        if ($matricula->saldo_pendiente <= 0) {
            $this->errorMessage = "Esta matrícula ya está pagada completamente. Saldo pendiente: L. 0.00";
            $this->showErrorModal = true;
            return;
        }
        
        // VERIFICAR SI YA PAGÓ TODOS LOS MESES
        if ($matricula->meses_pagados >= $matricula->modulo->duracion_meses) {
            $this->errorMessage = "Esta matrícula ya tiene todos los meses pagados ({$matricula->meses_pagados}/{$matricula->modulo->duracion_meses}). No puede registrar más pagos mensuales.";
            $this->showErrorModal = true;
            return;
        }
        
        // Validar si ya está pagado el mes si es mensualidad
        if ($this->pagosForms[$index]['tipo'] === 'mensualidad') {
            $mesesPagados = Pago::where('matricula_id', $matricula->id)
                ->where('tipo', 'mensualidad')
                ->where('estado', 'completado')
                ->pluck('mes_pagado')
                ->toArray();
            
            if (empty($this->pagosForms[$index]['mes_pagado'])) {
                // Calcular próximo mes a pagar
                if ($matricula->fecha_ultimo_pago) {
                    $proximoMes = Carbon::parse($matricula->fecha_ultimo_pago)
                        ->addMonth()
                        ->format('Y-m');
                } else {
                    // Si no hay pagos anteriores, usar fecha de matrícula
                    $proximoMes = Carbon::parse($matricula->fecha_matricula)->format('Y-m');
                }
                $this->pagosForms[$index]['mes_pagado'] = $proximoMes;
            }
            
            $mesPagado = $this->pagosForms[$index]['mes_pagado'];
            
            if (in_array($mesPagado, $mesesPagados)) {
                $this->errorMessage = "El mes {$mesPagado} ya fue pagado para esta matrícula.";
                $this->showErrorModal = true;
                return;
            }
        }
        
        $this->validate([
            "pagosForms.$index.tipo" => 'required|in:mensualidad,matricula,pago_unico,adelanto,otros',
            "pagosForms.$index.metodo_pago" => 'required|in:efectivo,tarjeta,transferencia,deposito,cheque',
            "pagosForms.$index.monto" => 'required|numeric|min:0|max:' . ($matricula->saldo_pendiente + 1000),
            "pagosForms.$index.monto_pagado" => 'required|numeric|min:' . $this->pagosForms[$index]['monto'],
            "pagosForms.$index.cambio" => 'required|numeric|min:0',
            "pagosForms.$index.mes_pagado" => 'nullable|date_format:Y-m',
            "pagosForms.$index.numero_transaccion" => 'nullable|string|max:100',
            "pagosForms.$index.referencia_bancaria" => 'nullable|string|max:100',
            "pagosForms.$index.estado" => 'required|in:pendiente,completado,anulado,reembolsado',
            "pagosForms.$index.fecha_pago" => 'required|date',
            "pagosForms.$index.observaciones" => 'nullable|string',
        ]);
        
        try {
            $pago = Pago::create([
                'matricula_id' => $this->pagosForms[$index]['matricula_id'],
                'tipo' => $this->pagosForms[$index]['tipo'],
                'metodo_pago' => $this->pagosForms[$index]['metodo_pago'],
                'monto' => $this->pagosForms[$index]['monto'],
                'monto_pagado' => $this->pagosForms[$index]['monto_pagado'],
                'cambio' => $this->pagosForms[$index]['cambio'],
                'mes_pagado' => $this->pagosForms[$index]['mes_pagado'],
                'numero_transaccion' => $this->pagosForms[$index]['numero_transaccion'],
                'referencia_bancaria' => $this->pagosForms[$index]['referencia_bancaria'],
                'estado' => $this->pagosForms[$index]['estado'],
                'fecha_pago' => $this->pagosForms[$index]['fecha_pago'],
                'observaciones' => $this->pagosForms[$index]['observaciones'],
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
            
            if ($this->pagosForms[$index]['estado'] === 'completado') {
                $montoPago = $this->pagosForms[$index]['monto'];
                
                // Actualizar saldo de matrícula
                $matricula->saldo_pendiente = max(0, $matricula->saldo_pendiente - $montoPago);
                
                if ($this->pagosForms[$index]['tipo'] === 'mensualidad') {
                    // Contar como mes pagado
                    $matricula->meses_pagados += 1;
                    $matricula->meses_pendientes = max(0, $matricula->modulo->duracion_meses - $matricula->meses_pagados);
                    
                    $matricula->fecha_ultimo_pago = $pago->fecha_pago;
                    $matricula->fecha_proximo_pago = Carbon::parse($pago->fecha_pago)->addMonth();
                }
                
                if ($matricula->saldo_pendiente <= 0) {
                    $matricula->estado = 'completada';
                    $matricula->meses_pendientes = 0;
                    $matricula->fecha_proximo_pago = null;
                }
                
                $matricula->updated_by = Auth::id();
                $matricula->save();
            }
            
            LogService::activity(
                'crear',
                'Pagos',
                "Se creó el pago #{$pago->id}",
                [
                    'Creado por' => Auth::user()->email,
                    'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                    'Monto' => $pago->monto,
                    'Tipo' => $pago->tipo,
                    'Método' => $pago->metodo_pago,
                    'Saldo actualizado' => $matricula->saldo_pendiente,
                    'Meses pagados' => $matricula->meses_pagados,
                    'Meses pendientes' => $matricula->meses_pendientes,
                ]
            );
            
            $this->pagosForms[$index]['matricula_info'] = Matricula::with(['estudiante', 'modulo'])->find($matricula->id);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Pago registrado exitosamente. ' .
                            'Saldo actual: L. ' . number_format($matricula->saldo_pendiente, 2) . ' | ' .
                            'Meses pagados: ' . $matricula->meses_pagados . '/' . $matricula->modulo->duracion_meses
            ]);
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function getProximoMesAPagar($matriculaId)
    {
        $matricula = Matricula::with(['modulo', 'pagos' => function($q) {
            $q->where('tipo', 'mensualidad')
            ->where('estado', 'completado')
            ->orderBy('mes_pagado', 'desc');
        }])->find($matriculaId);
        
        if (!$matricula) {
            return null;
        }
        
        if ($matricula->pagos->isEmpty()) {
            return Carbon::parse($matricula->fecha_matricula)->format('Y-m');
        }
        
        $ultimoMesPagado = $matricula->pagos->first()->mes_pagado;
        
        return Carbon::createFromFormat('Y-m', $ultimoMesPagado)
            ->addMonth()
            ->format('Y-m');
    }

    public function buscarMatricula($index)
    {
        if (empty($this->pagosForms[$index]['matricula_id'])) {
            $this->errorMessage = "Por favor ingrese un ID de matrícula";
            $this->showErrorModal = true;
            return;
        }
        
        $matriculaId = $this->pagosForms[$index]['matricula_id'];
        
        $matricula = Matricula::with(['estudiante', 'modulo', 'pagos' => function($q) {
            $q->where('tipo', 'mensualidad')
            ->where('estado', 'completado');
        }])->find($matriculaId);
        
        if ($matricula) {
            $this->pagosForms[$index]['matricula_info'] = $matricula;
            
            $saldoPendiente = $matricula->saldo_pendiente;
            $precioMensual = $matricula->modulo->precio_mensual;
            $mesesPendientes = $matricula->meses_pendientes;
            
            $mesesYaPagados = $matricula->pagos->pluck('mes_pagado')->toArray();
            
            if (empty($this->pagosForms[$index]['monto']) || $this->pagosForms[$index]['monto'] == 0) {
                if ($mesesPendientes > 0 && $this->pagosForms[$index]['tipo'] === 'mensualidad') {
                    $this->pagosForms[$index]['monto'] = $precioMensual;
                } else {
                    $this->pagosForms[$index]['monto'] = $saldoPendiente > 0 ? $saldoPendiente : $precioMensual;
                }
                $this->pagosForms[$index]['monto_pagado'] = $this->pagosForms[$index]['monto'];
            }
            
            if ($this->pagosForms[$index]['tipo'] === 'mensualidad') {
                if (empty($this->pagosForms[$index]['mes_pagado'])) {
                    $proximoMes = $this->getProximoMesAPagar($matriculaId);
                    $this->pagosForms[$index]['mes_pagado'] = $proximoMes;
                }
                
                $mesSugerido = $this->pagosForms[$index]['mes_pagado'];
                if (in_array($mesSugerido, $mesesYaPagados)) {
                    $this->errorMessage = "El mes {$mesSugerido} ya está pagado. Próximo mes a pagar: " . $this->getProximoMesAPagar($matriculaId);
                    $this->showErrorModal = true;
                }
            }
            
            $mensajeInfo = 'Matrícula encontrada. ' .
                        'Meses pagados: ' . count($mesesYaPagados) . '/' . $matricula->modulo->duracion_meses . ' | ' .
                        'Saldo pendiente: L. ' . number_format($saldoPendiente, 2);
            
            if (!empty($mesesYaPagados)) {
                $mensajeInfo .= ' | Meses pagados: ' . implode(', ', $mesesYaPagados);
            }
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $mensajeInfo
            ]);
            
        } else {
            $this->pagosForms[$index]['matricula_info'] = null;
            $this->errorMessage = "Matrícula con ID {$matriculaId} no encontrada";
            $this->showErrorModal = true;
        }
    }

    public function store()
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
            $matriculaExistente = Matricula::where('estudiante_id', $this->estudiante_id)
                ->where('modulo_id', $this->modulo_id)
                ->whereIn('estado', ['activa', 'pendiente'])
                ->first();
                
            if ($matriculaExistente) {
                throw new \Exception('El estudiante ya tiene una matrícula activa en este módulo.');
            }
            
            if ($this->modulo_info && $this->modulo_info->modulo_requerido_id && !$this->matricula_anterior_id) {
                $moduloRequerido = Modulo::find($this->modulo_info->modulo_requerido_id);
                throw new \Exception("Este módulo requiere haber completado y aprobado el módulo '{$moduloRequerido->nombre}'.");
            }
            
            $matricula = Matricula::create([
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
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $precioMensual = $matricula->modulo->precio_mensual;
            $mesPagado = Carbon::parse($matricula->fecha_matricula)->format('Y-m');
            
            Pago::create([
                'matricula_id' => $matricula->id,
                'tipo' => 'mensualidad',
                'metodo_pago' => 'efectivo',
                'monto' => $precioMensual,
                'monto_pagado' => $precioMensual,
                'cambio' => 0,
                'mes_pagado' => $mesPagado,
                'numero_transaccion' => 'MAT-' . $matricula->id . '-001',
                'referencia_bancaria' => null,
                'estado' => 'completado',
                'fecha_pago' => $matricula->fecha_matricula,
                'observaciones' => 'Pago inicial al matricularse',
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $matricula->update([
                'meses_pagados' => 1,
                'meses_pendientes' => $matricula->modulo->duracion_meses - 1,
                'saldo_pendiente' => $matricula->precio_total_modulo - $precioMensual,
                'fecha_ultimo_pago' => $matricula->fecha_matricula,
                'fecha_proximo_pago' => Carbon::parse($matricula->fecha_matricula)->addMonth(),
            ]);

            LogService::activity(
                'crear',
                'Matrículas',
                "Se creó la matrícula #{$matricula->id}",
                [
                    'Creado por' => Auth::user()->email,
                    'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                    'Módulo' => $matricula->modulo->nombre,
                    'Estado' => $matricula->estado,
                    'Precio total' => $matricula->precio_total_modulo,
                    'Primer mes pagado' => 'Sí',
                ]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Matrícula creada exitosamente. Se registró el pago del primer mes.'
            ]);
            
            $this->closeModal();
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }
        
    public function procesarTodosLosPagos()
    {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($this->pagosForms as $index => $form) {
            if (!empty($form['matricula_id'])) {
                try {
                    $this->procesarPago($index);
                    $successCount++;
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Formulario {$index}: " . $e->getMessage();
                }
            }
        }
        
        if ($errorCount > 0) {
            $this->errorMessage = "Se procesaron {$successCount} pagos correctamente. Errores: " . implode(', ', $errors);
            $this->showErrorModal = true;
        } else if ($successCount > 0) {
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Se procesaron {$successCount} pagos correctamente."
            ]);
            $this->closeModal();
        } else {
            $this->errorMessage = "No hay pagos para procesar. Complete al menos un formulario.";
            $this->showErrorModal = true;
        }
    }

    public function edit(Pago $pago)
    {
        $this->isEditing = true;
        $this->isOpen = true;
        
        $this->numeroPagos = 1;
        $this->pagosForms[0] = [
            'id' => $pago->id,
            'matricula_id' => $pago->matricula_id,
            'matricula_info' => $pago->matricula,
            'tipo' => $pago->tipo,
            'metodo_pago' => $pago->metodo_pago,
            'monto' => $pago->monto,
            'monto_pagado' => $pago->monto_pagado,
            'cambio' => $pago->cambio,
            'mes_pagado' => $pago->mes_pagado,
            'numero_transaccion' => $pago->numero_transaccion,
            'referencia_bancaria' => $pago->referencia_bancaria,
            'estado' => $pago->estado,
            'fecha_pago' => $pago->fecha_pago->format('Y-m-d'),
            'observaciones' => $pago->observaciones,
        ];
    }

    public function update()
    {
        $this->validate([
            "pagosForms.0.matricula_id" => 'required|exists:matriculas,id',
            "pagosForms.0.tipo" => 'required|in:mensualidad,matricula,pago_unico,adelanto,otros',
            "pagosForms.0.metodo_pago" => 'required|in:efectivo,tarjeta,transferencia,deposito,cheque',
            "pagosForms.0.monto" => 'required|numeric|min:0',
            "pagosForms.0.monto_pagado" => 'required|numeric|min:0',
            "pagosForms.0.cambio" => 'required|numeric|min:0',
            "pagosForms.0.mes_pagado" => 'nullable|date_format:Y-m',
            "pagosForms.0.numero_transaccion" => 'nullable|string|max:100',
            "pagosForms.0.referencia_bancaria" => 'nullable|string|max:100',
            "pagosForms.0.estado" => 'required|in:pendiente,completado,anulado,reembolsado',
            "pagosForms.0.fecha_pago" => 'required|date',
            "pagosForms.0.observaciones" => 'nullable|string',
        ]);

        try {
            $pago = Pago::findOrFail($this->pagosForms[0]['id']);
            $oldEstado = $pago->estado;
            $oldTipo = $pago->tipo;
            $oldMonto = $pago->monto;
            
            $pago->update([
                'matricula_id' => $this->pagosForms[0]['matricula_id'],
                'tipo' => $this->pagosForms[0]['tipo'],
                'metodo_pago' => $this->pagosForms[0]['metodo_pago'],
                'monto' => $this->pagosForms[0]['monto'],
                'monto_pagado' => $this->pagosForms[0]['monto_pagado'],
                'cambio' => $this->pagosForms[0]['cambio'],
                'mes_pagado' => $this->pagosForms[0]['mes_pagado'],
                'numero_transaccion' => $this->pagosForms[0]['numero_transaccion'],
                'referencia_bancaria' => $this->pagosForms[0]['referencia_bancaria'],
                'estado' => $this->pagosForms[0]['estado'],
                'fecha_pago' => $this->pagosForms[0]['fecha_pago'],
                'observaciones' => $this->pagosForms[0]['observaciones'],
                'updated_by' => Auth::id(),
            ]);
            
            $matricula = Matricula::find($pago->matricula_id);
            
            if ($matricula && $pago->tipo === 'mensualidad') {
                if ($oldEstado === 'completado' && $pago->estado !== 'completado') {
                    $matricula->meses_pagados = max(0, $matricula->meses_pagados - 1);
                    $matricula->meses_pendientes += 1;
                    $matricula->saldo_pendiente += $oldMonto;
                    $matricula->save();
                } elseif ($oldEstado !== 'completado' && $pago->estado === 'completado') {
                    $matricula->meses_pagados += 1;
                    $matricula->meses_pendientes = max(0, $matricula->meses_pendientes - 1);
                    $matricula->saldo_pendiente = max(0, $matricula->saldo_pendiente - $pago->monto);
                    $matricula->fecha_ultimo_pago = $pago->fecha_pago;
                    $matricula->fecha_proximo_pago = Carbon::parse($pago->fecha_pago)->addMonth();
                    $matricula->save();
                }
            }

            LogService::activity(
                'actualizar',
                'Pagos',
                "Se actualizó el pago #{$pago->id}",
                [
                    'Actualizado por' => Auth::user()->email,
                    'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                    'Monto' => $pago->monto,
                    'Tipo' => $pago->tipo_formateado,
                    'Estado' => $pago->estado_formateado,
                ]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Pago actualizado exitosamente.'
            ]);
            
            $this->closeModal();
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function confirmDelete($id)
    {
        $pago = Pago::findOrFail($id);
        $this->IdAEliminar = $id;
        $this->pagoAEliminar = $pago;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $pago = Pago::findOrFail($this->IdAEliminar);
        $matriculaNombre = $pago->matricula->estudiante->nombre . ' ' . $pago->matricula->estudiante->apellido;
        
        if ($pago->tipo === 'mensualidad' && $pago->estado === 'completado') {
            $matricula = $pago->matricula;
            $matricula->meses_pagados = max(0, $matricula->meses_pagados - 1);
            $matricula->meses_pendientes += 1;
            $matricula->saldo_pendiente += $pago->monto;
            $matricula->save();
        }
        
        $pago->forceDelete();

        LogService::activity(
            'eliminar',
            'Pagos',
            "Se eliminó el pago #{$pago->id}",
            [
                'Eliminado por' => Auth::user()->email,
                'Estudiante' => $matriculaNombre,
                'Monto' => $pago->monto,
                'Tipo' => $pago->tipo_formateado,
            ]
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Pago eliminado.'
        ]);
        
        $this->closeDeleteModal();
    }

    public function restore($id)
    {
        $pago = Pago::withTrashed()->findOrFail($id);
        $pago->restore();

        LogService::activity(
            'restaurar',
            'Pagos',
            "Se restauró el pago #{$pago->id}",
            [
                'Restaurado por' => Auth::user()->email,
                'Estudiante' => $pago->matricula->estudiante->nombre . ' ' . $pago->matricula->estudiante->apellido,
                'Monto' => $pago->monto,
            ]
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Pago restaurado correctamente.'
        ]);
    }

    public function forceDelete($id)
    {
        $pago = Pago::withTrashed()->findOrFail($id);
        $pagoId = $pago->id;
        $estudianteNombre = $pago->matricula->estudiante->nombre . ' ' . $pago->matricula->estudiante->apellido;
        $pago->forceDelete();

        LogService::activity(
            'eliminar_permanentemente',
            'Pagos',
            "Se eliminó permanentemente el pago #{$pagoId}",
            [
                'Eliminado por' => Auth::user()->email,
                'Estudiante' => $estudianteNombre,
            ]
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Pago eliminado permanentemente.'
        ]);
    }

    public function hideError()
    {
        $this->showErrorModal = false;
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
        $this->pagoAEliminar = null;
    }

    private function resetInputFields()
    {
        $this->reset([
            'numeroPagos',
            'pagosForms',
            'errorMessage',
        ]);
        $this->numeroPagos = 1;
        $this->resetPagosForms();
        $this->isEditing = false;
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';

        $this->sortField = $field;
    }

    public function diagnosticarMatricula($matriculaId)
    {
        try {
            $matricula = Matricula::with(['modulo', 'pagos' => function($q) {
                $q->where('estado', 'completado');
            }])->findOrFail($matriculaId);
            
            $diagnostico = [
                'matricula_id' => $matricula->id,
                'estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                'modulo' => $matricula->modulo->nombre,
                'duracion_modulo' => $matricula->modulo->duracion_meses,
                'precio_mensual_modulo' => $matricula->modulo->precio_mensual,
                'precio_total_esperado' => $matricula->modulo->precio_mensual * $matricula->modulo->duracion_meses,
                'precio_total_actual' => $matricula->precio_total_modulo,
                'saldo_pendiente' => $matricula->saldo_pendiente,
                'meses_pagados' => $matricula->meses_pagados,
                'meses_pendientes' => $matricula->meses_pendientes,
                'meses_totales_calculados' => $matricula->meses_pagados + $matricula->meses_pendientes,
                'pagos_completados' => $matricula->pagos->count(),
                'monto_total_pagado' => $matricula->pagos->sum('monto'),
            ];
            
            $this->errorMessage = "DIAGNÓSTICO:<br>" . 
                                 "Módulo: {$diagnostico['modulo']}<br>" .
                                 "Duración configurada: {$diagnostico['duracion_modulo']} meses<br>" .
                                 "Precio mensual: L. " . number_format($diagnostico['precio_mensual_modulo'], 2) . "<br>" .
                                 "Total esperado: L. " . number_format($diagnostico['precio_total_esperado'], 2) . "<br>" .
                                 "Total actual en matrícula: L. " . number_format($diagnostico['precio_total_actual'], 2) . "<br>" .
                                 "Saldo pendiente: L. " . number_format($diagnostico['saldo_pendiente'], 2) . "<br>" .
                                 "Meses pagados: {$diagnostico['meses_pagados']}<br>" .
                                 "Meses pendientes: {$diagnostico['meses_pendientes']}<br>" .
                                 "Meses totales (calculados): {$diagnostico['meses_totales_calculados']}<br>" .
                                 "Pagos registrados: {$diagnostico['pagos_completados']}<br>" .
                                 "Monto pagado: L. " . number_format($diagnostico['monto_total_pagado'], 2);
            
            $this->showErrorModal = true;
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error en diagnóstico: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function corregirMatricula($matriculaId)
    {
        try {
            $matricula = Matricula::with(['modulo', 'pagos' => function($q) {
                $q->where('estado', 'completado');
            }])->findOrFail($matriculaId);
            
            $precioTotalCorrecto = $matricula->modulo->precio_mensual * $matricula->modulo->duracion_meses;
            
            $mesesPagados = $matricula->pagos
                ->where('tipo', 'mensualidad')
                ->where('estado', 'completado')
                ->unique('mes_pagado')
                ->count();
            
            $montoPagado = $matricula->pagos
                ->where('estado', 'completado')
                ->sum('monto');
            
            $saldoPendiente = max(0, $precioTotalCorrecto - $montoPagado);
            
            $mesesPendientes = max(0, $matricula->modulo->duracion_meses - $mesesPagados);
            
            $ultimoPago = $matricula->pagos
                ->where('estado', 'completado')
                ->sortByDesc('fecha_pago')
                ->first();
            
            $matricula->update([
                'precio_total_modulo' => $precioTotalCorrecto,
                'meses_pagados' => $mesesPagados,
                'meses_pendientes' => $mesesPendientes,
                'saldo_pendiente' => $saldoPendiente,
                'fecha_ultimo_pago' => $ultimoPago ? $ultimoPago->fecha_pago : null,
                'fecha_proximo_pago' => $ultimoPago ? Carbon::parse($ultimoPago->fecha_pago)->addMonth() : 
                    Carbon::parse($matricula->fecha_matricula)->addMonth(),
            ]);
            
            if ($saldoPendiente <= 0 && $mesesPendientes <= 0) {
                $matricula->estado = 'completada';
                $matricula->save();
            }
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Matrícula corregida. ' .
                            'Total módulo: L. ' . number_format($precioTotalCorrecto, 2) . ' | ' .
                            'Saldo: L. ' . number_format($saldoPendiente, 2) . ' | ' .
                            'Meses: ' . $mesesPagados . '/' . $matricula->modulo->duracion_meses
            ]);
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al corregir: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }
}