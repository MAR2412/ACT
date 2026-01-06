<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;



class Dashboards extends Component
{

    use WithPagination;
    
    public $nombre, $descripcion, $fecha, $horaInicio, $horaFin, $lugar, $linkreunion, $idConferencista, $conferencia_id, $search, $IdEvento;
    public $isOpen = 0;
    public $showDetails = false;
    public $inputSearchConferencista = '';
    public $searchConferencistas = [];
    public $inputSearchEvento = '';
    public $searchEventos = [];
    public $selectedConferencia;
   
    protected $listeners = ['refreshComponent' => '$refresh'];
    public function viewDetails($id)
    {
       
        $this->showDetails = true;
    }

    public function closeDetails()
    {
        $this->showDetails = false;
    }

    public function render()
    {
       

        return view('dashboard', [
            
        ]);
    }
    

   
    

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function agregarConferencia($eventoId)
    {
        $this->IdEvento = $eventoId;
        $this->create();
        $this->resetPage(); 
    }
    

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->nombre = '';
        $this->descripcion = '';
        $this->fecha = '';
        $this->horaInicio = '';
        $this->horaFin = '';
        $this->lugar = '';
        $this->linkreunion = '';
        $this->idConferencista = '';
        $this->inputSearchConferencista = '';
        $this->searchConferencistas = [];
        $this->IdEvento = '';
    }
    
    public function store()
    {
        

        // Mensaje de éxito
        session()->flash('message', $this->conferencia_id ? 'Conferencia actualizada correctamente!' : 'Conferencia creada correctamente!');

        // Cierra el modal y reinicia los campos
        $this->closeModal();
        $this->resetInputFields();
        $this->resetPage(); 
       
        
    }


    public function edit($id)
    {
        
        $this->openModal();
    }

    public function delete($id)
    {
      
        session()->flash('message', 'Registro eliminado correctamente!');
    }


    public function mount()
    {
        
    }

}
