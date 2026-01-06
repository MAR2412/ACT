<?php

namespace App\Livewire\Usuario;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\LogService;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuarios extends Component
{
    use WithPagination;
    use Notifiable;
    public $isEditing = false;
    public $isOpen = false;
    public $showDeleteModal = false;
    public $showErrorModal = false;

    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $profile_photo_path;
    public $selectedRoles = [];

    public $user;
    public $roles;
    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'asc';

    public $IdAEliminar;
    public $nombreAEliminar;
    public $errorMessage;

    public function mount()
    {
        $this->roles = Role::all();
    }
    public function activeRole()
    {
        return $this->belongsTo(Role::class, 'active_role_id');
    }
    public function render()
    {
        $users = User::with('roles')
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.Usuario.usuarios', compact('users'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->roles = Role::all();
        $this->isEditing = false;
        $this->isOpen = true;
    }

    public function edit(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->profile_photo_path = $user->profile_photo_path;
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
        $this->roles = Role::all();
        $this->isEditing = true;
        $this->isOpen = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email' . ($this->user ? ',' . $this->user->id : ''),
            'password' => $this->isEditing ? 'nullable|min:8' : 'required|min:8',
            'selectedRoles' => 'required|array',
            'selectedRoles.*' => 'exists:roles,id',
        ]);

        try {
            if ($this->isEditing) {
                $this->updateUser();
            } else {
                $this->createUser();
            }
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->showErrorModal = true;
        }
    }

    private function createUser()
    {
        // Primero obtener el rol que será asignado
        $roles = Role::whereIn('id', $this->selectedRoles)->get();
        
        if ($roles->isEmpty()) {
            throw new \Exception('El usuario debe tener al menos un rol asignado');
        }

        // Crear el usuario con el active_role_id
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'profile_photo_path' => $this->profile_photo_path,
            'password' => Hash::make($this->password),
            'active_role_id' => $roles->first()->id, // ¡ESTO ES LO IMPORTANTE!
        ]);

        // Asignar los roles
        $user->syncRoles($roles);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        LogService::activity(
            'crear',
            'Configuración',
            "Se creó el usuario {$user->name}",
            [
                'Creado por' => Auth::user()->email,
                'Usuario' => $user->email,
            ]
        );

        session()->flash('message', 'Usuario creado correctamente');
        $this->closeModal();
    }

    private function updateUser()
    {
        $user = User::findOrFail($this->user->id);

        // Obtener los nuevos roles
        $roles = Role::whereIn('id', $this->selectedRoles)->get();
        
        if ($roles->isEmpty()) {
            throw new \Exception('El usuario debe tener al menos un rol asignado');
        }

        // Verificar si el active_role_id actual sigue siendo válido
        $currentActiveRoleIsValid = $roles->contains('id', $user->active_role_id);
        
        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
            'profile_photo_path' => $this->profile_photo_path,
            'password' => $this->password ? Hash::make($this->password) : $user->password,
        ];
        
        // Si el rol activo ya no es válido, asignar el primero de los nuevos roles
        if (!$currentActiveRoleIsValid) {
            $updateData['active_role_id'] = $roles->first()->id;
        }

        $user->update($updateData);

        // Asignar los roles
        $user->syncRoles($roles);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        LogService::activity(
            'actualizar',
            'Configuración',
            "Se actualizó el usuario {$user->name}",
            [
                'Actualizado por' => Auth::user()->email,
                'Usuario' => $user->email,
            ]
        );

        session()->flash('message', 'Usuario actualizado correctamente');
        $this->closeModal();
    }
    public function confirmDelete($id)
    {
        $user = User::findOrFail($id);
        $this->IdAEliminar = $id;
        $this->nombreAEliminar = $user->name;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $user = User::findOrFail($this->IdAEliminar);
        $user->forceDelete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        LogService::activity(
            'eliminar',
            'Configuración',
            "Se eliminó el usuario {$user->name}",
            [
                'Eliminado por' => Auth::user()->email,
            ]
        );

        session()->flash('message', 'Usuario eliminado');
        $this->closeDeleteModal();
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
            'name',
            'email',
            'password',
            'password_confirmation',
            'profile_photo_path',
            'selectedRoles',
            'user',
        ]);
    }

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortField === $field && $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';

        $this->sortField = $field;
    }
}
