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
            line-height: 1.5;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #a80024;
            margin-bottom: 30px;
        }
        .signature-table {
            width: 100%;
            margin-top: 80px;
            text-align: center;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 45%;
        }
        .signature-table .spacer {
            width: 10%;
        }
        .signature-line {
            border: 0;
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto 5px auto;
        }
    </style>
</head>
<body>

    <h2>ACTA DE ENTREGA DE EQUIPO</h2>

    <p style="text-align: justify; line-height: 1.6;">
        El día <strong>{{ date('d/m/Y') }}</strong>, el área de Soporte Técnico hace entrega formal del siguiente equipo tecnológico al funcionario <strong>{{ $equipo->usuarioAsignado->nombre }}</strong>, identificado con cédula <strong>{{ $equipo->usuarioAsignado->cedula }}</strong>.
    </p>

    <h4 style="margin-top: 20px; color: #333;">Información del Equipo:</h4>
    <ul style="line-height: 1.8; margin-bottom: 30px;">
        <li><strong>Tipo:</strong> {{ $equipo->tipoRecurso?->nombre ?? 'N/A' }}</li>
        <li><strong>Marca y Modelo:</strong> {{ $equipo->marca }} {{ $equipo->modelo }}</li>
        <li><strong>Serial:</strong> {{ $equipo->serial }}</li>
        <li><strong>Placa Interna:</strong> {{ $equipo->activo_fijo ?? 'N/A' }}</li>
    </ul>

    <p style="margin-top: 20px; font-size: 12px; color: #555; text-align: justify;">
        Con la firma de este documento, el funcionario acepta la responsabilidad por el cuidado y buen uso de este equipo.
    </p>

    <!-- Firmas -->
    <table class="signature-table">
        <tr>
            <td>
                <div class="signature-line"></div>
                <strong>Firma quien Entrega</strong><br>
                {{ auth()->user()->name ?? 'Administrador' }}<br>
                C.C. _________________
            </td>
            <td class="spacer"></td>
            <td>
                <div class="signature-line"></div>
                <strong>Firma quien Recibe</strong><br>
                {{ $equipo->usuarioAsignado->nombre }}<br>
                C.C. {{ $equipo->usuarioAsignado->cedula }}
            </td>
        </tr>
    </table>

</body>
</html>
