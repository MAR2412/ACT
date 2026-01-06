<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsuarioTablaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear el usuario administrador
        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'active_role_id' => 1,
                'name' => 'root', 
                'password' => bcrypt('12345678')
            ]
        );
        
        // Crear permisos específicos para los módulos si no existen
        $modulePermissions = [
            'acceso-configuracion',
            'acceso-logs',
            'acceso-estudiantes',
            'acceso-secciones',
            'acceso-oferta-educativa',
            'acceso-matriculas',
            'acceso-pagos-modulos',
        ];
        
        foreach ($modulePermissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }
        
        // Crear el rol super-admin
        $role = Role::firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'web'],
            ['description' => 'Super Administrador']
        );

        // Crear el rol super-admin
        $roleParticipante = Role::firstOrCreate(
            ['name' => 'participante', 'guard_name' => 'web'],
            ['description' => 'Rol por default']
        );
         
        // Asignar todos los permisos al rol
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);

    $permisoParticipante = Permission::where('name', 'ver-dashboard-participante')->pluck('id')->all();
    $roleParticipante->syncPermissions($permisoParticipante);

        // Asignar el rol al usuario
        $user->assignRole([$role->id]);
    }
}