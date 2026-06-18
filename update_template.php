<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

config(['database.connections.mysql.host' => '127.0.0.1']);
DB::reconnect('mysql');

use App\Models\PlantillaPdf;
use Illuminate\Support\Facades\DB;

// Desactivar plantillas anteriores
PlantillaPdf::where('activa', true)->update(['activa' => false]);

// Preparar logo
$logoPath = public_path('branding/logo-fnc.svg');
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoBase64 = 'data:image/svg+xml;base64,' . base64_encode(file_get_contents($logoPath));
}

$html = <<<HTML
<style>
    @page { margin: 20px 30px; font-family: Arial, sans-serif; font-size: 10px; }
    body { font-family: Arial, sans-serif; font-size: 10px; color: #000; white-space: normal !important; padding: 0 !important; }
    table { width: 100%; border-collapse: collapse; }
    td, th { border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle; }
    .no-border, .no-border td { border: none !important; }
    .header-table { margin-bottom: 20px; }
    .header-table td { font-weight: bold; }
    .logo-cell { width: 15%; padding: 5px; }
    .logo { max-width: 80px; max-height: 50px; }
    .title-cell { width: 65%; font-size: 12px; }
    .meta-cell { width: 20%; font-size: 9px; text-align: left; padding-left: 5px; }
    .info-section { margin-bottom: 15px; }
    .info-section td { text-align: center; }
    .info-label { font-weight: bold; font-size: 10px; display: inline-block; margin-right: 5px; }
    .info-box { border: 1px solid #000; padding: 4px 15px; display: inline-block; min-width: 100px; text-align: center; font-weight: normal; }
    .main-table { margin-bottom: 30px; }
    .main-table th { background-color: #d9d9d9; font-weight: bold; font-size: 9px; }
    .main-table td { font-size: 9px; height: 18px; text-transform: uppercase; }
    .text-na { color: #888; text-transform: none !important; }
    .fw-bold { font-weight: bold; }
    .signatures { margin-top: 40px; width: 100%; }
    .sig-box { border: 1px solid #000; text-align: center; padding: 5px; margin-bottom: 2px; font-weight: bold; font-size: 9px; min-height: 12px; text-transform: uppercase; }
    .sig-label { text-align: center; font-weight: bold; font-size: 9px; margin-bottom: 10px; }
    .sig-col { width: 45%; }
    .sig-spacer { width: 10%; }
</style>

<table class="header-table">
    <tr>
        <td rowspan="2" class="logo-cell">
            <img src="{$logoBase64}" class="logo" alt="Logo FNC">
        </td>
        <td class="title-cell">FEDERACIÓN NACIONAL DE CAFETEROS DE COLOMBIA</td>
        <td class="meta-cell">
            Código: FE-BS-F-0069<br><br>
            Fecha: 11/04/2017
        </td>
    </tr>
    <tr>
        <td class="title-cell">NOVEDAD DE ACTIVO</td>
        <td class="meta-cell">Versión: 1</td>
    </tr>
</table>

<table class="no-border info-section">
    <tr>
        <td style="width: 33%;">
            <span class="info-label">Tipo de Novedad</span>
            <div class="info-box" style="color: #666;">Inventario Físico</div>
        </td>
        <td style="width: 33%;">
            <span class="info-label">Ubicación:</span>
            <div class="info-box">{{ciudad}}</div>
        </td>
        <td style="width: 33%;">
            <span class="info-label">Fecha:</span>
            <div class="info-box">{{fecha_generacion}}</div>
            <div style="font-size: 8px; margin-top: 2px; font-weight: bold;">aaaa / mm / dd</div>
        </td>
    </tr>
</table>

<table class="main-table">
    <thead>
        <tr>
            <th style="width: 10%;">Activo fijo</th>
            <th style="width: 6%;">SN°</th>
            <th style="width: 22%;">Denominación del activo fijo</th>
            <th style="width: 10%;">Marca</th>
            <th style="width: 12%;">Modelo</th>
            <th style="width: 12%;">Serie</th>
            <th style="width: 10%;">Placa de inventario</th>
            <th style="width: 18%;">Observaciones</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-na">N/A</td>
            <td class="text-na">N/A</td>
            <td class="fw-bold">{{tipo_recurso}}</td>
            <td class="fw-bold">{{marca}}</td>
            <td class="fw-bold">{{modelo}}</td>
            <td class="fw-bold">{{serial}}</td>
            <td class="fw-bold">{{activo_fijo}}</td>
            <td class="fw-bold">ENTREGA DE EQUIPO</td>
        </tr>
        <tr>
            <td class="text-na">N/A</td>
            <td class="text-na">N/A</td>
            <td class="fw-bold">ADAPTADOR CORRIENTE PORTATIL</td>
            <td class="fw-bold">{{marca}}</td>
            <td class="fw-bold">ILEGIBLE</td>
            <td class="fw-bold">ILEGIBLE</td>
            <td class="text-na">N/A</td>
            <td class="fw-bold">ENTREGA DE EQUIPO</td>
        </tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
        <tr><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td><td class="text-na">N/A</td></tr>
    </tbody>
</table>

<table class="no-border signatures">
    <tr>
        <td class="sig-col" style="vertical-align: top;">
            <div class="sig-box">TI</div>
            <div class="sig-label">DEPENDENCIA</div>

            <div class="sig-box" style="margin-top: 15px;">{{usuario_sistema}}</div>
            <div class="sig-label">NOMBRE Y FIRMA DE QUIEN ENTREGA</div>

            <table class="no-border" style="width: 100%; margin-top: 15px;">
                <tr>
                    <td style="width: 30%; padding: 0 5px 0 0;">
                        <div class="sig-box"></div>
                        <div class="sig-label" style="margin-bottom: 0;">Cod. Personal</div>
                    </td>
                    <td style="width: 70%; padding: 0 0 0 5px;">
                        <div class="sig-box">ANALISTA TIC</div>
                        <div class="sig-label" style="margin-bottom: 0;">Cargo</div>
                    </td>
                </tr>
            </table>
        </td>
        
        <td class="sig-spacer"></td>

        <td class="sig-col" style="vertical-align: top;">
            <div class="sig-box">{{seccional}}</div>
            <div class="sig-label">DEPENDENCIA</div>

            <div class="sig-box" style="margin-top: 15px;">{{nombre_usuario}}</div>
            <div class="sig-label">NOMBRE Y FIRMA DE QUIEN RECIBE</div>

            <table class="no-border" style="width: 100%; margin-top: 15px;">
                <tr>
                    <td style="width: 30%; padding: 0 5px 0 0;">
                        <div class="sig-box"></div>
                        <div class="sig-label" style="margin-bottom: 0;">Cod. Personal</div>
                    </td>
                    <td style="width: 70%; padding: 0 0 0 5px;">
                        <div class="sig-box">{{cargo}}</div>
                        <div class="sig-label" style="margin-bottom: 0;">Cargo</div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
HTML;

PlantillaPdf::create([
    'nombre' => 'Acta Oficial FNC',
    'tipo' => 'acta_entrega',
    'contenido' => $html,
    'activa' => true,
    'user_id' => \App\Models\User::first()->id ?? 1
]);

echo "Plantilla insertada correctamente.\n";
