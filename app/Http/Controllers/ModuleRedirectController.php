<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleRedirectController extends Controller
{
    public function redirectToModule($module)
    {
        $user = auth()->user();
        $moduleConfig = config('rutas.' . $module, []);
        
        if (empty($moduleConfig)) {
            return redirect()->route('dashboard');
        }
        
       
        if ($user->hasRole('super-admin')) {
            return redirect()->route($moduleConfig['route']);
        }
        
        
        $modulePermission = "acceso-{$module}";
        $hasModuleAccess = $user->can($modulePermission);
    
        if (!isset($moduleConfig['items']) || empty($moduleConfig['items'])) {
            if ($hasModuleAccess) {
                return redirect()->route($moduleConfig['route']);
            } else {
                return redirect()->route('dashboard')->with('error', 'No tienes acceso a este módulo.');
            }
        }
        
  
        foreach ($moduleConfig['items'] as $item) {
            
            if (
                (isset($item['always_visible']) && $item['always_visible']) ||
                (!isset($item['permisos']) || empty($item['permisos']))
            ) {
                return redirect()->route($item['route']);
            }
            
            if (isset($item['permisos']) && is_array($item['permisos'])) {
                foreach ($item['permisos'] as $permiso) {
                    if ($user->can($permiso)) {
                        return redirect()->route($item['route']);
                    }
                }
            }
        }
        
        
        return redirect()->route('dashboard')->with('error', 'No tienes acceso a ninguna sección de este módulo.');
    }
}