<?php

use App\Livewire\Evento\Eventos;
use App\Livewire\Inicio\InicioAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogViewerController;
use App\Http\Controllers\ModuleRedirectController;
use App\Http\Middleware\CheckModuleAccess;
use App\Livewire\Admin\SessionManager;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Rol\Roles;
use App\Livewire\Rol\RoleForm;
use App\Livewire\Usuario\Usuarios;
use App\Livewire\Seccion\Secciones;
use App\Livewire\Modalidad\Modalidades;
use App\Livewire\Sede\Sedes;
use App\Livewire\Tutoria\Tutorias;
use App\Livewire\Modulo\Modulos;
use App\Livewire\Estudiante\Estudiantes;
use App\Livewire\Matricula\Matriculas;
use App\Livewire\Matricula\MatriculaTutorias;
use App\Livewire\Pago\Pagos;
use App\Livewire\Pago\HistorialPagos;
use App\Livewire\PagosTutoria\HistorialPagosTutorias;
use App\Livewire\PagosTutoria\PagosTutorias;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/error/404', 'errors.404')->name('error.404');
Route::view('/error/500', 'errors.500')->name('error.500');
Route::view('/error/403', 'errors.403')->name('error.403');

Route::get('/modulo/{module}', [ModuleRedirectController::class, 'redirectToModule'])
    ->middleware(['auth:sanctum', 'verified'])
    ->name('module.redirect');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', InicioAdmin::class)->name('dashboard');

    Route::middleware(['auth', CheckModuleAccess::class . ':configuracion'])->group(function () {
        Route::get('/configuracion/roles', Roles::class)
            ->name('roles')
            ->middleware('can:configuracion.roles.ver');
        Route::get('/configuracion/roles/crear', RoleForm::class)
            ->name('roles.create')
            ->middleware('can:configuracion.roles.crear');
        Route::get('/configuracion/roles/{roleId}/editar', RoleForm::class)
            ->name('roles.edit')
            ->middleware('can:configuracion.roles.editar');
        Route::get('/configuracion/usuarios', Usuarios::class)
            ->name('usuarios')
            ->middleware('can:configuracion.usuarios.ver');
    });

    Route::middleware(['auth', CheckModuleAccess::class . ':estudiantes'])->group(function () {
        Route::get('/estudiantes/estudiantes', Estudiantes::class)
            ->name('estudiantes')
            ->middleware('can:estudiantes.estudiantes.ver');
        Route::get('/estudiantes/estudiantes/crear', Estudiantes::class)
            ->name('estudiantes.create')
            ->middleware('can:estudiantes.estudiantes.crear');
        Route::get('/estudiantes/estudiantes/{estudiante}/editar', Estudiantes::class)
            ->name('estudiantes.edit')
            ->middleware('can:estudiantes.estudiantes.editar');
    });

    Route::middleware(['auth', CheckModuleAccess::class . ':secciones'])->group(function () {
        Route::get('/secciones/secciones', Secciones::class)
            ->name('secciones')
            ->middleware('can:secciones.secciones.ver');
        Route::get('/secciones/secciones/crear', Secciones::class)
            ->name('secciones.create')
            ->middleware('can:secciones.secciones.crear');
        Route::get('/secciones/secciones/{seccion}/editar', Secciones::class)
            ->name('secciones.edit')
            ->middleware('can:secciones.secciones.editar');
        Route::get('/secciones/modalidades', Modalidades::class)
            ->name('modalidades')
            ->middleware('can:secciones.modalidades.ver');
        Route::get('/secciones/modalidades/crear', Modalidades::class)
            ->name('modalidades.create')
            ->middleware('can:secciones.modalidades.crear');
        Route::get('/secciones/modalidades/{modalidad}/editar', Modalidades::class)
            ->name('modalidades.edit')
            ->middleware('can:secciones.modalidades.editar');
        Route::get('/secciones/sedes', Sedes::class)
            ->name('sedes')
            ->middleware('can:secciones.sedes.ver');
        Route::get('/secciones/sedes/crear', Sedes::class)
            ->name('sedes.create')
            ->middleware('can:secciones.sedes.crear');
        Route::get('/secciones/sedes/{sedes}/editar', Sedes::class)
            ->name('sedes.edit')
            ->middleware('can:secciones.sedes.editar');
    });

    Route::middleware(['auth', CheckModuleAccess::class . ':matriculas'])->group(function () {
        Route::get('/matriculas/matriculas', Matriculas::class)
            ->name('matriculas')
            ->middleware('can:matriculas.matriculas.ver');
        Route::get('/matriculas/matriculas/crear', Matriculas::class)
            ->name('matriculas.create')
            ->middleware('can:matriculas.matriculas.crear');
        Route::get('/matriculas/matriculas/{matricula}/editar', Matriculas::class)
            ->name('matriculas.edit')
            ->middleware('can:matriculas.matriculas.editar');
        Route::get('/matriculas/matriculas-tutorias', MatriculaTutorias::class)
            ->name('matriculas-tutorias')
            ->middleware('can:matriculas.matriculas-tutorias.ver');
        Route::get('/matriculas/matriculas-tutorias/crear', MatriculaTutorias::class)
            ->name('matriculas-tutoria.create')
            ->middleware('can:matriculas.matriculas-tutorias.crear');
        Route::get('/matriculas/matriculas-tutorias/{matriculaTutoria}/editar', MatriculaTutorias::class)
            ->name('matriculas-tutorias.edit')
            ->middleware('can:matriculas.matriculas-tutorias.editar');
    });

    Route::middleware(['auth', CheckModuleAccess::class . ':oferta-educativa'])->group(function () {
        Route::get('/oferta-educativa/modulos', Modulos::class)
            ->name('modulos')
            ->middleware('can:oferta-educativa.modulos.ver');
        Route::get('/oferta-educativa/modulos/crear', Modulos::class)
            ->name('modulos.create')
            ->middleware('can:oferta-educativa.modulos.crear');
        Route::get('/oferta-educativa/modulos/{modulo}/editar', Modulos::class)
            ->name('modulos.edit')
            ->middleware('can:oferta-educativa.modulos.editar');
        Route::get('/oferta-educativa/tutorias', Tutorias::class)
            ->name('tutorias')
            ->middleware('can:oferta-educativa.tutorias.ver');
        Route::get('/oferta-educativa/tutorias/crear', Tutorias::class)
            ->name('tutorias.create')
            ->middleware('can:oferta-educativa.tutorias.crear');
        Route::get('/oferta-educativa/tutorias/{tutoria}/editar', Tutorias::class)
            ->name('tutorias.edit')
            ->middleware('can:oferta-educativa.tutorias.editar');
    });

    Route::middleware(['auth', CheckModuleAccess::class . ':pagos-modulos'])->group(function () {
        Route::get('/pagos-modulos/pagos', Pagos::class)
            ->name('pagos')
            ->middleware('can:pagos-modulos.pagos.ver');
        Route::get('/pagos-modulos/pagos/crear', Pagos::class)
            ->name('pagos.create')
            ->middleware('can:pagos-modulos.pagos.crear');
        Route::get('/pagos-modulos/pagos/{pago}/editar', Pagos::class)
            ->name('pagos.edit')
            ->middleware('can:pagos-modulos.pagos.editar');
        Route::get('/pagos-modulos/pagos-historial', HistorialPagos::class)
            ->name('pagos-historial')
            ->middleware('can:pagos-modulos.pagos-historial.ver');
        Route::get('/pagos-modulos/pagos-tutorias', PagosTutorias::class)
            ->name('pagos-tutorias')
            ->middleware('can:pagos-modulos.pagos-tutorias.ver');
        Route::get('/pagos-modulos/pagos-tutorias/crear', PagosTutorias::class)
            ->name('pagos-tutorias.create')
            ->middleware('can:pagos-modulos.pagos-tutorias.crear');
        Route::get('/pagos-modulos/pagos-tutorias/{pagosTutoria}/editar', PagosTutorias::class)
            ->name('pagos-tutorias.edit')
            ->middleware('can:pagos-modulos.pagos-tutorias.editar');
        Route::get('/pagos-modulos/historial-pagos-tutorias', HistorialPagosTutorias::class)
            ->name('historial-pagos-tutorias')
            ->middleware('can:pagos-modulos.historial-pagos-tutorias.ver');
    });

    Route::middleware(['auth', CheckModuleAccess::class . ':logs'])->group(function () {
        Route::get('/logs', [LogViewerController::class, 'index'])
            ->name('logs')
            ->middleware('can:logs.visor.ver');
        Route::get('/logs/dashboard', [LogViewerController::class, 'dashboard'])
            ->name('logsdashboard')
            ->middleware('can:logs.dashboard.ver');
        Route::get('/logs/sessions', SessionManager::class)
            ->name('sessions')
            ->middleware('can:logs.sessions.ver');
        Route::get('/logs/{log}', [LogViewerController::class, 'show'])
            ->name('logs.show')
            ->middleware('can:logs.visor.ver');
        Route::post('/logs/cleanup', [LogViewerController::class, 'cleanup'])
            ->name('cleanup')
            ->middleware('can:logs.mantenimiento.limpiar');
    });

});