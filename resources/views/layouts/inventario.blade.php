<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Inventario') &mdash; Inventario de Equipos</title>
    <link rel="icon" type="image/png" href="{{ asset('imagenes/federacion cafeteros logo.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('imagenes/federacion cafeteros logo.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #9e052b;
            --sidebar-hover: #7f0422;
            --bs-primary: #9e052b;
            --bs-primary-rgb: 158, 5, 43;
            --bs-link-color: #9e052b;
            --bs-link-hover-color: #7f0422;
        }
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        #sidebar {
            width: var(--sidebar-width); min-height: 100vh;
            background: var(--sidebar-bg); position: fixed;
            top: 0; left: 0; z-index: 100; transition: transform .3s ease;
        }
        #sidebar .nav-link {
            color: rgba(255,255,255,.75); border-radius: 6px;
            margin: 2px 10px; padding: 9px 14px;
            transition: background .2s, color .2s;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            background: var(--sidebar-hover); color: #fff;
        }
        #sidebar .nav-link i { width: 22px; }
        .sidebar-brand {
            padding: 20px 20px 10px; color: #fff; font-weight: 700;
            font-size: 1.1rem;
            border-bottom: 1px solid rgba(255,255,255,.15);
            margin-bottom: 10px;
        }
        .main-content { margin-left: var(--sidebar-width); padding: 0; }
        .topbar {
            background: #fff; border-bottom: 1px solid #dee2e6;
            padding: 12px 24px; position: sticky; top: 0; z-index: 99;
        }
        .content-area { padding: 24px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,.07); }
        .table thead th {
            background: var(--sidebar-bg); color: #fff;
            font-weight: 600; border: none; font-size: .85rem;
            text-transform: uppercase; letter-spacing: .03em;
        }
        .table tbody tr:hover { background: #f8f9ff; }
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

<nav id="sidebar">
    <div class="sidebar-brand d-flex align-items-center gap-2">
        <img src="{{ asset('imagenes/federacion cafeteros logo.png') }}" alt="Federación Nacional de Cafeteros" width="40" height="40" style="object-fit:contain; border-radius:4px; background:#fff; padding:2px;">
        <span>Inventario TI</span>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('equipos.index') }}"
               class="nav-link {{ request()->routeIs('equipos.*') ? 'active' : '' }}">
                <i class="bi bi-laptop"></i> Equipos
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('tipo-recursos.index') }}"
               class="nav-link {{ request()->routeIs('tipo-recursos.*') ? 'active' : '' }}">
                <i class="bi bi-tags"></i> Tipos de Recurso
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('checklists.index') }}"
               class="nav-link {{ request()->routeIs('checklists.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i> Checklists
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('equipos.importar.form') }}"
               class="nav-link {{ request()->routeIs('equipos.importar*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-arrow-up"></i> Importar Excel
            </a>
        </li>
    </ul>
    <div style="position:absolute; bottom:20px; left:0; right:0; padding: 0 10px;">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="nav-link btn btn-link text-start w-100"
                    style="color:rgba(255,255,255,.75);">
                <i class="bi bi-box-arrow-left"></i> Cerrar sesion
            </button>
        </form>
    </div>
</nav>

