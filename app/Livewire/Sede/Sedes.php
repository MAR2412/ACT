<?php

namespace App\Livewire\Sede;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sede;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;

class Sedes extends Component
{
    use WithPagination;
    
    public $isEditing = false;
    public $isOpen = false;
    public $showDeleteModal = false;
    public $showErrorModal = false;

    public $nombre;
    public $descripcion;
    public $departamento;
    public $municipio;
    public $estado = true;

    public $sede;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $IdAEliminar;
    public $nombreAEliminar;
    public $errorMessage;

    public $departamentos = [
        'Atlántida',
        'Colón',
        'Comayagua',
        'Copán',
        'Cortés',
        'Choluteca',
        'El Paraíso',
        'Francisco Morazán',
        'Gracias a Dios',
        'Intibucá',
        'Islas de la Bahía',
        'La Paz',
        'Lempira',
        'Ocotepeque',
        'Olancho',
        'Santa Bárbara',
        'Valle',
        'Yoro'
    ];

    public $municipiosPorDepartamento = [
        'Atlántida' => ['La Ceiba', 'Tela', 'Jutiapa', 'La Masica', 'San Francisco', 'Arizona', 'Esparta', 'El Porvenir', 'San Antonio'],
        'Colón' => ['Trujillo', 'Tocoa', 'Sonaguera', 'Bonito Oriental', 'Limón', 'Santa Fe', 'Santa Rosa de Aguán', 'Iriona'],
        'Comayagua' => ['Comayagua', 'Siguatepeque', 'La Libertad', 'Taulabé', 'San Jerónimo', 'Esquías', 'Ojos de Agua', 'San José de Comayagua'],
        'Copán' => ['Santa Rosa de Copán', 'Copán Ruinas', 'La Entrada', 'Dulce Nombre', 'San Agustín', 'Concepción', 'San Antonio', 'Cabañas'],
        'Cortés' => ['San Pedro Sula', 'Puerto Cortés', 'Villanueva', 'Choloma', 'La Lima', 'Omoa', 'Pimienta', 'Potrerillos'],
        'Choluteca' => ['Choluteca', 'El Triunfo', 'Pespire', 'San Lorenzo', 'Marcovia', 'Namasigüe', 'Orocuina', 'Apacilagua', 'San Isidro'],
        'El Paraíso' => ['Yuscarán', 'Danlí', 'El Paraíso', 'Morocelí', 'San Matías', 'Teupasenti', 'Trojes', 'Villa de San Francisco'],
        'Francisco Morazán' => ['Tegucigalpa', 'Distrito Central', 'Valle de Ángeles', 'Santa Lucía', 'Ojojona', 'Talanga', 'Cedros', 'San Ignacio'],
        'Gracias a Dios' => ['Puerto Lempira', 'Brus Laguna', 'Ahuas', 'Wampusirpe', 'Villeda Morales'],
        'Intibucá' => ['La Esperanza', 'Intibucá', 'Jesús de Otoro', 'San Juan', 'Magdalena', 'San Miguelito', 'San Isidro'],
        'Islas de la Bahía' => ['Roatán', 'Guanaja', 'José Santos Guardiola', 'Utila'],
        'La Paz' => ['La Paz', 'Marcala', 'Santa Elena', 'Yarula', 'Cabañas', 'Opatoro', 'Chinacla', 'San Pedro de Tutule'],
        'Lempira' => ['Gracias', 'Lempira', 'La Campa', 'San Juan Guarita', 'San Manuel Colohete', 'Tomalá', 'San Sebastián', 'Erandique'],
        'Ocotepeque' => ['Ocotepeque', 'Sensenti', 'San Marcos', 'La Labor', 'Lucerna', 'Concepción', 'Dolores Merendón', 'Sinuapa'],
        'Olancho' => ['Juticalpa', 'Catacamas', 'Manto', 'San Esteban', 'Gualaco', 'Guarizama', 'San Francisco de la Paz', 'Dulce Nombre de Culmí'],
        'Santa Bárbara' => ['Santa Bárbara', 'Ilama', 'San Luis', 'Trinidad', 'San José de Colinas', 'Naranjito', 'Gualala', 'San Vicente Centenario'],
        'Valle' => ['Nacaome', 'San Lorenzo', 'Goascorán', 'Amapala', 'Langue', 'Aramecina', 'Caridad', 'Alianza'],
        'Yoro' => ['Yoro', 'Olanchito', 'El Progreso', 'Morazán', 'Victoria', 'Jocón', 'Santa Rita', 'Sulaco']
    ];

    public $municipiosDisponibles = [];

   /* public function updatedDepartamento($value)
    {
        $this->municipiosDisponibles = $this->municipiosPorDepartamento[$value] ?? [];
        $this->municipio = '';
    }*/
    public function updatedDepartamento($value)
    {
        $this->municipiosDisponibles = $this->municipiosPorDepartamento[$value] ?? [];
        $this->reset('municipio'); 
    }

