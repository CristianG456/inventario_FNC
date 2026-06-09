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
    
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #9e052b;
            --primary-hover: #7f0422;
            --primary-light: #fbebf0;
            --text-dark: #2d3748;
            --text-muted: #718096;
            --bg-light: #f7f9fc;
            --white: #ffffff;
            --border-color: #e2e8f0;
        }

        body { 
            background: var(--bg-light); 
            font-family: 'Poppins', sans-serif; 
            color: var(--text-dark);
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* SIDEBAR */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--white);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: transform .3s ease;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 16px;
        }
        
        .sidebar-brand img {
            width: 45px;
            height: auto;
        }

        .sidebar-brand .brand-text {
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand .brand-title {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--text-dark);
            line-height: 1.2;
        }

        .sidebar-brand .brand-subtitle {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .nav-menu {
            flex-grow: 1;
            padding: 0 16px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Scrollbar estético para el menú lateral */
        .nav-menu::-webkit-scrollbar {
            width: 6px;
        }
        .nav-menu::-webkit-scrollbar-track {
            background: transparent;
        }
        .nav-menu::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .nav-menu::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        #sidebar .nav-link {
            color: var(--text-dark);
            border-radius: 50px;
            margin-bottom: 8px;
            padding: 10px 16px;
            font-size: 0.95rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }

        #sidebar .nav-link i { 
            font-size: 1.2rem;
            color: var(--text-muted);
            transition: color 0.2s ease;
        }

        #sidebar .nav-link:hover {
            background: #f1f5f9;
        }

        #sidebar .nav-link.active {
            background: var(--primary-light);
            color: var(--primary-color);
        }

        #sidebar .nav-link.active i {
            color: var(--primary-color);
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border-color);
            margin-top: auto;
        }

        .btn-logout {
            color: var(--primary-color) !important;
            font-weight: 600;
        }

        .btn-logout:hover {
            background: var(--primary-light) !important;
        }

        /* MAIN CONTENT */
        .main-content { 
            margin-left: var(--sidebar-width); 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* TOPBAR */
        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border-color);
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        .topbar-title {
            display: flex;
            flex-direction: column;
        }

        .topbar-title h5 {
            font-weight: 700;
            margin: 0;
            color: var(--text-dark);
            font-size: 1.25rem;
        }

        .topbar-title span {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--primary-color);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--bg-light);
            padding: 6px 16px;
            border-radius: 50px;
            border: 1px solid var(--border-color);
        }

        .user-profile .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .user-profile .user-role {
            font-weight: 700;
            font-size: 0.8rem;
            color: var(--text-dark);
            line-height: 1;
        }

        .user-profile .user-name {
            font-size: 0.7rem;
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: var(--primary-light);
            color: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
        }

        /* CONTENT AREA */
        .content-area { 
            padding: 32px; 
            flex-grow: 1;
        }

        .page-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-title {
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .page-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin: 0;
        }

        /* CARDS & COMPONENTS */
        .card { 
            border: 1px solid var(--border-color);
            border-radius: 16px; 
            box-shadow: 0 4px 6px rgba(0,0,0,.02); 
            background: var(--white);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .search-bar {
            background: var(--white);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            background: transparent;
            font-size: 0.95rem;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: transparent;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.85rem;
            border-bottom: 2px solid var(--border-color);
            padding: 16px;
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            color: var(--text-dark);
            border-bottom: 1px solid var(--border-color);
            font-size: 0.9rem;
        }

        .table tbody tr:hover { 
            background: var(--bg-light); 
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .badge-warning { background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; }
        .badge-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-info { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }

        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .topbar { padding: 16px; }
            .content-area { padding: 16px; }
        }
    </style>
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
            <!-- 
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li> 
            -->
            
            <li class="nav-item">
                <a href="{{ route('equipos.index') }}" class="nav-link {{ request()->routeIs('equipos.*') ? 'active' : '' }}">
                    <i class="bi bi-display"></i> Activos
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('funcionarios.index') }}" class="nav-link {{ request()->routeIs('funcionarios.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Funcionarios
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('tickets.index') }}" class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}">
                    <i class="bi bi-headset"></i> HelpDesk
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('historial-tecnico.index') }}" class="nav-link {{ request()->routeIs('historial-tecnico.*') ? 'active' : '' }}">
                    <i class="bi bi-tools"></i> Mantenimientos
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('tipo-recursos.index') }}" class="nav-link {{ request()->routeIs('tipo-recursos.*') ? 'active' : '' }}">
                    <i class="bi bi-tags"></i> Categorías
                </a>
            </li>
            
            <li class="nav-item">
                <a href="#" class="nav-link {{ request()->routeIs('licencias.*') ? 'active' : '' }}">
                    <i class="bi bi-key"></i> Licencias
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Reportes
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('plantillas-pdf.index') }}" class="nav-link {{ request()->routeIs('plantillas-pdf.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-check"></i> Actas
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('equipos.importar.form') }}" class="nav-link {{ request()->routeIs('equipos.importar*') ? 'active' : '' }}">
                    <i class="bi bi-upload"></i> Importar
                </a>
            </li>
            
            <li class="nav-item">
                <a href="#" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Usuarios
                </a>
            </li>
            
            <li class="nav-item">
                <a href="#" class="nav-link {{ request()->routeIs('backups.*') ? 'active' : '' }}">
                    <i class="bi bi-database-down"></i> Backups
                </a>
            </li>
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
                <span class="user-role">Administrador</span>
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
</script>
@stack('scripts')
</body>
</html>
