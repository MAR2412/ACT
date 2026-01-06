<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermisoSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $modulePermissions = [
            'configuracion' => [
                'roles' => ['ver', 'crear', 'editar', 'eliminar'],
                'usuarios' => ['ver', 'crear', 'editar', 'eliminar'],
            ],
            'secciones' => [
               'secciones' => ['ver', 'crear', 'editar', 'eliminar'],
               'modalidades' => ['ver', 'crear', 'editar', 'eliminar'],
               'sedes' => ['ver', 'crear', 'editar', 'eliminar'],
            ],
            'matriculas' => [
               'matriculas' => ['ver', 'crear', 'editar', 'eliminar'],
               'matriculas-tutorias' => ['ver', 'crear', 'editar', 'eliminar'],
            ],
            'oferta-educativa' => [
                'modulos' => ['ver', 'crear', 'editar', 'eliminar'],
                'tutorias' => ['ver', 'crear', 'editar', 'eliminar'],
            ],
            'pagos-modulos' => [
               'pagos' => ['ver', 'crear', 'editar', 'eliminar'],
               'pagos-tutorias' => ['ver', 'crear', 'editar', 'eliminar'],
               'pagos-historial' => ['ver', 'crear', 'editar', 'eliminar'], // Corregido para coincidir con el menu
               'historial-pagos-tutorias' => ['ver', 'crear', 'editar', 'eliminar'],
            ],
            'estudiantes' => [
               'estudiantes' => ['ver', 'crear', 'editar', 'eliminar'],
            ],
            'logs' => [
                'visor' => ['ver', 'filtrar'],
                'dashboard' => ['ver', 'analizar'],
                'sessions' => ['ver', 'gestionar'],
            ],
        ];
        
        foreach (array_keys($modulePermissions) as $module) {
            Permission::firstOrCreate([
                'name' => "acceso-{$module}", 
                'guard_name' => 'web'
            ]);
        }
        
        foreach ($modulePermissions as $module => $functionalities) {
            foreach ($functionalities as $functionality => $actions) {
                foreach ($actions as $action) {
                    Permission::firstOrCreate([
                        'name' => "{$module}.{$functionality}.{$action}", 
                        'guard_name' => 'web'
                    ]);
                }
            }
        }
        
        $otherPermissions = [
            'ver-dashboard-admin',
            'ver-dashboard-participante',
        ];

        foreach ($otherPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission, 
                'guard_name' => 'web'
            ]);
        }

        $this->command->info('Permisos sincronizados correctamente.');
    }
}