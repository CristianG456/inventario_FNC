<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('imagenes/federacion cafeteros logo.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('imagenes/federacion cafeteros logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            :root { --marca: #9e052b; }
            .brand-bg { background-color: var(--marca); }
            .brand-text { color: var(--marca); }
            
            .circles-bg {
                position: relative;
                overflow: hidden;
            }
            .circles-bg::before, .circles-bg::after {
                content: '';
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.04);
            }
            .circles-bg::before {
                width: 800px;
                height: 800px;
                top: -200px;
                left: -300px;
            }
            .circles-bg::after {
                width: 600px;
                height: 600px;
                bottom: -150px;
                right: -200px;
            }
            .circles-bg-3 {
                position: absolute;
                width: 500px;
                height: 500px;
                top: 30%;
                left: 10%;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.03);
                z-index: 0;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-white">
        <div class="min-h-screen flex flex-col md:flex-row">
            
            <!-- Left Side Panel (Brand) -->
            <div class="hidden md:flex md:w-[45%] lg:w-[50%] brand-bg circles-bg text-white flex-col justify-center px-12 lg:px-20 py-10 relative">
                <div class="circles-bg-3"></div>
                <div class="z-10 relative mt-32 lg:mt-48">
                    <span class="inline-block bg-white bg-opacity-20 rounded-full px-5 py-1.5 text-sm font-semibold tracking-wide mb-8 shadow-sm">
                        Sistema Institucional
                    </span>
                    <h1 class="text-5xl lg:text-6xl font-bold font-sans tracking-tight leading-tight mb-8">
                        Sistema de<br>Inventario
                    </h1>
                    <p class="text-white text-opacity-90 text-lg max-w-md font-medium leading-relaxed">
                        Comité Departamental de Cafeteros del Tolima —<br>
                        Plataforma de registro y control de equipos tecnológicos.
                    </p>
                </div>
            </div>

            <!-- Right Side Panel (Content/Form) -->
            <div class="w-full md:w-[55%] lg:w-[50%] flex flex-col justify-center items-center px-6 py-12 lg:px-16 relative bg-white">
                <div class="w-full max-w-md flex flex-col items-center">
                    {{ $slot }}
                </div>
            </div>
            
        </div>
        
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

                const flashSuccess = @json(session('success'));
                const flashError = @json(session('error'));
                const flashWarning = @json(session('warning'));
                const validationErrors = @json($errors->all());

                if (flashSuccess) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: flashSuccess,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#9e052b',
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
            })();
        </script>
    </body>
</html>
