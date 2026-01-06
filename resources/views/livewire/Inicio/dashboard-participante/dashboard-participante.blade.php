<div class="min-h-screen bg-stone-50 dark:bg-stone-900">
    <div class="max-w-7xl mx-auto px-4 py-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-stone-900 dark:text-green">Dashboard </h1>
            <p class="text-stone-600 dark:text-stone-400 mt-2">Gestión de estudiantes y matrículas</p>
        </div>
 
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex bg-green dark:bg-stone-800 rounded-lg p-1">
                <button wire:click="$set('tab', 'todos')" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $tab == 'todos' ? 'bg-blue-600 text-green' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700' }}">
                    Todos
                </button>
                <button wire:click="$set('tab', 'activos')" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $tab == 'activos' ? 'bg-blue-600 text-green' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700' }}">
                    Activos
                </button>
                <button wire:click="$set('tab', 'inactivos')" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $tab == 'inactivos' ? 'bg-blue-600 text-green' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700' }}">
                    Inactivos
                </button>
            </div>
            
            <div class="flex-1">
                <input type="text" 
                       wire:model.live="busqueda" 
                       placeholder="Buscar por DNI, nombre, apellido, email o teléfono..."
                       class="w-full px-4 py-2 bg-green dark:bg-stone-800 border border-stone-300 dark:border-stone-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        
        <!-- Tarjetas de Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Estudiantes -->
            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green/80">Total Estudiantes</p>
                        <p class="text-3xl font-bold text-green mt-2">{{ number_format($estadisticas['total']) }}</p>
                    </div>
                    <div class="p-3 bg-green/20 rounded-lg">
                        <svg class="w-8 h-8 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13 0c-1.657 0-3-4.03-3-9s1.343-9 3-9" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Estudiantes Activos -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-500 rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green/80">Estudiantes Activos</p>
                        <p class="text-3xl font-bold text-green mt-2">{{ number_format($estadisticas['activos']) }}</p>
                        <p class="text-sm text-green/80 mt-1">{{ number_format(($estadisticas['activos'] / max($estadisticas['total'], 1)) * 100, 1) }}% del total</p>
                    </div>
                    <div class="p-3 bg-green/20 rounded-lg">
                        <svg class="w-8 h-8 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Con Matrículas -->
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green/80">Con Matrículas</p>
                        <p class="text-3xl font-bold text-green mt-2">{{ number_format($estadisticas['con_matriculas']) }}</p>
                        <p class="text-sm text-green/80 mt-1">{{ number_format(($estadisticas['con_matriculas'] / max($estadisticas['total'], 1)) * 100, 1) }}% del total</p>
                    </div>
                    <div class="p-3 bg-green/20 rounded-lg">
                        <svg class="w-8 h-8 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Saldo Pendiente -->
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green/80">Saldo Pendiente</p>
                        <p class="text-3xl font-bold text-green mt-2">L. {{ number_format($estadisticas['saldo_total'], 2) }}</p>
                        <p class="text-sm text-green/80 mt-1">Total de saldos activos</p>
                    </div>
                    <div class="p-3 bg-green/20 rounded-lg">
                        <svg class="w-8 h-8 text-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contenido Principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Lista de Estudiantes (2/3 del ancho) -->
            <div class="lg:col-span-2">
                <div class="bg-green dark:bg-stone-800 rounded-xl shadow">
                    <div class="px-6 py-4 border-b border-stone-200 dark:border-stone-700">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-stone-900 dark:text-green flex items-center gap-2">
                                👥 Lista de Estudiantes
                                <span class="text-sm font-normal text-stone-500 dark:text-stone-400">
                                    ({{ $estudiantes->total() }} registros)
                                </span>
                            </h2>
                            @can('estudiantes.estudiantes.crear')
                            <a href="{{ route('estudiantes.create') }}" 
                               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-green rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Nuevo Estudiante
                            </a>
                            @endcan
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-stone-50 dark:bg-stone-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Estudiante</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Contacto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Matrículas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-stone-500 dark:text-stone-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-stone-200 dark:divide-stone-700">
                                @forelse($estudiantes as $estudiante)
                                <tr class="hover:bg-stone-50 dark:hover:bg-stone-750 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                    <span class="text-blue-600 dark:text-blue-400 font-medium text-sm">
                                                        {{ substr($estudiante->nombre, 0, 1) }}{{ substr($estudiante->apellido, 0, 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-stone-900 dark:text-green">
                                                    {{ $estudiante->apellido }}, {{ $estudiante->nombre }}
                                                </div>
                                                <div class="text-sm text-stone-500 dark:text-stone-400">
                                                    DNI: {{ $estudiante->dni }}
                                                </div>
                                                @if($estudiante->fecha_nacimiento)
                                                <div class="text-xs text-stone-500 dark:text-stone-400 mt-1">
                                                    📅 {{ $estudiante->fecha_nacimiento->format('d/m/Y') }}
                                                    @if($estudiante->edad)
                                                    <span class="ml-2">({{ $estudiante->edad }} años)</span>
                                                    @endif
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-stone-900 dark:text-green">
                                            @if($estudiante->email)
                                            <div class="flex items-center gap-2 mb-1">
                                                <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                <span class="truncate max-w-[150px]">{{ $estudiante->email }}</span>
                                            </div>
                                            @endif
                                            @if($estudiante->telefono)
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                                <span>{{ $estudiante->telefono }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col gap-2">
                                            @if($estudiante->matriculas_count > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                📚 {{ $estudiante->matriculas_count }} módulo(s)
                                            </span>
                                            @endif
                                            @if($estudiante->matriculas_tutorias_count > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                🎓 {{ $estudiante->matriculas_tutorias_count }} tutoría(s)
                                            </span>
                                            @endif
                                            @if($estudiante->matriculas_count == 0 && $estudiante->matriculas_tutorias_count == 0)
                                            <span class="text-sm text-stone-500 dark:text-stone-400">Sin matrículas</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            @if($estudiante->estado)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                Activo
                                            </span>
                                            @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                Inactivo
                                            </span>
                                            @endif
                                            @can('estudiantes.estudiantes.editar')
                                            <button wire:click="toggleEstado({{ $estudiante->id }})"
                                                    class="text-xs text-stone-500 dark:text-stone-400 hover:text-blue-600 dark:hover:text-blue-400">
                                                {{ $estudiante->estado ? 'Desactivar' : 'Activar' }}
                                            </button>
                                            @endcan
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <button wire:click="verDetalle({{ $estudiante->id }})"
                                                    class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                                    title="Ver detalles">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            @can('estudiantes.estudiantes.editar')
                                            <a href="{{ route('estudiantes.edit', $estudiante->id) }}"
                                               class="p-2 text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-900/30 rounded-lg transition-colors"
                                               title="Editar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            @endcan
                                            @can('estudiantes.estudiantes.editar')
                                            <button wire:click="confirmarEliminar({{ $estudiante->id }})"
                                                    class="p-2 text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                                    title="Eliminar">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                            @endcan
                                            @can('matriculas.matriculas.crear')
                                            <a href="{{ route('matriculas.create') }}?estudiante_id={{ $estudiante->id }}"
                                               class="p-2 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg transition-colors"
                                               title="Nueva matrícula">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                            </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="max-w-md mx-auto">
                                            <div class="w-24 h-24 mx-auto bg-stone-100 dark:bg-stone-800 rounded-full flex items-center justify-center mb-6">
                                                <svg class="w-12 h-12 text-stone-300 dark:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-semibold text-stone-700 dark:text-stone-300 mb-2">No hay estudiantes</h3>
                                            <p class="text-stone-500 dark:text-stone-400 mb-4">
                                                @if($busqueda)
                                                    No se encontraron resultados para "{{ $busqueda }}"
                                                @else
                                                    No hay estudiantes registrados en el sistema.
                                                @endif
                                            </p>
                                            @can('estudiantes.estudiantes.crear')
                                            <a href="{{ route('estudiantes.create') }}" 
                                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-green rounded-lg font-medium transition-colors">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Agregar primer estudiante
                                            </a>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($estudiantes->hasPages())
                    <div class="px-6 py-4 border-t border-stone-200 dark:border-stone-700">
                        {{ $estudiantes->links() }}
                    </div>
                    @endif
                </div>
            </div>
    
            <div class="space-y-8">
                <div class="bg-green dark:bg-stone-800 rounded-xl shadow">
                    <div class="px-6 py-4 border-b border-stone-200 dark:border-stone-700">
                        <h2 class="text-lg font-semibold text-stone-900 dark:text-green flex items-center gap-2">
                            📋 Matrículas Recientes
                        </h2>
                    </div>
                    <div class="divide-y divide-stone-200 dark:divide-stone-700">
                        @forelse($matriculasRecientes as $matricula)
                        <div class="p-4 hover:bg-stone-50 dark:hover:bg-stone-750 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-stone-900 dark:text-green truncate max-w-[140px]">
                                    👤 {{ $matricula['estudiante'] ?? 'Sin nombre' }}
                                </span>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ ($matricula['tipo'] ?? '') == 'modulo' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' }}">
                                    {{ ($matricula['tipo'] ?? '') == 'modulo' ? '📚 Módulo' : '🎓 Tutoría' }}
                                </span>
                            </div>
                            <p class="text-sm text-stone-600 dark:text-stone-400 mb-2 truncate">
                                {{ $matricula['curso'] ?? 'Sin curso' }}
                                @if(isset($matricula['codigo']) && $matricula['codigo'])
                                    <span class="text-xs text-stone-500 dark:text-stone-500">({{ $matricula['codigo'] }})</span>
                                @endif
                            </p>
                            <div class="flex justify-between items-center text-sm">
                                <div class="flex items-center gap-2">
                                    @if(isset($matricula['fecha']))
                                    <span class="text-stone-500 dark:text-stone-400">{{ $matricula['fecha']->format('d/m H:i') }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    @if(isset($matricula['estado']))
                                    <span class="text-xs px-2 py-1 rounded {{ $matricula['estado'] == 'activa' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' }}">
                                        {{ $matricula['estado'] }}
                                    </span>
                                    @endif
                                    @if(isset($matricula['saldo']) && $matricula['saldo'] > 0)
                                    <span class="font-medium text-red-600 dark:text-red-400">
                                        L. {{ number_format($matricula['saldo'], 2) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2 flex gap-2">
                                @if(isset($matricula['tipo']) && isset($matricula['id']) && isset($matricula['estudiante_id']))
                                    @if($matricula['tipo'] == 'modulo')
                                    <a href="{{ route('matriculas.edit', $matricula['id']) }}" 
                                    class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                        Editar
                                    </a>
                                    @else
                                    <a href="{{ route('matriculas-tutorias.edit', $matricula['id']) }}" 
                                    class="text-xs text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300">
                                        Editar
                                    </a>
                                    @endif
                                    <a href="{{ route('estudiantes.edit', $matricula['estudiante_id']) }}" 
                                    class="text-xs text-stone-600 dark:text-stone-400 hover:text-stone-800 dark:hover:text-stone-300">
                                        Ver estudiante
                                    </a>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="p-8 text-center">
                            <div class="w-16 h-16 mx-auto bg-stone-100 dark:bg-stone-800 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-stone-300 dark:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="text-stone-500 dark:text-stone-400">No hay matrículas recientes</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Acciones Rápidas -->
                <div class="bg-green dark:bg-stone-800 rounded-xl shadow">
                    <div class="px-6 py-4 border-b border-stone-200 dark:border-stone-700">
                        <h2 class="text-lg font-semibold text-stone-900 dark:text-green flex items-center gap-2">
                            ⚡ Acciones Rápidas
                        </h2>
                    </div>
                    <div class="p-4 space-y-3">
                        @can('estudiantes.estudiantes.crear')
                        <a href="{{ route('estudiantes.create') }}" 
                           class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors group">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200 dark:group-hover:bg-blue-800">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-blue-700 dark:text-blue-300 font-medium block">Nuevo Estudiante</span>
                                    <span class="text-xs text-blue-600 dark:text-blue-400">Registrar estudiante</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @endcan
                        
                        @can('matriculas.matriculas.crear')
                        <a href="{{ route('matriculas.create') }}" 
                           class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-lg transition-colors group">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 dark:group-hover:bg-green-800">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-green-700 dark:text-green-300 font-medium block">Nueva Matrícula</span>
                                    <span class="text-xs text-green-600 dark:text-green-400">Módulo o tutoría</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @endcan
                        
                        @can('matriculas.matriculas-tutorias.crear')
                        <a href="{{ route('matriculas-tutoria.create') }}" 
                           class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/30 hover:bg-purple-100 dark:hover:bg-purple-900/50 rounded-lg transition-colors group">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-3 group-hover:bg-purple-200 dark:group-hover:bg-purple-800">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-purple-700 dark:text-purple-300 font-medium block">Nueva Tutoría</span>
                                    <span class="text-xs text-purple-600 dark:text-purple-400">Matrícula de tutoría</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal de Detalle del Estudiante -->
        @if($mostrarModalDetalle && $estudianteDetalle)
        <div class="fixed inset-0 bg-green/70 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:key="modal-detalle">
            <div class="bg-green dark:bg-stone-800 rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-stone-200 dark:border-stone-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <span class="text-blue-600 dark:text-blue-400 font-bold text-lg">
                                    {{ substr($estudianteDetalle->nombre, 0, 1) }}{{ substr($estudianteDetalle->apellido, 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-stone-900 dark:text-green">{{ $estudianteDetalle->apellido }}, {{ $estudianteDetalle->nombre }}</h3>
                                <p class="text-sm text-stone-600 dark:text-stone-400">DNI: {{ $estudianteDetalle->dni }}</p>
                            </div>
                        </div>
                        <button wire:click="$set('mostrarModalDetalle', false)"
                                class="text-stone-400 hover:text-stone-600 dark:text-stone-500 dark:hover:text-stone-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-sm font-medium text-stone-700 dark:text-stone-300 mb-3">📋 Información Personal</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-stone-500 dark:text-stone-400">Fecha de nacimiento:</span>
                                        <span class="font-medium">{{ $estudianteDetalle->fecha_nacimiento?->format('d/m/Y') ?? 'No especificada' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-stone-500 dark:text-stone-400">Sexo:</span>
                                        <span class="font-medium">{{ $estudianteDetalle->sexo == 'M' ? 'Masculino' : 'Femenino' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-stone-500 dark:text-stone-400">Estado:</span>
                                        <span class="font-medium {{ $estudianteDetalle->estado ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $estudianteDetalle->estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-stone-700 dark:text-stone-300 mb-3">📞 Información de Contacto</h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-stone-500 dark:text-stone-400">Email:</span>
                                        <span class="font-medium">{{ $estudianteDetalle->email ?? 'No especificado' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-stone-500 dark:text-stone-400">Teléfono:</span>
                                        <span class="font-medium">{{ $estudianteDetalle->telefono ?? 'No especificado' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-stone-500 dark:text-stone-400">Dirección:</span>
                                        <span class="font-medium text-right">{{ $estudianteDetalle->direccion ?? 'No especificada' }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            @if($estudianteDetalle->nombre_tutor || $estudianteDetalle->telefono_tutor)
                            <div>
                                <h4 class="text-sm font-medium text-stone-700 dark:text-stone-300 mb-3">👨‍🏫 Información del Tutor</h4>
                                <div class="space-y-3">
                                    @if($estudianteDetalle->nombre_tutor)
                                    <div class="flex justify-between">
                                        <span class="text-stone-500 dark:text-stone-400">Nombre del tutor:</span>
                                        <span class="font-medium">{{ $estudianteDetalle->nombre_tutor }}</span>
                                    </div>
                                    @endif
                                    @if($estudianteDetalle->telefono_tutor)
                                    <div class="flex justify-between">
                                        <span class="text-stone-500 dark:text-stone-400">Teléfono del tutor:</span>
                                        <span class="font-medium">{{ $estudianteDetalle->telefono_tutor }}</span>
                                    </div>
                                    @endif
                                    @if($estudianteDetalle->email_tutor)
                                    <div class="flex justify-between">
                                        <span class="text-stone-500 dark:text-stone-400">Email del tutor:</span>
                                        <span class="font-medium">{{ $estudianteDetalle->email_tutor }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-stone-700 dark:text-stone-300 mb-3">🎓 Matrículas del Estudiante</h4>
                            
                            @if($estudianteDetalle->matriculas->count() > 0)
                            <div class="mb-6">
                                <h5 class="text-xs font-medium text-blue-600 dark:text-blue-400 mb-2">📚 Módulos Matriculados</h5>
                                <div class="space-y-3">
                                    @foreach($estudianteDetalle->matriculas as $matricula)
                                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="font-medium text-blue-800 dark:text-blue-300">{{ $matricula->modulo->nombre }}</p>
                                                <p class="text-xs text-blue-600 dark:text-blue-400">Código: {{ $matricula->modulo->codigo }}</p>
                                            </div>
                                            <span class="text-xs px-2 py-1 rounded {{ $matricula->estado == 'activa' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' }}">
                                                {{ $matricula->estado }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-stone-600 dark:text-stone-400">
                                            <div class="flex justify-between">
                                                <span>Fecha matrícula:</span>
                                                <span>{{ $matricula->fecha_matricula->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Saldo pendiente:</span>
                                                <span class="font-bold {{ $matricula->saldo_pendiente > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                    L. {{ number_format($matricula->saldo_pendiente, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            
                            @if($estudianteDetalle->matriculasTutorias->count() > 0)
                            <div>
                                <h5 class="text-xs font-medium text-green-600 dark:text-green-400 mb-2">🎓 Tutorías Matriculadas</h5>
                                <div class="space-y-3">
                                    @foreach($estudianteDetalle->matriculasTutorias as $tutoria)
                                    <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="font-medium text-green-800 dark:text-green-300">{{ $tutoria->tutoria->nombre }}</p>
                                                @if($tutoria->tutoria->materia)
                                                <p class="text-xs text-green-600 dark:text-green-400">Materia: {{ $tutoria->tutoria->materia }}</p>
                                                @endif
                                            </div>
                                            <span class="text-xs px-2 py-1 rounded {{ $tutoria->estado == 'activa' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' }}">
                                                {{ $tutoria->estado }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-stone-600 dark:text-stone-400">
                                            <div class="flex justify-between">
                                                <span>Fecha inicio:</span>
                                                <span>{{ $tutoria->fecha_inicio->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Saldo pendiente:</span>
                                                <span class="font-bold {{ $tutoria->saldo_pendiente > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                    L. {{ number_format($tutoria->saldo_pendiente, 2) }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span>Sesiones:</span>
                                                <span>{{ $tutoria->tutorias_registradas }} registradas / {{ $tutoria->tutorias_pagadas }} pagadas</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            
                            @if($estudianteDetalle->matriculas->count() == 0 && $estudianteDetalle->matriculasTutorias->count() == 0)
                            <div class="text-center py-8">
                                <div class="w-16 h-16 mx-auto bg-stone-100 dark:bg-stone-800 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-stone-300 dark:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="text-stone-500 dark:text-stone-400">Este estudiante no tiene matrículas</p>
                                @can('matriculas.matriculas.crear')
                                <a href="{{ route('matriculas.create') }}?estudiante_id={{ $estudianteDetalle->id }}" 
                                   class="inline-block mt-3 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    + Agregar matrícula
                                </a>
                                @endcan
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-850">
                    <div class="flex justify-between">
                        <div class="flex gap-3">
                            @can('estudiantes.estudiantes.editar')
                            <a href="{{ route('estudiantes.edit', $estudianteDetalle->id) }}"
                               class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-green rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </a>
                            @endcan
                            @can('matriculas.matriculas.crear')
                            <a href="{{ route('matriculas.create') }}?estudiante_id={{ $estudianteDetalle->id }}"
                               class="px-4 py-2 bg-green-600 hover:bg-green-700 text-green rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Nueva Matrícula
                            </a>
                            @endcan
                        </div>
                        <button wire:click="$set('mostrarModalDetalle', false)"
                                class="px-4 py-2 text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700 rounded-lg text-sm font-medium transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Modal de Confirmación de Eliminación -->
        @if($mostrarModalEliminar && $estudianteAEliminar)
        <div class="fixed inset-0 bg-green/70 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:key="modal-eliminar">
            <div class="bg-green dark:bg-stone-800 rounded-xl shadow-2xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-stone-200 dark:border-stone-700 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/30 dark:to-orange-900/30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.698-.833-2.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-stone-900 dark:text-green">Confirmar eliminación</h3>
                            <p class="text-sm text-stone-600 dark:text-stone-400">Esta acción no se puede deshacer</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <p class="text-stone-700 dark:text-stone-300 mb-4">
                        ¿Estás seguro de que deseas eliminar al estudiante 
                        <span class="font-bold">{{ $estudianteAEliminar->apellido }}, {{ $estudianteAEliminar->nombre }}</span>?
                    </p>
                    <p class="text-sm text-stone-500 dark:text-stone-400 mb-6">
                        DNI: {{ $estudianteAEliminar->dni }}
                    </p>
                    
                    @php
                        $tieneMatriculas = $estudianteAEliminar->matriculas()
                            ->whereIn('estado', ['activa', 'pendiente'])
                            ->exists();
                        $tieneTutorias = $estudianteAEliminar->matriculasTutorias()
                            ->whereIn('estado', ['activa', 'pendiente'])
                            ->exists();
                    @endphp
                    
                    @if($tieneMatriculas || $tieneTutorias)
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
                        <div class="flex items-center gap-2 text-red-600 dark:text-red-400 mb-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.698-.833-2.464 0L4.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                            <span class="font-medium">¡Advertencia!</span>
                        </div>
                        <p class="text-sm text-red-700 dark:text-red-300">
                            Este estudiante tiene matrículas activas o pendientes. No se puede eliminar hasta que cancele o complete todas sus matrículas.
                        </p>
                    </div>
                    @endif
                </div>
                
                <div class="px-6 py-4 border-t border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-850">
                    <div class="flex justify-end gap-3">
                        <button wire:click="$set('mostrarModalEliminar', false)"
                                class="px-5 py-2 text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700 rounded-lg font-medium transition-colors">
                            Cancelar
                        </button>
                        @if(!$tieneMatriculas && !$tieneTutorias)
                        <button wire:click="eliminarEstudiante"
                                class="px-5 py-2 bg-red-600 hover:bg-red-700 text-green rounded-lg font-medium transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Eliminar
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Notificaciones -->
        @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 4000)"
             class="fixed bottom-4 right-4 z-50">
            <div class="bg-gradient-to-r from-emerald-500 to-green-500 text-green px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-fade-in-up">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif
        
        @if(session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 z-50">
            <div class="bg-gradient-to-r from-red-500 to-orange-500 text-green px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-fade-in-up">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif
    </div>
</div>