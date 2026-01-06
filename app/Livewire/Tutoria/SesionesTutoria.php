<?php

namespace App\Livewire\Tutoria;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MatriculaTutoria;
use App\Models\SesionTutoria;
use App\Models\PagoTutoria;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SesionesTutoria extends Component
{
    use WithPagination;
    
    public $isOpen = false;
    public $showPagoModal = false;
    public $showRegistrarModal = false;
    public $matriculaId;
    public $matricula;
    public $sesionId;
    
    // Para crear nueva sesión
    public $fecha_sesion;
    public $hora_inicio;
    public $hora_fin;
    public $contenido = '';
    public $monto_sesion = 0;
    public $observaciones = '';
    
    // Para marcar asistencia/pago
    public $asistio = false;
    public $pagado = false;
    
    // Para crear pago
    public $metodo_pago = 'efectivo';
    public $monto_pagado = 0;
    public $cambio = 0;
    public $numero_transaccion = '';
    public $referencia_bancaria = '';
    public $observaciones_pago = '';
    
    public $search = '';
    public $perPage = 10;
    
    public function mount($matriculaId = null)
    {
        $this->matriculaId = $matriculaId;
        if ($matriculaId) {
            $this->matricula = MatriculaTutoria::with(['estudiante', 'tutoria'])->find($matriculaId);
        }
        $this->fecha_sesion = now()->format('Y-m-d');
        $this->hora_inicio = '09:00';
    }
    
    public function render()
    {
        $query = SesionTutoria::with(['matriculaTutoria.estudiante', 'matriculaTutoria.tutoria']);
        
        if ($this->matriculaId) {
            $query->where('matricula_tutoria_id', $this->matriculaId);
        }
        
        if ($this->search) {
            $query->where(function($q) {
                $q->whereHas('matriculaTutoria.estudiante', function($q2) {
                    $q2->where('nombre', 'like', "%{$this->search}%")
                       ->orWhere('apellido', 'like', "%{$this->search}%");
                })
                ->orWhere('contenido', 'like', "%{$this->search}%")
                ->orWhere('observaciones', 'like', "%{$this->search}%");
            });
        }
        
        $sesiones = $query->orderBy('fecha_sesion', 'desc')
                         ->paginate($this->perPage);
        
        return view('livewire.tutoria.sesiones-tutoria', compact('sesiones'));
    }
    
    public function openRegistrarModal()
    {
        $this->reset(['fecha_sesion', 'hora_inicio', 'hora_fin', 'contenido', 'monto_sesion', 'observaciones']);
        $this->fecha_sesion = now()->format('Y-m-d');
        $this->hora_inicio = '09:00';
        $this->monto_sesion = $this->matricula->precio_sesion_base ?? 0;
        $this->showRegistrarModal = true;
    }
    
    public function registrarSesion()
    {
        $this->validate([
            'fecha_sesion' => 'required|date',
            'hora_inicio' => 'nullable',
            'hora_fin' => 'nullable',
            'monto_sesion' => 'required|numeric|min:0',
            'contenido' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);
        
        $sesion = SesionTutoria::create([
            'matricula_tutoria_id' => $this->matriculaId,
            'fecha_sesion' => $this->fecha_sesion,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'contenido' => $this->contenido,
            'monto_sesion' => $this->monto_sesion,
            'observaciones' => $this->observaciones,
            'created_by' => Auth::id(),
        ]);
        
        // Incrementar saldo pendiente
        $this->matricula->increment('saldo_pendiente', $this->monto_sesion);
        
        session()->flash('message', 'Sesión registrada correctamente. Saldo pendiente actualizado.');
        $this->closeModals();
    }
    
    public function marcarAsistencia($sesionId, $asistio)
    {
        $sesion = SesionTutoria::find($sesionId);
        $sesion->update(['asistio' => $asistio]);
        
        $estado = $asistio ? 'asistió' : 'no asistió';
        session()->flash('message', "Se registró que el estudiante {$estado} a la sesión.");
    }
    
    public function openPagoModal($sesionId)
    {
        $this->sesionId = $sesionId;
        $sesion = SesionTutoria::find($sesionId);
        $this->monto_sesion = $sesion->monto_sesion;
        $this->monto_pagado = $sesion->monto_sesion;
        $this->showPagoModal = true;
    }
    
    public function registrarPago()
    {
        $this->validate([
            'monto_pagado' => 'required|numeric|min:' . $this->monto_sesion,
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,deposito,cheque',
            'numero_transaccion' => 'nullable|string',
            'referencia_bancaria' => 'nullable|string',
            'observaciones_pago' => 'nullable|string',
        ]);
        
        $sesion = SesionTutoria::find($this->sesionId);
        $cambio = $this->monto_pagado - $this->monto_sesion;
        
        // Registrar pago
        $pago = PagoTutoria::create([
            'matricula_tutoria_id' => $sesion->matricula_tutoria_id,
            'sesion_tutoria_id' => $this->sesionId,
            'tipo' => 'sesion',
            'metodo_pago' => $this->metodo_pago,
            'monto' => $this->monto_sesion,
            'monto_pagado' => $this->monto_pagado,
            'cambio' => $cambio,
            'numero_transaccion' => $this->numero_transaccion,
            'referencia_bancaria' => $this->referencia_bancaria,
            'estado' => 'completado',
            'fecha_pago' => now(),
            'observaciones' => $this->observaciones_pago,
            'created_by' => Auth::id(),
        ]);
        
        // Actualizar sesión como pagada
        $sesion->update(['pagado' => true]);
        
        // Actualizar saldo pendiente de la matrícula
        $matricula = MatriculaTutoria::find($sesion->matricula_tutoria_id);
        $nuevoSaldo = max(0, $matricula->saldo_pendiente - $this->monto_sesion);
        $matricula->update(['saldo_pendiente' => $nuevoSaldo]);
        
        session()->flash('message', 'Pago registrado correctamente. Saldo pendiente actualizado.');
        $this->closeModals();
    }
    
    public function closeModals()
    {
        $this->showPagoModal = false;
        $this->showRegistrarModal = false;
        $this->reset(['sesionId', 'monto_pagado', 'metodo_pago', 'numero_transaccion', 'referencia_bancaria', 'observaciones_pago']);
    }
    
    public function eliminarSesion($sesionId)
    {
        $sesion = SesionTutoria::find($sesionId);
        
        if ($sesion->pagado) {
            session()->flash('error', 'No se puede eliminar una sesión que ya tiene pago registrado.');
            return;
        }
        
        // Decrementar saldo pendiente si la sesión no estaba pagada
        if (!$sesion->pagado) {
            $matricula = $sesion->matriculaTutoria;
            $matricula->decrement('saldo_pendiente', $sesion->monto_sesion);
        }
        
        $sesion->delete();
        session()->flash('message', 'Sesión eliminada correctamente.');
    }
}