<div class="main-content">
    <div class="topbar d-flex align-items-center justify-content-between">
        <button class="btn btn-light d-md-none"
                onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>
        <h6 class="mb-0 fw-semibold text-muted">@yield('title', 'Inventario')</h6>
        <span class="text-muted small">
            <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
        </span>
    </div>

    <div class="content-area">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function () {
        function esCampoValido(el) {
            if (!el || el.disabled || el.readOnly) return false;
            if (el.dataset.noCapitalizeFirst === 'true') return false;

            if (el.tagName === 'TEXTAREA') return true;
            if (el.tagName !== 'INPUT') return false;

            const tipo = (el.getAttribute('type') || 'text').toLowerCase();
            const excluidos = ['email', 'password', 'number', 'date', 'datetime-local', 'time', 'month', 'week', 'url', 'search', 'tel', 'hidden', 'file', 'checkbox', 'radio'];
            return tipo === 'text' && !excluidos.includes(tipo);
        }

        function capitalizarPrimeraLetra(el) {
            if (!esCampoValido(el) || !el.value) return;

            const indice = el.value.search(/[A-Za-zÁÉÍÓÚÑáéíóúñ]/u);
            if (indice < 0) return;

            const letra = el.value.charAt(indice);
            const mayuscula = letra.toLocaleUpperCase('es-CO');
            if (letra === mayuscula) return;

            el.value = el.value.slice(0, indice) + mayuscula + el.value.slice(indice + 1);
        }

        document.addEventListener('input', function (event) {
            capitalizarPrimeraLetra(event.target);
        });

        document.addEventListener('change', function (event) {
            capitalizarPrimeraLetra(event.target);
        });

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const flashSuccess = @json(session('success'));
        const flashError = @json(session('error'));
        const flashWarning = @json(session('warning'));
        const validationErrors = @json($errors->all());

        // Resultado de importación masiva
        @if (session()->has('import_insertados'))
        @php
            $impInsertados = session('import_insertados', 0);
            $impOmitidos   = session('import_omitidos', 0);
            $impFail       = count(session('import_failures', [])) + count(session('import_errors', []));
        @endphp
        Swal.fire({
            icon: {{ $impInsertados > 0 ? "'success'" : "'warning'" }},
            title: 'Importación completada',
            html: `
                <div style="text-align:left; line-height:1.8;">
                    <span style="font-size:1.1rem;">✅ <strong>{{ $impInsertados }}</strong> equipo(s) registrado(s)</span><br>
                    @if($impOmitidos > 0)
                    <span>⚠️ <strong>{{ $impOmitidos }}</strong> periférico(s) omitido(s)</span><br>
                    @endif
                    @if($impFail > 0)
                    <span>❌ <strong>{{ $impFail }}</strong> fila(s) con error</span><br>
                    @endif
                </div>`,
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#9e052b',
        });
        @endif

        if (flashSuccess) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: flashSuccess,
                confirmButtonText: 'OK',
                confirmButtonColor: '#9e052b',
                showClass: { popup: 'swal2-show' },
                hideClass: { popup: 'swal2-hide' },
                toast: true,
                position: 'top-end',
                timer: 2800,
                timerProgressBar: true,
                showConfirmButton: false,
            });
        }

        if (flashWarning) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: flashWarning,
                confirmButtonText: 'OK',
                confirmButtonColor: '#9e052b',
            });
        }

        if (flashError) {
            Swal.fire({
                icon: 'error',
                title: 'Ocurrió un error',
                text: flashError,
                confirmButtonText: 'OK',
                confirmButtonColor: '#9e052b',
            });
        }

        if (Array.isArray(validationErrors) && validationErrors.length > 0) {
            const htmlErrores = '<ul style="text-align:left; margin:0; padding-left:1rem;">'
                + validationErrors.map(function (msg) { return '<li>' + msg + '</li>'; }).join('')
                + '</ul>';

            Swal.fire({
                icon: 'error',
                title: 'Revise los campos del formulario',
                html: htmlErrores,
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#9e052b',
            });
        }

        document.addEventListener('click', function (event) {
            const trigger = event.target.closest('[data-delete-url]');
            if (!trigger) return;

            event.preventDefault();

            const count = parseInt(trigger.getAttribute('data-count') || '0', 10);
            if (!Number.isNaN(count) && count > 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No se puede eliminar',
                    text: 'Este registro tiene elementos asociados y no puede eliminarse.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#9e052b',
                });
                return;
            }

            const url = trigger.getAttribute('data-delete-url');
            const nombre = trigger.getAttribute('data-delete-name') || 'este registro';

            Swal.fire({
                icon: 'warning',
                title: '¿Estás seguro de eliminar este registro?',
                text: 'Se eliminará ' + nombre + '. Esta acción no se puede deshacer.',
                showCancelButton: true,
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
            }).then(function (result) {
                if (!result.isConfirmed || !url) return;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = csrfToken;

                const method = document.createElement('input');
                method.type = 'hidden';
                method.name = '_method';
                method.value = 'DELETE';

                form.appendChild(csrf);
                form.appendChild(method);
                document.body.appendChild(form);
                form.submit();
            });
        });
    })();
</script>
@stack('scripts')
</body>
</html>
