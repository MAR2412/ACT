<?php

namespace App\Livewire\Inicio;

use App\Models\Estado\TipoEstado;
use App\Models\Proyecto\Proyecto;
use App\Models\Personal\Empleado;
use Livewire\Component;
use Carbon\Carbon;
use Spatie\Activitylog\Models\Activity;

class InicioAdmin extends Component
{
    public function render()
    {
        return view('livewire.Inicio.Dashboard.dashboard');
    }
}