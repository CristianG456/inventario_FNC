<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inventario TIC') &mdash; Federación Nacional de Cafeteros</title>
    
    <link rel="icon" type="image/png" href="{{ asset('imagenes/federacion cafeteros logo.png') }}">
    
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/css/app.css'])
    <style>
        /* Responsive Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }
        @media (max-width: 768px) {
            #sidebar {
                z-index: 1000;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

<div id="sidebar-overlay" class="sidebar-overlay d-md-none"></div>

<nav id="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('imagenes/logo_comite_tolima.png') }}" alt="Logo Comité Tolima">
    </div>
    
    <div class="nav-menu">
        <ul class="nav flex-column">
            
            @can('dashboard.ver')
            <li class="nav-item">
                <a href="{{ Route::has('inicio') ? route('inicio') : route('dashboard') }}" class="nav-link {{ request()->routeIs('inicio', 'dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Inicio
                </a>
            </li>
            @endcan
            
            @can('equipos.ver')
            <li class="nav-item">
                <a href="{{ route('equipos.index') }}" class="nav-link {{ request()->routeIs('equipos.*') ? 'active' : '' }}">
                    <i class="bi bi-display"></i> Activos
                </a>
            </li>
            @endcan
            
            @can('usuarios.ver')
            <li class="nav-item">
                <a href="{{ route('funcionarios.index') }}" class="nav-link {{ request()->routeIs('funcionarios.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Funcionarios
                </a>
            </li>
            @endcan
            
            @can('mesaayuda.ver')
            <li class="nav-item">
                <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                    <i class="bi bi-headset"></i> HelpDesk
                </a>
            </li>
            @endcan
            
            @can('historial.ver')
            <li class="nav-item">
                <a href="{{ route('historial-tecnico.index') }}" class="nav-link {{ request()->routeIs('historial-tecnico.*') ? 'active' : '' }}">
                    <i class="bi bi-tools"></i> Mantenimientos
                </a>
            </li>
            @endcan
            
            @canany(['configuracion.editar', 'campos_personalizados.ver'])
            <li class="nav-item">
                @php
                    $isConfigActive = request()->routeIs('tipo-recursos.*', 'campos-personalizados.*');
                @endphp
                <a class="nav-link {{ $isConfigActive ? 'active' : '' }}" href="#menuConfiguracion" data-bs-toggle="collapse" role="button" aria-expanded="{{ $isConfigActive ? 'true' : 'false' }}" aria-controls="menuConfiguracion">
                    <i class="bi bi-gear"></i> <span>Configuración</span>
                    <i class="bi bi-chevron-down ms-auto" style="font-size: 0.75rem; transition: transform 0.3s;"></i>
                </a>
                <div class="collapse {{ $isConfigActive ? 'show' : '' }}" id="menuConfiguracion" style="visibility: visible;">
                    <ul class="nav flex-column" style="padding-left: 1rem; margin-top: 4px; margin-bottom: 4px;">
                        @can('configuracion.editar')
                        <li class="nav-item">
                            <a href="{{ route('tipo-recursos.index') }}" class="nav-link {{ request()->routeIs('tipo-recursos.*') ? 'active' : '' }}" style="padding-top: 6px; padding-bottom: 6px; margin-bottom: 2px;">
                                <i class="bi bi-tags"></i> <span>Categorías</span>
                            </a>
                        </li>
                        @endcan
                        
                        @can('campos_personalizados.ver')
                        <li class="nav-item">
                            <a href="{{ route('campos-personalizados.index') }}" class="nav-link {{ request()->routeIs('campos-personalizados.*') ? 'active' : '' }}" style="padding-top: 6px; padding-bottom: 6px; margin-bottom: 2px;">
                                <i class="bi bi-ui-checks-grid"></i> <span>Campos Personalizados</span>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </div>
            </li>
            @endcanany
            
            @can('licencias.ver')
            <li class="nav-item">
                <a href="{{ route('licencias.index') }}" class="nav-link {{ request()->routeIs('licencias.*') || request()->routeIs('licencia-asignaciones.*') ? 'active' : '' }}">
                    <i class="bi bi-key"></i> Licencias
                </a>
            </li>
            @endcan


            @can('dashboard.ver')
            <li class="nav-item">
                <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Reportes
                </a>
            </li>
            @endcan
            
            @can('configuracion.editar')
            <li class="nav-item">
                <a href="{{ route('plantillas-pdf.index') }}" class="nav-link {{ request()->routeIs('plantillas-pdf.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-check"></i> Actas
                </a>
            </li>
            @endcan

            @can('equipos.ver')
            <li class="nav-item">
                <a href="{{ route('actas-firmadas.index') }}" class="nav-link {{ request()->routeIs('actas-firmadas.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-pdf"></i> Actas Firmadas
                </a>
            </li>
            @endcan
            
            @can('equipos.importar')
            <li class="nav-item">
                <a href="{{ route('equipos.importar.form') }}" class="nav-link {{ request()->routeIs('equipos.importar*') ? 'active' : '' }}">
                    <i class="bi bi-upload"></i> Importar
                </a>
            </li>
            @endcan
            
            @can('roles.ver')
            <li class="nav-item mt-3 mb-1 px-3 text-uppercase text-muted text-xs font-weight-bold">Seguridad</li>
            <li class="nav-item">
                <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Usuarios Sistema
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock"></i> Roles y Permisos
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('auditoria.index') }}" class="nav-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i> Auditoría
                </a>
            </li>
            @endcan
            
            @can('configuracion.editar')
            <li class="nav-item">
                <a href="#" class="nav-link {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                    <i class="bi bi-database-down"></i> Backups
                </a>
            </li>
            @endcan
        </ul>
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="nav-link btn btn-link text-start w-100 btn-logout">
                <i class="bi bi-box-arrow-left me-2"></i> Cerrar Sesión
            </button>
        </form>
    </div>
</nav>

<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3 overflow-hidden">
            <button class="btn btn-light d-md-none" id="sidebarToggleBtn">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-title text-truncate">
                <h5 class="mb-0 text-truncate">Sistema Inventario</h5>
                <span class="d-none d-sm-block text-truncate" style="font-size: 0.65rem; font-weight: 600; letter-spacing: 0.2px; color: var(--primary-color);">FEDERACIÓN NACIONAL DE CAFETEROS - TOLIMA</span>
            </div>
        </div>
        
        <div class="user-profile">
            <div class="user-info">
                <span class="user-role">{{ auth()->user()->roles->first()->name ?? 'Usuario' }}</span>
                <span class="user-name">{{ auth()->user()->name ?? 'ADMIN' }}</span>
            </div>
            <div class="user-avatar">
                {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
            </div>
        </div>
    </div>

    <div class="content-area">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/app-core.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.initAlerts(
            @json(session('success')),
            @json(session('error')),
            @json(session('warning')),
            @json($errors->all())
        );

        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');

        function toggleSidebar() {
            if (sidebar) sidebar.classList.toggle('show');
            if (sidebarOverlay) sidebarOverlay.classList.toggle('show');
        }

        if (sidebarToggleBtn) {
            sidebarToggleBtn.addEventListener('click', toggleSidebar);
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', toggleSidebar);
        }

        // Auto-cerrar sidebar en móvil al seleccionar opción
        if (sidebar && window.matchMedia('(max-width: 768px)').matches) {
            const navLinks = sidebar.querySelectorAll('.nav-link:not([data-bs-toggle="collapse"])');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    sidebar.classList.remove('show');
                    if (sidebarOverlay) sidebarOverlay.classList.remove('show');
                });
            });
        }

        if (sidebar && window.matchMedia('(min-width: 769px)').matches) {
            sidebar.classList.remove('is-expanded');

            sidebar.addEventListener('mouseenter', () => {
                sidebar.classList.add('is-expanded');
            });

            sidebar.addEventListener('mouseleave', () => {
                sidebar.classList.remove('is-expanded');
            });
        }
    });
</script>
@stack('scripts')
</body>
</html>
