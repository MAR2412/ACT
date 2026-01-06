<?php

namespace App\Livewire\Matricula;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Estudiante;
use App\Models\Tutoria;
use App\Models\MatriculaTutoria;
use App\Models\PagoTutoria;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;
use Barryvdh\DomPDF\Facade\Pdf;
class MatriculaTutorias extends Component
{
    use WithPagination;
    
    public $isEditing = false;
    public $isOpen = false;
    public $showDeleteModal = false;
    public $showErrorModal = false;
    public $showRegistrarTutoriaModal = false;
    public $errorMessage;
    public $matricula_id = null;
    public $estudiante_id = '';
    public $tutoria_id = '';
    public $estado = 'activa';
    public $precio_hora_aplicado = 0;
    public $saldo_pendiente = 0;
    public $tutorias_registradas = 0;
    public $tutorias_pagadas = 0;
    public $fecha_inicio;
    public $aprobado = false;
    public $observaciones = '';
    
    // Para registrar tutoría
    public $registrarPagada = false;
    public $metodo_pago = 'efectivo';
    public $numero_transaccion = '';
    public $referencia_bancaria = '';
    public $fecha_pago;
    
    public $dni_busqueda = '';
    public $estudiante_info = null;
    public $tutoria_info = null;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $IdAEliminar;
    public $matriculaAEliminar;
    public $tutorias = [];
    public $estados = ['activa', 'completada', 'cancelada', 'pendiente'];
    public function downloadPdf($tutoriaId)
    {
        $tutoria = Tutoria::with(['sede', 'modalidad'])->findOrFail($tutoriaId);

      
        $matriculas = MatriculaTutoria::where('tutoria_id', $tutoriaId)
            ->with('estudiante')
            ->get()
            ->sort(function ($a, $b) {
                if ($a->estudiante->sexo !== $b->estudiante->sexo) {
                    return $a->estudiante->sexo <=> $b->estudiante->sexo;
                }
                else if ($a->estudiante->nombre !== $b->estudiante->nombre){
                    return $a->estudiante->nombre <=> $b->estudiante->nombre;
                }
                return $a->estudiante->apellido <=> $b->estudiante->apellido;
            });

        $data = [
            'tutoria'    => $tutoria,
            'matriculas' => $matriculas,
            'fecha'      => now('America/Tegucigalpa')->format('d/m/Y h:i A') 
        ];

        $pdf = Pdf::loadView('pdf.reporte-matricula-tutoria', $data);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "Matricula_" . str_replace(' ', '_', $tutoria->nombre) . ".pdf");
    }
    public function mount()
    {
        $this->fecha_inicio = now()->format('Y-m-d');
        $this->fecha_pago = now()->format('Y-m-d');
        
        $this->tutorias = Tutoria::where('estado', true)
            ->with(['sede', 'modalidad', 'seccion'])
            ->orderBy('nombre')
            ->get();
    }
    
    public function render()
    {
        $matriculas = MatriculaTutoria::withTrashed()
            ->with(['estudiante', 'tutoria.sede', 'tutoria.modalidad', 'tutoria.seccion'])
            ->where(function ($q) {
                $q->whereHas('estudiante', function ($q2) {
                    $q2->where('nombre', 'like', "%{$this->search}%")
                       ->orWhere('apellido', 'like', "%{$this->search}%")
                       ->orWhere('dni', 'like', "%{$this->search}%");
                })
                ->orWhereHas('tutoria', function ($q2) {
                    $q2->where('nombre', 'like', "%{$this->search}%");
                })
                ->orWhere('estado', 'like', "%{$this->search}%")
                ->orWhere('observaciones', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
        
        return view('livewire.matricula.matricula-tutorias', compact('matriculas'));
    }
    
    public function openModal()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isOpen = true;
    }
    
    public function openRegistrarTutoriaModal($matriculaId)
    {
        $this->matricula_id = $matriculaId;
        $matricula = MatriculaTutoria::find($matriculaId);
        $this->precio_hora_aplicado = $matricula->precio_hora_aplicado;
        $this->showRegistrarTutoriaModal = true;
    }
    
    public function edit(MatriculaTutoria $matricula)
    {
        $this->isEditing = true;
        $this->isOpen = true;
        
        $this->matricula_id = $matricula->id;
        $this->estudiante_id = $matricula->estudiante_id;
        $this->tutoria_id = $matricula->tutoria_id;
        $this->estado = $matricula->estado;
        $this->precio_hora_aplicado = $matricula->precio_hora_aplicado;
        $this->saldo_pendiente = $matricula->saldo_pendiente;
        $this->tutorias_registradas = $matricula->tutorias_registradas;
        $this->tutorias_pagadas = $matricula->tutorias_pagadas;
        $this->fecha_inicio = $matricula->fecha_inicio->format('Y-m-d');
        $this->aprobado = $matricula->aprobado;
        $this->observaciones = $matricula->observaciones;
        
        $this->estudiante_info = $matricula->estudiante;
        $this->dni_busqueda = $matricula->estudiante->dni;
        
        $this->tutoria_info = Tutoria::with(['sede', 'modalidad', 'seccion'])->find($matricula->tutoria_id);
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
            
            $matriculaActiva = MatriculaTutoria::where('estudiante_id', $estudiante->id)
                ->whereIn('estado', ['activa', 'pendiente'])
                ->first();
            
            if ($matriculaActiva) {
                $this->errorMessage = "El estudiante ya está matriculado en la tutoría '{$matriculaActiva->tutoria->nombre}'. No puede matricularse en otra tutoría hasta que finalice esta.";
                $this->showErrorModal = true;
                $this->estudiante_info = null;
                $this->estudiante_id = '';
                return;
            }
            
        } else {
            $this->estudiante_info = null;
            $this->estudiante_id = '';
            $this->errorMessage = "Estudiante con DNI {$this->dni_busqueda} no encontrado o está inactivo.";
            $this->showErrorModal = true;
        }
    }
    
    public function updatedTutoriaId($value)
    {
        if ($value) {
            $tutoria = Tutoria::with(['sede', 'modalidad', 'seccion'])->find($value);
            if ($tutoria) {
                $this->tutoria_info = $tutoria;
                $this->precio_hora_aplicado = $tutoria->precio_hora;
                
                if ($this->estudiante_info) {
                    $matriculaExistente = MatriculaTutoria::where('estudiante_id', $this->estudiante_info->id)
                        ->where('tutoria_id', $tutoria->id)
                        ->whereIn('estado', ['activa', 'pendiente'])
                        ->first();
                    
                    if ($matriculaExistente) {
                        $this->errorMessage = 'El estudiante ya está matriculado en esta tutoría.';
                        $this->showErrorModal = true;
                    }
                }
            }
        } else {
            $this->resetTutoriaInfo();
        }
    }
    
    private function resetTutoriaInfo()
    {
        $this->tutoria_info = null;
        $this->precio_hora_aplicado = 0;
    }
    
    public function store()
    {
        $this->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'tutoria_id' => 'required|exists:tutorias,id',
            'estado' => 'required|in:activa,completada,cancelada,pendiente',
            'precio_hora_aplicado' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'aprobado' => 'boolean',
            'observaciones' => 'nullable|string',
        ]);
        
        try {
            $matriculaExistente = MatriculaTutoria::where('estudiante_id', $this->estudiante_id)
                ->where('tutoria_id', $this->tutoria_id)
                ->whereIn('estado', ['activa', 'pendiente'])
                ->first();
                
            if ($matriculaExistente) {
                throw new \Exception('El estudiante ya tiene una matrícula activa en esta tutoría.');
            }
            
            $matricula = MatriculaTutoria::create([
                'estudiante_id' => $this->estudiante_id,
                'tutoria_id' => $this->tutoria_id,
                'estado' => $this->estado,
                'precio_hora_aplicado' => $this->precio_hora_aplicado,
                'saldo_pendiente' => 0,
                'tutorias_registradas' => 0,
                'tutorias_pagadas' => 0,
                'fecha_inicio' => $this->fecha_inicio,
                'aprobado' => $this->aprobado,
                'observaciones' => $this->observaciones,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            LogService::activity(
                'crear',
                'MatrículaTutorias',
                "Se creó la matrícula de tutoría #{$matricula->id}",
                [
                    'Creado por' => Auth::user()->email,
                    'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                    'Tutoría' => $matricula->tutoria->nombre,
                    'Estado' => $matricula->estado,
                    'Precio por hora' => 'L. ' . number_format($matricula->precio_hora_aplicado, 2),
                ]
            );
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Matrícula creada exitosamente.'
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
            'tutoria_id' => 'required|exists:tutorias,id',
            'estado' => 'required|in:activa,completada,cancelada,pendiente',
            'precio_hora_aplicado' => 'required|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'aprobado' => 'boolean',
            'observaciones' => 'nullable|string',
        ]);
        
        try {
            $matricula = MatriculaTutoria::findOrFail($this->matricula_id);
            
            // Verificar si cambió la tutoría
            $tutoriaCambiada = $matricula->tutoria_id != $this->tutoria_id;
            
            if ($tutoriaCambiada) {
                $matriculaExistente = MatriculaTutoria::where('estudiante_id', $this->estudiante_id)
                    ->where('tutoria_id', $this->tutoria_id)
                    ->where('id', '!=', $matricula->id)
                    ->whereIn('estado', ['activa', 'pendiente'])
                    ->first();
                    
                if ($matriculaExistente) {
                    throw new \Exception('El estudiante ya tiene otra matrícula activa en esta tutoría.');
                }
            }
            
            // Actualizar la matrícula
            $matricula->update([
                'estudiante_id' => $this->estudiante_id,
                'tutoria_id' => $this->tutoria_id,
                'estado' => $this->estado,
                'precio_hora_aplicado' => $this->precio_hora_aplicado,
                'fecha_inicio' => $this->fecha_inicio,
                'aprobado' => $this->aprobado,
                'observaciones' => $this->observaciones,
                'updated_by' => Auth::id(),
            ]);
            
            LogService::activity(
                'actualizar',
                'MatrículaTutorias',
                "Se actualizó la matrícula de tutoría #{$matricula->id}",
                [
                    'Actualizado por' => Auth::user()->email,
                    'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                    'Tutoría' => $matricula->tutoria->nombre,
                    'Estado' => $matricula->estado,
                    'Precio por hora' => 'L. ' . number_format($matricula->precio_hora_aplicado, 2),
                    'Tutorías registradas' => $matricula->tutorias_registradas,
                    'Tutorías pagadas' => $matricula->tutorias_pagadas,
                    'Saldo pendiente' => 'L. ' . number_format($matricula->saldo_pendiente, 2),
                ]
            );
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Matrícula de tutoría actualizada exitosamente.'
            ]);
            
            $this->closeModal();
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al actualizar: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }
    
   // 1. Agrega esta propiedad al inicio de la clase
    public $cantidad_horas = 1; 


    public function registrarTutoria()
    {
        $this->validate([
            'cantidad_horas' => 'required|numeric|min:1', // Nueva validación
            'registrarPagada' => 'required|boolean',
            'metodo_pago' => 'required_if:registrarPagada,true|in:efectivo,tarjeta,transferencia,deposito,cheque',
            'numero_transaccion' => 'nullable|string',
            'referencia_bancaria' => 'nullable|string',
            'fecha_pago' => 'required_if:registrarPagada,true|date',
        ]);
        
        try {
            $matricula = MatriculaTutoria::findOrFail($this->matricula_id);
            
            // Calculamos el monto total basado en las horas
            $montoTotal = $matricula->precio_hora_aplicado * $this->cantidad_horas;

            // IMPORTANTE: Tu modelo debe aceptar la cantidad de horas en registrarTutoria
            // Si tu método en el modelo no acepta parámetros, deberías modificarlo para recibir $this->cantidad_horas
            $matricula->registrarTutoria($this->registrarPagada, $this->cantidad_horas);
            
            if ($this->registrarPagada) {
                PagoTutoria::create([
                    'matricula_tutoria_id' => $matricula->id,
                    'tipo' => 'adelanto',
                    'metodo_pago' => $this->metodo_pago,
                    'monto' => $montoTotal, // Usamos el total calculado
                    'monto_pagado' => $montoTotal,
                    'cambio' => 0,
                    'numero_transaccion' => $this->numero_transaccion,
                    'referencia_bancaria' => $this->referencia_bancaria,
                    'estado' => 'completado',
                    'fecha_pago' => $this->fecha_pago,
                    'observaciones' => "Pago por {$this->cantidad_horas} hora(s) de tutoría registrada",
                    'created_by' => Auth::id(),
                    'updated_by' => Auth::id(),
                ]);
                
                LogService::activity(
                    'pagar_tutoria',
                    'MatrículaTutorias',
                    "Se registró y pagó {$this->cantidad_horas} horas para la matrícula #{$matricula->id}",
                    [
                        'Horas' => $this->cantidad_horas,
                        'Monto Total' => 'L. ' . number_format($montoTotal, 2),
                        'Método' => $this->metodo_pago,
                    ]
                );
                
                $mensaje = "Tutoría ({$this->cantidad_horas} hrs) registrada y pagada exitosamente.";
            } else {
                LogService::activity(
                    'registrar_tutoria',
                    'MatrículaTutorias',
                    "Se registró una tutoría pendiente de pago para la matrícula #{$matricula->id}",
                    [
                        'Registrado por' => Auth::user()->email,
                        'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                        'Tutoría' => $matricula->tutoria->nombre,
                        'Monto pendiente' => 'L. ' . number_format($matricula->precio_hora_aplicado, 2),
                        'Saldo total pendiente' => 'L. ' . number_format($matricula->saldo_pendiente, 2),
                    ]
                );
                
                $mensaje = "Tutoría registrada pendiente de pago. Saldo pendiente: L. " . number_format($matricula->saldo_pendiente, 2);
            }
            
            $this->dispatch('notify', ['type' => 'success', 'message' => $mensaje]);
            $this->closeRegistrarTutoriaModal();
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
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
    public function save()
    {
        if ($this->isEditing) {
            $this->update();
        } else {
            $this->store();
        }
    }

    public function delete($id = null)
    {
        
        $idFinal = $id ?? $this->IdAEliminar;

        try {
         
            $matricula = MatriculaTutoria::withTrashed()->find($idFinal);

            if (!$matricula) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'El registro ya no existe.'
                ]);
                return;
            }

            
            \App\Models\PagoTutoria::where('matricula_tutoria_id', $idFinal)->delete();
            
            
            $matricula->forceDelete();
            LogService::activity(
                'eliminar',
                'MatrículaTutorias',
                "Se eliminó permanentemente la matrícula #{$idFinal}"
            );
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Matrícula eliminada permanentemente.'
            ]);

        } catch (\Exception $e) {
         
            $this->errorMessage = 'No se pudo eliminar: ' . $e->getMessage();
            $this->dispatch('openErrorModal'); 
        }
    }
    public function restore($id)
    {
        $matricula = MatriculaTutoria::withTrashed()->findOrFail($id);
        $matricula->restore();
        
        LogService::activity(
            'restaurar',
            'MatrículaTutorias',
            "Se restauró la matrícula de tutoría #{$matricula->id}",
            [
                'Restaurado por' => Auth::user()->email,
                'Estudiante' => $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido,
                'Tutoría' => $matricula->tutoria->nombre,
            ]
        );
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Matrícula restaurada correctamente.'
        ]);
    }
    
    public function forceDelete($id)
    {
        $matricula = MatriculaTutoria::withTrashed()->findOrFail($id);
        $matriculaId = $matricula->id;
        $estudianteNombre = $matricula->estudiante->nombre . ' ' . $matricula->estudiante->apellido;
        $matricula->forceDelete();
        
        LogService::activity(
            'eliminar_permanentemente',
            'MatrículaTutorias',
            "Se eliminó permanentemente la matrícula de tutoría #{$matriculaId}",
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
    }
    
    public function closeRegistrarTutoriaModal()
    {
        $this->showRegistrarTutoriaModal = false;
        $this->reset(['registrarPagada', 'metodo_pago', 'numero_transaccion', 'referencia_bancaria', 'fecha_pago']);
        $this->fecha_pago = now()->format('Y-m-d');
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
            'tutoria_id',
            'estado',
            'precio_hora_aplicado',
            'saldo_pendiente',
            'tutorias_registradas',
            'tutorias_pagadas',
            'fecha_inicio',
            'aprobado',
            'observaciones',
            'dni_busqueda',
            'estudiante_info',
            'tutoria_info',
            'errorMessage',
            'registrarPagada',
            'metodo_pago',
            'numero_transaccion',
            'referencia_bancaria',
            'fecha_pago',
        ]);
        $this->fecha_inicio = now()->format('Y-m-d');
        $this->fecha_pago = now()->format('Y-m-d');
        $this->cantidad_horas = 1;
        $this->estado = 'activa';
        $this->isEditing = false;
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
        $matricula = MatriculaTutoria::findOrFail($id);
        
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
}