    public function mount()
    {
        $this->departamento = 'Choluteca';
        $this->municipiosDisponibles = $this->municipiosPorDepartamento[$this->departamento] ?? [];
    }
    
    public function render()
    {
        $sedes = Sede::withTrashed()
            ->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('descripcion', 'like', "%{$this->search}%")
                  ->orWhere('departamento', 'like', "%{$this->search}%")
                  ->orWhere('municipio', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.sede.sedes', compact('sedes'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditing = false;
        $this->isOpen = true;
    }

    public function edit(Sede $sede)
    {
        $this->sede = $sede;
        $this->nombre = $sede->nombre;
        $this->descripcion = $sede->descripcion;
        $this->departamento = $sede->departamento;
        $this->municipiosDisponibles = $this->municipiosPorDepartamento[$this->departamento] ?? [];
        $this->municipio = $sede->municipio;
        $this->estado = $sede->estado;
        $this->isEditing = true;
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate([
            'nombre' => 'required|unique:sedes,nombre' . ($this->isEditing && $this->sede ? ',' . $this->sede->id : ''),
            'descripcion' => 'nullable|string|max:500',
            'departamento' => 'required|in:' . implode(',', $this->departamentos),
            'municipio' => 'required|string|max:100',
            'estado' => 'required|boolean',
        ]);

        try {
            if ($this->isEditing) {
                $this->updateSede();
            } else {
                $this->createSede();
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    private function createSede()
    {
        $sede = Sede::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'departamento' => $this->departamento,
            'municipio' => $this->municipio,
            'estado' => $this->estado,
            'created_by' => Auth::id(),
        ]);

        LogService::activity(
            'crear',
            'Sedes',
            "Se creó la sede {$sede->nombre}",
            [
                'Creado por' => Auth::user()->email,
                'Sede' => $sede->nombre,
                'Ubicación' => "{$sede->municipio}, {$sede->departamento}",
            ]
        );

        session()->flash('message', 'Sede creada correctamente');
        $this->closeModal();
    }

    private function updateSede()
    {
        $sede = Sede::findOrFail($this->sede->id);

        $sede->update([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'departamento' => $this->departamento,
            'municipio' => $this->municipio,
            'estado' => $this->estado,
            'updated_by' => Auth::id(),
        ]);

        LogService::activity(
            'actualizar',
            'Sedes',
            "Se actualizó la sede {$sede->nombre}",
            [
                'Actualizado por' => Auth::user()->email,
                'Sede' => $sede->nombre,
                'Ubicación' => "{$sede->municipio}, {$sede->departamento}",
            ]
        );

        session()->flash('message', 'Sede actualizada correctamente');
        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $sede = Sede::findOrFail($id);
        $this->IdAEliminar = $id;
        $this->nombreAEliminar = $sede->nombre;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $sede = Sede::findOrFail($this->IdAEliminar);
        $sede->delete();

        LogService::activity(
            'eliminar',
            'Sedes',
            "Se eliminó la sede {$sede->nombre}",
            [
                'Eliminado por' => Auth::user()->email,
                'Sede' => $sede->nombre,
            ]
        );

        session()->flash('message', 'Sede eliminada');
        $this->closeDeleteModal();
    }

    public function restore($id)
    {
        $sede = Sede::withTrashed()->findOrFail($id);
        $sede->restore();

        LogService::activity(
            'restaurar',
            'Sedes',
            "Se restauró la sede {$sede->nombre}",
            [
                'Restaurado por' => Auth::user()->email,
                'Sede' => $sede->nombre,
            ]
        );

        session()->flash('message', 'Sede restaurada correctamente');
    }
    
    public function forceDelete($id)
    {
        $sede = Sede::withTrashed()->findOrFail($id);
        $sedeName = $sede->nombre;
        $sede->forceDelete();

        LogService::activity(
            'eliminar_permanentemente',
            'Sedes',
            "Se eliminó permanentemente la sede {$sedeName}",
            [
                'Eliminado por' => Auth::user()->email,
            ]
        );

        session()->flash('message', 'Sede eliminada permanentemente');
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
        $this->nombreAEliminar = null;
    }

    private function resetInputFields()
    {
        $this->reset([
            'nombre',
            'descripcion',
            'departamento',
            'municipio',
            'estado',
            'sede',
            'errorMessage',
        ]);
        $this->departamento = 'Choluteca';
        $this->municipiosDisponibles = $this->municipiosPorDepartamento[$this->departamento] ?? [];
        $this->estado = true;
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
        $sede = Sede::findOrFail($id);
        $sede->estado = !$sede->estado;
        $sede->save();

        $accion = $sede->estado ? 'activada' : 'desactivada';
        
        LogService::activity(
            'cambiar_estado',
            'Sedes',
            "Se {$accion} la sede {$sede->nombre}",
            [
                'Modificado por' => Auth::user()->email,
                'Sede' => $sede->nombre,
                'Nuevo estado' => $sede->estado ? 'Activo' : 'Inactivo',
            ]
        );

        session()->flash('message', "Sede {$accion} correctamente");
    }
}