<?php

namespace App\Livewire\PagosTutoria;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PagoTutoria;
use App\Models\MatriculaTutoria;
use App\Models\Tutoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\LogService;
use Barryvdh\DomPDF\Facade\Pdf;
class PagosTutorias extends Component
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
        'matricula_tutoria_id' => '',
        'matricula_info' => null,
        'tipo' => 'pago_unico',
        'metodo_pago' => 'efectivo',
        'monto' => 0.00,
        'monto_pagado' => 0.00,
        'cambio' => 0.00,
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
    
    public $tipos = ['pago_unico', 'adelanto', 'mensual', 'otros'];
    public $metodosPago = ['efectivo', 'tarjeta', 'transferencia', 'deposito', 'cheque'];
    public $estados = ['pendiente', 'completado', 'anulado', 'reembolsado'];
    
    public function mount()
    {
        $this->pagoTemplate['fecha_pago'] = now()->format('Y-m-d');
        $this->resetPagosForms();
    }
    public function downloadPagoTutoriaPdf($pagoId)
    {
        $pago = PagoTutoria::with(['matriculaTutoria.estudiante', 'matriculaTutoria.tutoria'])->findOrFail($pagoId);

        $data = [
            'pago'   => $pago,
            'tipo'   => 'TUTORÍA',
            'nombre' => $pago->matriculaTutoria->tutoria->nombre,
            'fecha'  => now('America/Tegucigalpa')->format('d/m/Y h:i A')
        ];

        $pdf = Pdf::loadView('pdf.recibo-pago', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "Recibo_Pago_Tutoria_" . $pago->id . ".pdf");
    }
    public function downloadReportePagosTutoria($tutoriaId)
    {
        $tutoria = Tutoria::with(['sede'])->findOrFail($tutoriaId);

        $pagos = PagoTutoria::whereHas('matriculaTutoria', function ($query) use ($tutoriaId) {
                $query->where('tutoria_id', $tutoriaId);
            })
            ->with(['matriculaTutoria.estudiante'])
            ->orderBy('fecha_pago', 'asc')
            ->get();

        $data = [
            'titulo' => 'REPORTE DE INGRESOS POR TUTORÍA',
            'subtitulo' => $tutoria->nombre,
            'entidad' => $tutoria,
            'pagos' => $pagos,
            'total' => $pagos->sum('monto_pagado'),
            'fecha' => now('America/Tegucigalpa')->format('d/m/Y h:i A')
        ];

        $pdf = Pdf::loadView('pdf.reporte-pagos-general', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "Reporte_Pagos_Tutoria_" . str_replace(' ', '_', $tutoria->nombre) . ".pdf");
    }
    public function resetPagosForms()
    {
        $this->pagosForms = [];
        for ($i = 0; $i < $this->numeroPagos; $i++) {
            $this->pagosForms[$i] = array_merge([], $this->pagoTemplate);
            $this->pagosForms[$i]['fecha_pago'] = now()->format('Y-m-d');
            $this->pagosForms[$i]['matricula_info'] = null;
        }
    }

    public function render()
    {
        $pagos = PagoTutoria::withTrashed()
            ->with(['matriculaTutoria.estudiante', 'matriculaTutoria.tutoria'])
            ->where(function ($q) {
                $q->whereHas('matriculaTutoria.estudiante', function ($q2) {
                    $q2->where('nombre', 'like', "%{$this->search}%")
                       ->orWhere('apellido', 'like', "%{$this->search}%")
                       ->orWhere('dni', 'like', "%{$this->search}%");
                })
                ->orWhereHas('matriculaTutoria.tutoria', function ($q2) {
                    $q2->where('nombre', 'like', "%{$this->search}%");
                })
                ->orWhere('numero_transaccion', 'like', "%{$this->search}%")
                ->orWhere('referencia_bancaria', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.pagos-tutoria.pagos-tutorias', compact('pagos'));
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
            $this->addForm();
        }
    }

    private function addForm()
    {
        $form = $this->pagoTemplate;
        $form['fecha_pago'] = now()->format('Y-m-d');
        $this->pagosForms[] = $form;
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
            
            if ($field === 'monto_pagado' || $field === 'monto') {
                $monto = (float) ($this->pagosForms[$index]['monto'] ?? 0);
                $montoPagado = (float) ($this->pagosForms[$index]['monto_pagado'] ?? 0);
                $this->pagosForms[$index]['cambio'] = max(0, $montoPagado - $monto);
            }
        }
    }
    
    public function procesarPago($index)
    {
        $form = $this->pagosForms[$index];

        if ((float)($form['monto'] ?? 0) <= 0) {
            $this->errorMessage = "El monto debe ser mayor a 0";
            $this->showErrorModal = true;
            return;
        }

        $this->validate([
            "pagosForms.$index.matricula_tutoria_id" => 'required|exists:matricula_tutorias,id',
            "pagosForms.$index.tipo" => 'required|in:pago_unico,adelanto,mensual,otros',
            "pagosForms.$index.metodo_pago" => 'required|in:efectivo,tarjeta,transferencia,deposito,cheque',
            "pagosForms.$index.monto" => 'required|numeric|gt:0',
            "pagosForms.$index.monto_pagado" => 'required|numeric|gte:pagosForms.' . $index . '.monto',
            "pagosForms.$index.estado" => 'required|in:pendiente,completado,anulado,reembolsado',
            "pagosForms.$index.fecha_pago" => 'required|date',
        ]);
        
        try {
            DB::transaction(function () use ($index) {
                $form = $this->pagosForms[$index];
                $matricula = MatriculaTutoria::lockForUpdate()->findOrFail($form['matricula_tutoria_id']);

                $pago = PagoTutoria::create([
                    'matricula_tutoria_id' => $form['matricula_tutoria_id'],
                    'tipo' => $form['tipo'],
                    'metodo_pago' => $form['metodo_pago'],
                    'monto' => $form['monto'],
                    'monto_pagado' => $form['monto_pagado'],
                    'cambio' => $form['cambio'],
                    'numero_transaccion' => $form['numero_transaccion'],
                    'referencia_bancaria' => $form['referencia_bancaria'],
                    'estado' => $form['estado'],
                    'fecha_pago' => $form['fecha_pago'],
                    'observaciones' => $form['observaciones'],
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);

                if ($form['estado'] === 'completado') {
                    $monto = (float)$form['monto'];
                    // Según tu esquema: tutorias_pagadas y saldo_pendiente
                    $precioHora = $matricula->precio_hora_aplicado > 0 ? $matricula->precio_hora_aplicado : ($matricula->tutoria->precio_hora ?? 0);
                    
                    $matricula->saldo_pendiente = max(0, $matricula->saldo_pendiente - $monto);
                    
                    if ($precioHora > 0) {
                        $tutoriasPagadas = floor($monto / $precioHora);
                        $matricula->tutorias_pagadas += $tutoriasPagadas;
                    }

                    if ($matricula->saldo_pendiente <= 0) {
                        $matricula->estado = 'completada';
                    }
                    
                    $matricula->updated_by = Auth::id();
                    $matricula->save();
                }

                LogService::activity('crear', 'PagosTutorias', "Pago registrado #{$pago->id} para matrícula {$matricula->id}");
            });

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Pago procesado correctamente.']);
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function buscarMatricula($index)
    {
        $id = $this->pagosForms[$index]['matricula_tutoria_id'];
        if (empty($id)) return;
        
        $matricula = MatriculaTutoria::with(['estudiante', 'tutoria'])->find($id);
        
        if ($matricula) {
            $this->pagosForms[$index]['matricula_info'] = $matricula;
            $this->pagosForms[$index]['monto'] = $matricula->saldo_pendiente;
            $this->pagosForms[$index]['monto_pagado'] = $matricula->saldo_pendiente;
        } else {
            $this->errorMessage = "Matrícula #{$id} no encontrada.";
            $this->showErrorModal = true;
        }
    }

    public function procesarTodosLosPagos()
    {
        $success = 0;
        foreach ($this->pagosForms as $index => $form) {
            if (!empty($form['matricula_tutoria_id'])) {
                $this->procesarPago($index);
                $success++;
            }
        }
        if ($success > 0) $this->closeModal();
    }

    public function edit(PagoTutoria $pago)
    {
        $this->isEditing = true;
        $this->isOpen = true;
        $this->numeroPagos = 1;
        $this->pagosForms = [[
            'id' => $pago->id,
            'matricula_tutoria_id' => $pago->matricula_tutoria_id,
            'matricula_info' => $pago->matriculaTutoria,
            'tipo' => $pago->tipo,
            'metodo_pago' => $pago->metodo_pago,
            'monto' => $pago->monto,
            'monto_pagado' => $pago->monto_pagado,
            'cambio' => $pago->cambio,
            'numero_transaccion' => $pago->numero_transaccion,
            'referencia_bancaria' => $pago->referencia_bancaria,
            'estado' => $pago->estado,
            'fecha_pago' => $pago->fecha_pago->format('Y-m-d'),
            'observaciones' => $pago->observaciones,
        ]];
    }

    public function update()
    {
        try {
            $data = $this->pagosForms[0];
            $pago = PagoTutoria::findOrFail($data['id']);
            $pago->update(array_merge($data, ['updated_by' => Auth::id()]));
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Pago actualizado.']);
            $this->closeModal();
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function confirmDelete($id)
    {
        $this->IdAEliminar = $id;
        $this->pagoAEliminar = PagoTutoria::find($id);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        PagoTutoria::findOrFail($this->IdAEliminar)->forceDelete();
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Eliminado permanentemente.']);
        $this->closeDeleteModal();
    }

    public function diagnosticarMatricula($matriculaId)
    {
        $m = MatriculaTutoria::with(['tutoria', 'pagos' => fn($q) => $q->where('estado', 'completado')])->findOrFail($matriculaId);
        $precio = $m->precio_hora_aplicado > 0 ? $m->precio_hora_aplicado : ($m->tutoria->precio_hora ?? 0);
        $totalPagado = $m->pagos->sum('monto');
        
        $this->errorMessage = "<strong>Diagnóstico:</strong><br>" .
                              "Matrícula: #{$m->id}<br>" .
                              "Saldo en BD: L. " . number_format($m->saldo_pendiente, 2) . "<br>" .
                              "Total Pagado: L. " . number_format($totalPagado, 2) . "<br>" .
                              "Tutorías Pagadas (Calculadas): " . ($precio > 0 ? floor($totalPagado / $precio) : 0);
        $this->showErrorModal = true;
    }

    public function corregirMatricula($matriculaId)
    {
        try {
            $m = MatriculaTutoria::with(['tutoria', 'pagos' => fn($q) => $q->where('estado', 'completado')])->findOrFail($matriculaId);
            $precio = $m->precio_hora_aplicado > 0 ? $m->precio_hora_aplicado : ($m->tutoria->precio_hora ?? 0);
            $pagado = $m->pagos->sum('monto');
            $totalEsperado = ($m->tutoria->total_horas ?? 0) * $precio;

            $m->update([
                'saldo_pendiente' => max(0, $totalEsperado - $pagado),
                'tutorias_pagadas' => $precio > 0 ? floor($pagado / $precio) : 0,
            ]);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Saldos corregidos.']);
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function restore($id) { PagoTutoria::withTrashed()->findOrFail($id)->restore(); $this->dispatch('notify', ['type' => 'success', 'message' => 'Restaurado.']); }
    public function hideError() { $this->showErrorModal = false; }
    public function closeModal() { $this->isOpen = false; $this->resetInputFields(); }
    public function closeDeleteModal() { $this->showDeleteModal = false; }
    private function resetInputFields() { $this->numeroPagos = 1; $this->resetPagosForms(); }
    public function sortBy($field) { $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc' ? 'desc' : 'asc'; $this->sortField = $field; }
}