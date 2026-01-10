<?php

namespace App\Livewire\Pago;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Egreso;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;
use Barryvdh\DomPDF\Facade\Pdf;

class Egresos extends Component
{
    use WithPagination;
    
    public $isEditing = false;
    public $isOpen = false;
    public $showDeleteModal = false;
    public $showErrorModal = false;
    public $errorMessage;

    public $egreso_id = null;
    public $monto_utilizado = '';
    public $fecha_egreso = '';
    public $descripcion = '';
    public $estado = true;
    
    public $search = '';
    public $perPage = 10;
    public $sortField = 'fecha_egreso';
    public $sortDirection = 'desc';
    
    public $IdAEliminar;
    public $egresoAEliminar;
    
    protected $rules = [
        'monto_utilizado' => 'required|numeric|min:0.01',
        'fecha_egreso' => 'required|date',
        'descripcion' => 'required|string|min:3|max:500',
        'estado' => 'boolean',
    ];
    
    public function mount()
    {
        $this->fecha_egreso = now()->format('Y-m-d');
    }
    
    public function render()
    {
        $egresos = Egreso::with(['creator'])
            ->where(function ($q) {
                $q->where('descripcion', 'like', "%{$this->search}%")
                  ->orWhereHas('creator', function ($q2) {
                      $q2->where('name', 'like', "%{$this->search}%");
                  })
                  ->orWhere('monto_utilizado', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
            
        $totalEgresos = Egreso::where('estado', true)->sum('monto_utilizado');
        $totalMes = Egreso::where('estado', true)
            ->whereMonth('fecha_egreso', now()->month)
            ->whereYear('fecha_egreso', now()->year)
            ->sum('monto_utilizado');
        
        return view('livewire.pago.egresos', compact('egresos', 'totalEgresos', 'totalMes'));
    }

    public function openModal()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isOpen = true;
    }
    
    public function edit($id)
    {
        $egreso = Egreso::findOrFail($id);
        $this->egreso_id = $egreso->id;
        $this->monto_utilizado = $egreso->monto_utilizado;
        $this->fecha_egreso = $egreso->fecha_egreso->format('Y-m-d');
        $this->descripcion = $egreso->descripcion;
        $this->estado = $egreso->estado;
        $this->isEditing = true;
        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate();
        
        try {
            $data = [
                'monto_utilizado' => $this->monto_utilizado,
                'fecha_egreso' => $this->fecha_egreso,
                'descripcion' => $this->descripcion,
                'estado' => $this->estado,
                'updated_by' => Auth::id(),
            ];
            
            if ($this->isEditing) {
                $egreso = Egreso::findOrFail($this->egreso_id);
                $egreso->update($data);
                $message = 'Egreso actualizado exitosamente';
                
                LogService::activity(
                    'actualizar',
                    'Egresos',
                    "Se actualizó el egreso #{$egreso->id}",
                    [
                        'Actualizado por' => Auth::user()->email,
                        'Monto' => $egreso->monto_utilizado,
                        'Descripción' => $egreso->descripcion,
                        'Estado' => $egreso->estado ? 'Activo' : 'Inactivo',
                    ]
                );
            } else {
                $data['created_by'] = Auth::id();
                $egreso = Egreso::create($data);
                $message = 'Egreso creado exitosamente';
                
                LogService::activity(
                    'crear',
                    'Egresos',
                    "Se creó el egreso #{$egreso->id}",
                    [
                        'Creado por' => Auth::user()->email,
                        'Monto' => $egreso->monto_utilizado,
                        'Descripción' => $egreso->descripcion,
                        'Fecha' => $egreso->fecha_egreso,
                    ]
                );
            }
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message
            ]);
            
            $this->closeModal();
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }
        
    public function confirmDelete($id)
    {
        $egreso = Egreso::findOrFail($id);
        $this->IdAEliminar = $id;
        $this->egresoAEliminar = $egreso;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $egreso = Egreso::findOrFail($this->IdAEliminar);
        $descripcion = $egreso->descripcion;
        $monto = $egreso->monto_utilizado;
        
        $egreso->forceDelete();

        LogService::activity(
            'eliminar',
            'Egresos',
            "Se eliminó el egreso #{$egreso->id}",
            [
                'Eliminado por' => Auth::user()->email,
                'Descripción' => $descripcion,
                'Monto' => $monto,
            ]
        );

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Egreso eliminado.'
        ]);
        
        $this->closeDeleteModal();
    }

    public function toggleEstado($id)
    {
        try {
            $egreso = Egreso::findOrFail($id);
            $nuevoEstado = !$egreso->estado;
            $egreso->update([
                'estado' => $nuevoEstado,
                'updated_by' => Auth::id(),
            ]);
            
            $estadoTexto = $nuevoEstado ? 'activado' : 'desactivado';
            
            LogService::activity(
                'actualizar',
                'Egresos',
                "Se {$estadoTexto} el egreso #{$egreso->id}",
                [
                    'Actualizado por' => Auth::user()->email,
                    'Descripción' => $egreso->descripcion,
                    'Nuevo estado' => $nuevoEstado ? 'Activo' : 'Inactivo',
                ]
            );

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Egreso {$estadoTexto}"
            ]);
            
        } catch (\Exception $e) {
            $this->errorMessage = 'Error: ' . $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    public function downloadReporteEgresos()
    {
        $egresos = Egreso::with(['creator'])
            ->where('estado', true)
            ->orderBy('fecha_egreso', 'desc')
            ->get();
        
        $total = $egresos->sum('monto_utilizado');
        $totalMes = Egreso::where('estado', true)
            ->whereMonth('fecha_egreso', now()->month)
            ->whereYear('fecha_egreso', now()->year)
            ->sum('monto_utilizado');
        
        $data = [
            'titulo' => 'REPORTE DE EGRESOS',
            'subtitulo' => 'Historial de salidas de efectivo',
            'egresos' => $egresos,
            'total' => $total,
            'total_mes' => $totalMes,
            'fecha' => now('America/Tegucigalpa')->format('d/m/Y h:i A'),
            'user' => Auth::user(),
        ];
        
        $pdf = Pdf::loadView('pdf.reporte-egresos', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "Reporte_Egresos_" . date('Y-m-d') . ".pdf");
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
        $this->egresoAEliminar = null;
    }

    private function resetInputFields()
    {
        $this->reset([
            'egreso_id',
            'monto_utilizado',
            'fecha_egreso',
            'descripcion',
            'estado',
        ]);
        $this->fecha_egreso = now()->format('Y-m-d');
        $this->estado = true;
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';

        $this->sortField = $field;
    }
}