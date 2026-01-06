<div class="min-h-screen bg-stone-50 dark:bg-stone-900">
    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-stone-900 dark:text-white">Dashboard de Pagos</h1>
            <p class="text-stone-600 dark:text-stone-400 mt-2">Gestión de pagos pendientes por mensualidad</p>
        </div>
        
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex bg-white dark:bg-stone-800 rounded-lg p-1">
                <button wire:click="$set('tab', 'hoy')" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $tab == 'hoy' ? 'bg-blue-600 text-white' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700' }}">
                    Hoy
                </button>
                <button wire:click="$set('tab', 'semana')" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $tab == 'semana' ? 'bg-blue-600 text-white' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700' }}">
                    Esta semana
                </button>
                <button wire:click="$set('tab', 'mes')" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $tab == 'mes' ? 'bg-blue-600 text-white' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700' }}">
                    Este mes
                </button>
            </div>
            
            <div class="flex bg-white dark:bg-stone-800 rounded-lg p-1">
                <button wire:click="$set('tipo', 'todos')" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $tipo == 'todos' ? 'bg-blue-600 text-white' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700' }}">
                    Todos
                </button>
                <button wire:click="$set('tipo', 'modulos')" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $tipo == 'modulos' ? 'bg-blue-600 text-white' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700' }}">
                    Módulos
                </button>
                <button wire:click="$set('tipo', 'tutorias')" 
                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $tipo == 'tutorias' ? 'bg-blue-600 text-white' : 'text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700' }}">
                    Tutorías
                </button>
            </div>
            
            <div class="flex-1">
                <input type="text" 
                       wire:model.live.debounce.300ms="busqueda" 
                       placeholder="Buscar estudiante, curso o materia..."
                       class="w-full px-4 py-2 bg-white dark:bg-stone-800 border border-stone-300 dark:border-stone-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-gradient-to-r from-emerald-500 to-green-500 rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-white/80">Ingresos del mes</p>
                        <p class="text-3xl font-bold text-white mt-2">L. {{ number_format($metricas['ingresos_mes'], 2) }}</p>
                        <p class="text-sm text-white/80 mt-1">Total recaudado este mes</p>
                    </div>
                    <div class="p-3 bg-white/20 rounded-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-stone-800 rounded-xl p-6 shadow">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <p class="text-sm text-stone-500 dark:text-stone-400">Módulos pendientes</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $metricas['mes']['modulos'] }}</p>
                        <div class="flex items-center justify-center gap-2 mt-2">
                            <div class="flex flex-col">
                                <span class="text-xs text-stone-500 dark:text-stone-400">Hoy: {{ $metricas['hoy']['modulos'] }}</span>
                                <span class="text-xs text-stone-500 dark:text-stone-400">Semana: {{ $metricas['semana']['modulos'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-stone-500 dark:text-stone-400">Tutorías pendientes</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $metricas['mes']['tutorias'] }}</p>
                        <div class="flex items-center justify-center gap-2 mt-2">
                            <div class="flex flex-col">
                                <span class="text-xs text-stone-500 dark:text-stone-400">Hoy: {{ $metricas['hoy']['tutorias'] }}</span>
                                <span class="text-xs text-stone-500 dark:text-stone-400">Semana: {{ $metricas['semana']['tutorias'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-stone-800 rounded-xl shadow">
                    <div class="px-6 py-4 border-b border-stone-200 dark:border-stone-700">
                        <div class="flex justify-between items-center">
                            <h2 class="text-lg font-semibold text-stone-900 dark:text-white">
                                📋 Pagos pendientes por mensualidad
                                <span class="text-sm font-normal text-stone-500 dark:text-stone-400 ml-2">
                                    ({{ $pendientes->count() }} registros)
                                </span>
                            </h2>
                            <div class="text-sm text-stone-500 dark:text-stone-400 flex items-center gap-2">
                                <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span>
                                <span>Módulos: {{ $pendientes->where('tipo', 'modulo')->count() }}</span>
                                <span class="inline-block w-2 h-2 rounded-full bg-green-500 ml-2"></span>
                                <span>Tutorías: {{ $pendientes->where('tipo', 'tutoria')->count() }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-stone-200 dark:divide-stone-700">
                        @forelse($pendientes as $item)
                            <div class="p-6 hover:bg-stone-50 dark:hover:bg-stone-750 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-3">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $item['tipo'] == 'modulo' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' }}">
                                                {{ $item['tipo'] == 'modulo' ? '📚 Módulo' : '🎓 Tutoría' }}
                                            </span>
                                            
                                            @if($item['destino'] == 'tutor')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                                    👨‍🏫 Tutor
                                                </span>
                                            @endif
                                            
                                            @if($item['estado'] == 'vencido')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                    ⚠️ VENCIDO
                                                </span>
                                            @elseif($item['estado'] == 'urgente')
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                                    🔥 URGENTE
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                                    ⏳ PENDIENTE
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <h3 class="font-medium text-lg text-stone-900 dark:text-white mb-1">
                                            👤 {{ $item['nombre'] }}
                                            @if($item['nombre_tutor'])
                                                <span class="text-sm text-stone-500 dark:text-stone-400"> (Tutor: {{ $item['nombre_tutor'] }})</span>
                                            @endif
                                        </h3>
                                        
                                        @if($item['tipo'] == 'modulo')
                                            <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                    <div class="space-y-1">
                                                        <div class="text-sm text-stone-500 dark:text-stone-400">Módulo / Mes pendiente</div>
                                                        <div class="font-medium text-stone-900 dark:text-white">
                                                            {{ $item['curso'] }}
                                                            <span class="text-sm text-blue-600 dark:text-blue-400 ml-1">
                                                                (Mes {{ $item['mes_actual'] ?? 1 }} de {{ $item['duracion_meses'] ?? 3 }})
                                                            </span>
                                                        </div>
                                                        @if($item['ultimo_mes_pagado'])
                                                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                                            Último pago: {{ \Carbon\Carbon::createFromFormat('Y-m', $item['ultimo_mes_pagado'])->format('M Y') }}
                                                        </div>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($item['telefono'])
                                                    <div class="space-y-1">
                                                        <div class="text-sm text-stone-500 dark:text-stone-400">Teléfono</div>
                                                        <div class="font-medium text-stone-900 dark:text-white flex items-center gap-2">
                                                            <span>📞 {{ $item['telefono'] }}</span>
                                                            @if($item['destino'] == 'tutor')
                                                                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300 rounded">
                                                                    Tutor
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endif
                                                    
                                                    <div class="space-y-1">
                                                        <div class="text-sm text-stone-500 dark:text-stone-400">Mensualidad</div>
                                                        <div class="font-medium text-stone-900 dark:text-white">
                                                            💰 L. {{ number_format($item['precio_mensual'], 2) }}
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="space-y-1">
                                                        <div class="text-sm text-stone-500 dark:text-stone-400">Mes a pagar</div>
                                                        <div class="font-medium text-blue-600 dark:text-blue-400">
                                                            📆 Mes {{ $item['mes_nombre'] ?? 'actual' }}
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="space-y-1">
                                                        <div class="text-sm text-stone-500 dark:text-stone-400">Total este mes</div>
                                                        <div class="font-bold text-lg text-red-600 dark:text-red-400">
                                                            💵 L. {{ number_format($item['saldo'], 2) }}
                                                        </div>
                                                        @if($item['total_pendiente'] > $item['saldo'])
                                                        <div class="text-xs text-stone-500 dark:text-stone-400">
                                                            Saldo total: L. {{ number_format($item['total_pendiente'], 2) }}
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                                    <div class="space-y-1">
                                                        <div class="text-sm text-stone-500 dark:text-stone-400">Tutoría</div>
                                                        <div class="font-medium text-stone-900 dark:text-white">
                                                            {{ $item['curso'] }}
                                                            @if(!empty($item['materia']))
                                                                <span class="text-sm text-green-600 dark:text-green-400 ml-1">({{ $item['materia'] }})</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    @if($item['telefono'])
                                                    <div class="space-y-1">
                                                        <div class="text-sm text-stone-500 dark:text-stone-400">Teléfono</div>
                                                        <div class="font-medium text-stone-900 dark:text-white flex items-center gap-2">
                                                            <span>📞 {{ $item['telefono'] }}</span>
                                                            @if($item['destino'] == 'tutor')
                                                                <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300 rounded">
                                                                    Tutor
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endif
                                                    
                                                    <div class="space-y-1">
                                                        <div class="text-sm text-stone-500 dark:text-stone-400">Precio por hora</div>
                                                        <div class="font-medium text-stone-900 dark:text-white">
                                                            💰 L. {{ number_format($item['precio_hora'], 2) }}
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="space-y-1">
                                                        <div class="text-sm text-stone-500 dark:text-stone-400">Horas pendientes</div>
                                                        <div class="font-medium text-red-600 dark:text-red-400 flex items-center gap-2">
                                                            <span>⏰ {{ $item['horas_pendientes'] }}</span>
                                                            @if($item['horas_asistidas'] > 0 && isset($item['total_horas']))
                                                                <span class="text-xs text-stone-500 dark:text-stone-400">
                                                                    ({{ $item['horas_asistidas'] }}/{{ $item['total_horas'] }} asistidas)
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="space-y-1">
                                                        <div class="text-sm text-stone-500 dark:text-stone-400">Total a pagar</div>
                                                        <div class="font-bold text-lg text-red-600 dark:text-red-400">
                                                            💵 L. {{ number_format($item['saldo'], 2) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex flex-col gap-3 ml-6 min-w-[140px]">
                                        <button wire:click="abrirModalWhatsApp(@js($item))"
                                                class="px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.76.982.998-3.675-.236-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.9 6.994c-.004 5.45-4.438 9.88-9.888 9.88m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.333.158 11.892c0 2.096.547 4.142 1.588 5.945L.058 24l6.306-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.333 11.89-11.893 0-3.18-1.24-6.162-3.495-8.411"/>
                                            </svg>
                                            WhatsApp
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <div class="max-w-md mx-auto">
                                    <div class="w-24 h-24 mx-auto bg-stone-100 dark:bg-stone-800 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-12 h-12 text-stone-300 dark:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-semibold text-stone-700 dark:text-stone-300 mb-2">🎉 ¡Todo al día!</h3>
                                    <p class="text-stone-500 dark:text-stone-400 mb-4">
                                        No hay pagos pendientes para el período seleccionado.
                                    </p>
                                    @if($busqueda)
                                        <p class="text-sm text-stone-400 dark:text-stone-500">
                                            Intenta con otros términos de búsqueda.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <div class="space-y-8">
                <div class="bg-white dark:bg-stone-800 rounded-xl shadow">
                    <div class="px-6 py-4 border-b border-stone-200 dark:border-stone-700">
                        <h2 class="text-lg font-semibold text-stone-900 dark:text-white flex items-center gap-2">
                            💰 Pagos recientes
                            <span class="text-sm font-normal text-stone-500 dark:text-stone-400">
                                (últimas 24 horas)
                            </span>
                        </h2>
                    </div>
                    <div class="divide-y divide-stone-200 dark:divide-stone-700">
                        @forelse($pagosRecientes as $pago)
                            <div class="p-4 hover:bg-stone-50 dark:hover:bg-stone-750 transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-stone-900 dark:text-white truncate max-w-[140px]">
                                        👤 {{ $pago['estudiante'] }}
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $pago['tipo'] == 'modulo' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' }}">
                                        {{ $pago['tipo'] == 'modulo' ? '📚 Módulo' : '🎓 Tutoría' }}
                                    </span>
                                </div>
                                <p class="text-sm text-stone-600 dark:text-stone-400 mb-2 truncate">
                                    {{ $pago['curso'] }}
                                </p>
                                <div class="flex justify-between items-center text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="text-stone-500 dark:text-stone-400">🕒 {{ $pago['fecha']->format('H:i') }}</span>
                                        <span class="text-xs text-stone-400 dark:text-stone-500">|</span>
                                        <span class="text-stone-500 dark:text-stone-400">{{ $pago['fecha']->format('d/m') }}</span>
                                    </div>
                                    <span class="font-bold text-emerald-600 dark:text-emerald-400">
                                        L. {{ number_format($pago['monto'], 2) }}
                                    </span>
                                </div>
                                @if($pago['metodo'])
                                <div class="mt-2 text-xs text-stone-500 dark:text-stone-400">
                                    Método: {{ ucfirst($pago['metodo']) }}
                                </div>
                                @endif
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <div class="w-16 h-16 mx-auto bg-stone-100 dark:bg-stone-800 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-stone-300 dark:text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-stone-500 dark:text-stone-400">No hay pagos recientes</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <div class="bg-white dark:bg-stone-800 rounded-xl shadow">
                    <div class="px-6 py-4 border-b border-stone-200 dark:border-stone-700">
                        <h2 class="text-lg font-semibold text-stone-900 dark:text-white flex items-center gap-2">
                            ⚡ Acciones rápidas
                        </h2>
                    </div>
                    <div class="p-4 space-y-3">
                        <a href="{{ route('matriculas.create') }}" 
                           class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors group">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3 group-hover:bg-blue-200 dark:group-hover:bg-blue-800">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-blue-700 dark:text-blue-300 font-medium block">Nueva matrícula</span>
                                    <span class="text-xs text-blue-600 dark:text-blue-400">Módulo o tutoría</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                        
                        <a href="{{ route('pagos.create') }}" 
                           class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-lg transition-colors group">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3 group-hover:bg-green-200 dark:group-hover:bg-green-800">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="text-green-700 dark:text-green-300 font-medium block">Registrar pago</span>
                                    <span class="text-xs text-green-600 dark:text-green-400">Actualizar saldos</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        @if($mostrarModalWhatsApp && !empty($datosModal))
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50 p-4" wire:key="modal-whatsapp">
            <div class="bg-white dark:bg-stone-800 rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-hidden transform transition-all" wire:click.outside="$set('mostrarModalWhatsApp', false)">
                <div class="px-6 py-4 border-b border-stone-200 dark:border-stone-700 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.76.982.998-3.675-.236-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.9 6.994c-.004 5.45-4.438 9.88-9.888 9.88m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.333.158 11.892c0 2.096.547 4.142 1.588 5.945L.058 24l6.306-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.333 11.89-11.893 0-3.18-1.24-6.162-3.495-8.411"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-stone-900 dark:text-white">Enviar por WhatsApp</h3>
                                <p class="text-sm text-stone-600 dark:text-stone-400">Recordatorio de pago</p>
                            </div>
                        </div>
                        <button wire:click="$set('mostrarModalWhatsApp', false)"
                                class="text-stone-400 hover:text-stone-600 dark:text-stone-500 dark:hover:text-stone-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="p-6 space-y-6 overflow-y-auto max-h-[60vh]">
                    <div>
                        <h4 class="text-sm font-medium text-stone-700 dark:text-stone-300 mb-3">📱 Seleccionar destinatario:</h4>
                        <div class="space-y-3">
                            @if(!empty($datosModal['telefono_estudiante']))
                            <div class="flex items-center p-4 rounded-lg border-2 transition-all {{ $telefonoDestino == $datosModal['telefono_estudiante'] ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-stone-200 dark:border-stone-700 hover:border-green-300 dark:hover:border-green-700' }}">
                                <input type="radio" wire:model="telefonoDestino" 
                                       wire:click="cambiarTelefonoDestino('{{ $datosModal['telefono_estudiante'] }}')"
                                       value="{{ $datosModal['telefono_estudiante'] }}" 
                                       id="tel_estudiante" class="mr-4 w-5 h-5 text-green-600">
                                <label for="tel_estudiante" class="flex-1 cursor-pointer">
                                    <div class="font-medium text-stone-900 dark:text-white">👤 Estudiante</div>
                                    <div class="text-sm text-stone-600 dark:text-stone-400 mt-1">{{ $datosModal['telefono_estudiante'] }}</div>
                                </label>
                            </div>
                            @endif
                            
                            @if(!empty($datosModal['telefono_tutor']))
                            <div class="flex items-center p-4 rounded-lg border-2 transition-all {{ $telefonoDestino == $datosModal['telefono_tutor'] ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-stone-200 dark:border-stone-700 hover:border-purple-300 dark:hover:border-purple-700' }}">
                                <input type="radio" wire:model="telefonoDestino" 
                                       wire:click="cambiarTelefonoDestino('{{ $datosModal['telefono_tutor'] }}')"
                                       value="{{ $datosModal['telefono_tutor'] }}" 
                                       id="tel_tutor" class="mr-4 w-5 h-5 text-purple-600">
                                <label for="tel_tutor" class="flex-1 cursor-pointer">
                                    <div class="font-medium text-stone-900 dark:text-white">👨‍🏫 Tutor</div>
                                    <div class="text-sm text-stone-600 dark:text-stone-400 mt-1">{{ $datosModal['telefono_tutor'] }}</div>
                                    @if(!empty($datosModal['nombre_tutor']))
                                    <div class="text-xs text-stone-500 dark:text-stone-500 mt-1">{{ $datosModal['nombre_tutor'] }}</div>
                                    @endif
                                </label>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-4 rounded-lg">
                        <h4 class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Información del pago
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-700 dark:text-blue-400">Estudiante:</span>
                                <span class="font-medium text-blue-800 dark:text-blue-300">{{ $datosModal['nombre'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700 dark:text-blue-400">{{ $datosModal['tipo'] == 'modulo' ? 'Módulo:' : 'Tutoría:' }}</span>
                                <span class="font-medium text-blue-800 dark:text-blue-300">{{ $datosModal['curso'] }}</span>
                            </div>
                            @if($datosModal['tipo'] == 'modulo' && $datosModal['ultimo_mes_pagado'])
                            <div class="flex justify-between">
                                <span class="text-blue-700 dark:text-blue-400">Último mes pagado:</span>
                                <span class="font-medium text-green-600 dark:text-green-400">{{ \Carbon\Carbon::createFromFormat('Y-m', $datosModal['ultimo_mes_pagado'])->format('M Y') }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <span class="text-blue-700 dark:text-blue-400">Total a pagar:</span>
                                <span class="font-bold text-lg text-red-600 dark:text-red-400">L. {{ number_format($datosModal['saldo'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-medium text-stone-700 dark:text-stone-300 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Mensaje personalizado
                            </h4>
                            <button wire:click="$set('mensajePersonalizado', '{{ addslashes($this->construirMensajeWhatsApp($datosModal)) }}')"
                                    class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                Restaurar original
                            </button>
                        </div>
                        <textarea wire:model="mensajePersonalizado" 
                                  rows="6"
                                  class="w-full px-4 py-3 border border-stone-300 dark:border-stone-700 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-stone-700 dark:text-white text-sm resize-none"
                                  placeholder="Personaliza el mensaje..."></textarea>
                        <div class="mt-2 text-xs text-stone-500 dark:text-stone-400 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            El mensaje se abrirá en WhatsApp con el número seleccionado
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-stone-200 dark:border-stone-700 bg-stone-50 dark:bg-stone-850">
                    <div class="flex justify-end gap-3">
                        <button wire:click="$set('mostrarModalWhatsApp', false)"
                                class="px-5 py-2.5 text-stone-600 dark:text-stone-300 hover:bg-stone-100 dark:hover:bg-stone-700 rounded-lg font-medium transition-colors">
                            Cancelar
                        </button>
                        <button wire:click="enviarWhatsApp"
                                class="px-5 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg font-medium transition-colors shadow-lg hover:shadow-xl flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.76.982.998-3.675-.236-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.9 6.994c-.004 5.45-4.438 9.88-9.888 9.88m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.333.158 11.892c0 2.096.547 4.142 1.588 5.945L.058 24l6.306-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.333 11.89-11.893 0-3.18-1.24-6.162-3.495-8.411"/>
                            </svg>
                            Abrir WhatsApp
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        @if(session('mensaje'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 4000)"
             class="fixed bottom-4 right-4 z-50">
            <div class="bg-gradient-to-r from-emerald-500 to-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-fade-in-up">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('mensaje') }}</span>
            </div>
        </div>
        @endif
        
        @if(session('error'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-transition
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed bottom-4 right-4 z-50">
            <div class="bg-gradient-to-r from-red-500 to-orange-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-fade-in-up">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('abrirWhatsApp', (event) => {
            if (event.url) {
                window.open(event.url, '_blank', 'noopener,noreferrer');
            }
        });
    });
</script>