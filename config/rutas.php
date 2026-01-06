<?php

return [
    'dashboard' => [
        'titulo' => 'Inicio',
        'icono' => '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12 8-8 8 8M6 10.5V19a1 1 0 0 0 1 1h3v-3a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3h3a1 1 0 0 0 1-1v-8.5" />',
        'route' => 'dashboard',
        'items' => [
            [
                'titulo' => 'Panel Principal',
                'route' => 'dashboard',
                'routes' => ['dashboard'],
                'permisos' => [],
                'icono' => '',
                'always_visible' => true,
                'breadcrumb' => true 
            ]
        ]
    ],

   'secciones' => [
        'titulo' => 'Secciones',
        'icono' => '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 2h5a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2Z" />',
        'route' => 'secciones',
        'breadcrumb_label' => 'Secciones',
        'permisos_modulo' => 'acceso-secciones', 
        'items' => [
            [
                'titulo' => 'Crear secciones',
                'route' => 'secciones',
                'routes' => ['secciones'],
                'permisos' => ['secciones.secciones.ver'], 
                'icono' => '',
                'default_route' => true,
                'breadcrumb' => true
            ],
            [
                'titulo' => 'Modalidades',
                'route' => 'modalidades',
                'routes' => ['modalidades'],
                'permisos' => ['secciones.modalidades.ver'], 
                'icono' => '',
                'default_route' => true,
                'breadcrumb' => true
            ],
            [
                'titulo' => 'Sedes',
                'route' => 'sedes',
                'routes' => ['sedes'],
                'permisos' => ['secciones.sedes.ver'], 
                'icono' => '',
                'default_route' => true,
                'breadcrumb' => true
            ],
        ],
        'footer' => false
    ],
    'oferta-educativa' => [
        'titulo' => 'Oferta Educativa',
        'icono' => '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />',
        'route' => 'modulos',
        'breadcrumb_label' => 'Oferta Educativa',
        'permisos_modulo' => 'acceso-oferta-educativa',
        'items' => [
            [
                'titulo' => 'Módulos',
                'route' => 'modulos',
                'routes' => ['modulos'],
                'permisos' => ['oferta-educativa.modulos.ver'],
                'icono' => '',
                'default_route' => true,
                'breadcrumb' => true
            ],
            [
                'titulo' => 'Tutorías',
                'route' => 'tutorias',
                'routes' => ['tutorias'],
                'permisos' => ['oferta-educativa.tutorias.ver'],
                'icono' => '',
                'breadcrumb' => true
            ],
        ],
        'footer' => false
    ],
    'estudiantes' => [
        'titulo' => 'Estudiantes',
        'icono' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>',
        'route' => 'estudiantes',
        'breadcrumb_label' => 'Estudiantes',
        'permisos_modulo' => 'acceso-estudiantes',
        'items' => [
            [
                'titulo' => 'Gestión de Estudiantes',
                'route' => 'estudiantes',
                'routes' => ['estudiantes'],
                'permisos' => ['estudiantes.estudiantes.ver'],
                'icono' => '',
                'default_route' => true,
                'breadcrumb' => true
            ],
        ],
        'footer' => false
    ],
    'matriculas' => [
        'titulo' => 'Matricular Estudiantes',
        'icono' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>',
        'route' => 'matriculas',
        'breadcrumb_label' => 'matriculas',
        'permisos_modulo' => 'acceso-matriculas',
        'items' => [
            [
                'titulo' => 'Matriculas Módulos',
                'route' => 'matriculas',
                'routes' => ['matriculas'],
                'permisos' => ['matriculas.matriculas.ver'],
                'icono' => '',
                'default_route' => true,
                'breadcrumb' => true
            ],
            [
                'titulo' => 'Matricula Tutorías',
                'route' => 'matriculas-tutorias',
                'routes' => ['matriculas-tutorias'],
                'permisos' => ['matriculas.matriculas-tutorias.ver'],
                'icono' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>',
                'default_route' => false,
                'breadcrumb' => true
            ]
        ],
        'footer' => false
    ],
    'pagos-modulos' => [
        'titulo' => 'Pagos',
        'icono' => '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        'route' => 'pagos',
        'breadcrumb_label' => 'pagos de modulos',
        'permisos_modulo' => 'acceso-pagos-modulos',
        'items' => [
            [
                'titulo' => 'Pagos',
                'route' => 'pagos',
                'routes' => ['pagos'],
                'permisos' => ['pagos-modulos.pagos.ver'],
                'icono' => '',
                'default_route' => true,
                'breadcrumb' => true
            ],
            [
                'titulo' => 'Pagos Tutorias',
                'route' => 'pagos-tutorias',
                'routes' => ['pagos-tutorias'],
                'permisos' => ['pagos-modulos.pagos-tutorias.ver'],
                'icono' => 'fas fa-hand-holding-usd',
                'default_route' => true,
                'breadcrumb' => true
            ],
            [
                'titulo' => 'Historial Pagos Módulos',
                'route' => 'pagos-historial',
                'routes' => ['pagos-historial'],
                'permisos' => ['pagos-modulos.pagos-historial.ver'],
                'icono' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>',
                'default_route' => false,
                'breadcrumb' => true
            ],
            [
                'titulo' => 'Historial Pagos Tutorías',
                'route' => 'historial-pagos-tutorias',
                'routes' => ['historial-pagos-tutorias'],
                'permisos' => ['pagos-modulos.historial-pagos-tutorias.ver'],
                'icono' => 'fas fa-history',
                'default_route' => false,
                'breadcrumb' => true
            ],
        ],
        'footer' => false
    ],
    'configuracion' => [
        'titulo' => 'Configuración',
        'icono' => '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13v-2a1 1 0 0 0-1-1h-.757l-.707-1.707.535-.536a1 1 0 0 0 0-1.414l-1.414-1.414a1 1 0 0 0-1.414 0l-.536.535L14 4.757V4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v.757l-1.707.707-.536-.535a1 1 0 0 0-1.414 0L4.929 6.343a1 1 0 0 0 0 1.414l.536.536L4.757 10H4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h.757l.707 1.707-.535.536a1 1 0 0 0 0 1.414l1.414 1.414a1 1 0 0 0 1.414 0l.536-.535 1.707.707V20a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-.757l1.707-.708.536.536a1 1 0 0 0 1.414 0l1.414-1.414a1 1 0 0 0 0-1.414l-.535-.536.707-1.707H20a1 1 0 0 0 1-1Z" /><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />',
        'route' => 'roles',
        'breadcrumb_label' => 'Configuración',
        'permisos_modulo' => 'acceso-configuracion', 
        'items' => [
            [
                'titulo' => 'Roles',
                'route' => 'roles',
                'routes' => ['roles'],
                'permisos' => ['configuracion.roles.ver'], 
                'icono' => '',
                'default_route' => true,
                'breadcrumb' => true
            ],
            [
                'titulo' => 'Usuarios',
                'route' => 'usuarios',
                'routes' => ['usuarios'],
                'permisos' => ['configuracion.usuarios.ver'], 
                'icono' => '',
                'breadcrumb' => true
            ],
        ],
        'footer' => true
    ],
    'logs' => [
        'titulo' => 'Registros del Sistema',
        'icono' => '<path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M20 6H10m0 0a2 2 0 1 0-4 0m4 0a2 2 0 1 1-4 0m0 0H4m16 6h-2m0 0a2 2 0 1 0-4 0m4 0a2 2 0 1 1-4 0m0 0H4m16 6H10m0 0a2 2 0 1 0-4 0m4 0a2 2 0 1 1-4 0m0 0H4"/>',
        'route' => 'logs',
        'breadcrumb_label' => 'Registros del Sistema',
        'permisos_modulo' => 'acceso-logs', 
        'items' => [
            [
                'titulo' => 'Visor de Logs',
                'route' => 'logs',
                'routes' => ['logs'],
                'permisos' => ['logs.visor.ver'],
                'icono' => '',
                'breadcrumb' => true
            ],
            [
                'titulo' => 'Dashboard de Logs',
                'route' => 'logsdashboard',
                'routes' => ['logsdashboard'],
                'permisos' => ['logs.dashboard.ver'],
                'icono' => '',
                'breadcrumb' => true,
            ],
            [
                'titulo' => 'Sesiones',
                'route' => 'sessions',
                'routes' => ['sessions'],
                'permisos' => ['logs.sessions.ver'],
                'icono' => '',
                'breadcrumb' => true,
            ]
        ],
        'footer' => true
    ],
];