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
    @stack('styles')
</head>
<body>

<nav id="sidebar">
    <div class="sidebar-brand">
        <img src="{{ asset('imagenes/federacion cafeteros logo.png') }}" alt="Logo FNC">
        <div class="brand-text">
            <span class="brand-title">Inventario TIC</span>
            <span class="brand-subtitle">Tolima</span>
        </div>
    </div>
    
    <div class="nav-menu">
        <ul class="nav flex-column">
            
            @can('dashboard.ver')
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
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
            
            @can('configuracion.editar')
            <li class="nav-item">
                <a href="{{ route('tipo-recursos.index') }}" class="nav-link {{ request()->routeIs('tipo-recursos.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Categorías
                </a>
            </li>
            @endcan
            
            @can('licencias.ver')
            <li class="nav-item">
                <a href="{{ route('licencias.index') }}" class="nav-link {{ request()->routeIs('licencias.*') || request()->routeIs('licencia-asignaciones.*') ? 'active' : '' }}">
                    <i class="bi bi-key"></i> Licencias
                </a>
            </li>
            @endcan

            <li class="nav-item">
                <a href="{{ route('suscripciones.index') }}" class="nav-link {{ request()->routeIs('suscripciones.*') || request()->routeIs('suscripcion-asignaciones.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i> Suscripciones
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('vitalicias.index') }}" class="nav-link {{ request()->routeIs('vitalicias.*') || request()->routeIs('vitalicia-asignaciones.*') ? 'active' : '' }}">
                    <i class="bi bi-award"></i> Vitalicias
                </a>
            </li>
            
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
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list"></i>
            </button>
            <div class="topbar-title">
                <h5>Sistema Inventario</h5>
                <span>FEDERACIÓN NACIONAL DE CAFETEROS - TOLIMA</span>
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
    });

    // Auto-capitalizar la primera letra en todos los inputs de texto y textareas
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[type="text"], input[type="search"], textarea')) {
            let val = e.target.value;
            if (val.length > 0 && val[0].match(/[a-z]/)) {
                // Guardar la posición del cursor para no interrumpir la escritura
                let start = e.target.selectionStart;
                let end = e.target.selectionEnd;
                
                e.target.value = val.charAt(0).toUpperCase() + val.slice(1);
                
                // Restaurar la posición del cursor
                e.target.setSelectionRange(start, end);
            }
        }
    });
</script>
@stack('scripts')
</body>
</html>
