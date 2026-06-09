<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta de Entrega - {{ $equipo->serial }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
            padding: 40px;
            white-space: pre-wrap; /* Respeta los saltos de línea del texto plano */
        }
    </style>
</head>
<body>
{!! $contenidoHtml !!}
</body>
</html>
