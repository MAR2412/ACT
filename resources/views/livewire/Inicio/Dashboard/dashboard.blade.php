<div>
    <div>
    @if (auth()->check() &&
         auth()->user()->hasPermissionTo('ver-dashboard-admin') &&
         auth()->user()->activeRole &&
         auth()->user()->activeRole->hasPermissionTo('ver-dashboard-admin'))
        @livewire('Inicio.dashboard-admin.dashboard-admin')
    @endif

    @if (auth()->check() &&
         auth()->user()->hasPermissionTo('ver-dashboard-participante') &&
         auth()->user()->activeRole &&
         auth()->user()->activeRole->hasPermissionTo('ver-dashboard-participante'))
        @livewire('Inicio.dashboard-participante.dashboard-participante')
    @endif
</div>
</div>