<?php

namespace App\Imports;

use App\Models\Equipo;
use App\Models\TipoRecurso;
use App\Models\UsuarioAsignado;
use App\Services\Importadores\CMDBMapperService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * EquiposImport — Importador de equipos desde Excel.
 *
 * Delega toda la lógica de mapeo de columnas al CMDBMapperService.
 * Soporta automáticamente el formato propio del sistema y el CMDB corporativo.
 *
 * Cada fila del Excel se convierte en:
 *   1 Equipo  +  1 UsuarioAsignado
 *
 * Transacciones DB por fila. Errores individuales NO detienen la importación.
 */
class EquiposImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsOnError
{
    use SkipsErrors;

    // ── Tipos que se ignoran silenciosamente ──────────────────────────────────
    private const TIPOS_PERIFERICO = ['telefono', 'teclado', 'mouse', 'mause', 'camara'];

    // ── Estado interno ───────────────────────────────────────────────────────

    private CMDBMapperService $mapper;
    private int   $insertados  = 0;
    private int   $omitidos    = 0;
    private int   $currentRow  = 1;
    private array $rowFailures = [];
    private array $rawHeaders  = [];
    private int   $detectedHeadingRow = 1;

    // ── Constructor ──────────────────────────────────────────────────────────

    public function __construct(string $filePath)
    {
        $this->mapper = new CMDBMapperService();
        $this->detectHeadingRow($filePath);
    }
    
    public function __destruct()
    {
        Log::info("AUDITORIA IMPORTACIÓN - 4. Cantidad total de filas procesadas: " . ($this->currentRow - 1));
    }

    /**
     * Detección dinámica de la fila de encabezados.
     * Lee las primeras 5 filas del Excel y busca las palabras clave.
     */
    private function detectHeadingRow(string $filePath): void
    {
        try {
            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            
            for ($row = 1; $row <= 5; $row++) {
                $cellIterator = $sheet->getRowIterator($row, $row)->current()->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);
                $score = 0;
                $tempRaw = [];
                
                foreach ($cellIterator as $cell) {
                    $valRaw = $cell->getValue();
                    $val = strtolower(trim((string) $valRaw));
                    if ($valRaw !== null && $valRaw !== '') {
                        $tempRaw[] = $valRaw;
                    }
                    if ($val) {
                        // Puntaje si contiene palabras clave del CMDB o del sistema
                        if (in_array($val, ['serial', 'marca', 'modelo', 'tipo de recurso', 'estado operativo', 'nombres y apellidos', 'procesador', 'memoria ram'])) {
                            $score++;
                        }
                    }
                }
                
                // Si encontramos al menos 3 encabezados clave, esta es nuestra fila
                if ($score >= 3) {
                    $this->detectedHeadingRow = $row;
                    $this->currentRow = $row;
                    $this->rawHeaders = $tempRaw;
                    Log::info("IMPORT: Fila de encabezados detectada dinámicamente en la fila {$row}");
                    return;
                }
            }
        } catch (\Exception $e) {
            Log::warning("IMPORT: Error al detectar fila de encabezados: " . $e->getMessage());
        }
    }

    /**
     * Devuelve la fila dinámica detectada.
     */
    public function headingRow(): int
    {
        return $this->detectedHeadingRow;
    }

    /**
     * Tamaño de chunk para lectura eficiente en memoria.
     */
    public function chunkSize(): int
    {
        return 100;
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PROCESAMIENTO PRINCIPAL
    // ══════════════════════════════════════════════════════════════════════════

    public function model(array $row): ?Equipo
    {
        $this->currentRow++;

        // Inicializar el mapper con la primera fila de datos (una sola vez)
        if (!$this->mapper->isInitialized()) {
            Log::info("=========================================================================");
            Log::info("AUDITORIA IMPORTACIÓN - INICIO DE DIAGNÓSTICO");
            Log::info("=========================================================================");
            Log::info("1. Fila utilizada como encabezado: " . $this->headingRow());
            Log::info("2. Lista completa de columnas detectadas (normalizadas): " . json_encode(array_keys($row), JSON_UNESCAPED_UNICODE));
            Log::info("3. Nombre exacto de cada encabezado (sin normalizar): " . json_encode($this->rawHeaders, JSON_UNESCAPED_UNICODE));
            Log::info("5. Primera fila de datos leída: " . json_encode($row, JSON_UNESCAPED_UNICODE));
            
            $this->mapper->initialize($row);
            
            Log::info("6. Valores extraídos de la primera fila:");
            Log::info("   - nombre: " . $this->mapper->getOrDefault($row, 'nombre_usuario'));
            Log::info("   - cedula: " . $this->mapper->getOrDefault($row, 'cedula'));
            Log::info("   - serial: " . $this->mapper->get($row, 'serial'));
            Log::info("   - marca: " . $this->mapper->getOrDefault($row, 'marca'));
            Log::info("   - modelo: " . $this->mapper->getOrDefault($row, 'modelo'));
            Log::info("   - nombre_equipo: " . $this->mapper->getOrDefault($row, 'nombre_equipo'));
            Log::info("=========================================================================");
        }

        // Ignorar filas completamente vacías
        if ($this->filaVacia($row)) {
            return null;
        }

        $tipoNombre = $this->mapper->get($row, 'tipo_recurso');
        $serial     = $this->mapper->get($row, 'serial');

        // Periféricos → omitir sin error
        if ($tipoNombre !== null && $this->esPeriferico($tipoNombre)) {
            $this->omitidos++;
            return null;
        }

        try {
            $equipo = DB::transaction(function () use ($row, $tipoNombre, $serial) {

                // 1. Resolver tipo_recurso
                $tipoId = $this->resolverTipoRecurso($tipoNombre);

                // 2. Resolver serial (autogenerar si no existe)
                $serialFinal = $this->resolverSerial($serial);

                // 3. Crear o actualizar equipo
                $equipo = Equipo::updateOrCreate(
                    ['serial' => $serialFinal],
                    [
                        'tipo_recurso_id'   => $tipoId,
                        'placa'             => $this->mapper->get($row, 'placa'),
                        'marca'             => $this->mapper->getOrDefault($row, 'marca'),
                        'modelo'            => $this->mapper->getOrDefault($row, 'modelo'),
                        'nombre_equipo'     => $this->mapper->getOrDefault($row, 'nombre_equipo'),
                        'estado_operativo'  => $this->mapearEstadoOperativo($this->mapper->get($row, 'estado_operativo')),
                        'razon_estado'      => $this->mapper->get($row, 'razon_estado'),
                        'procesador'        => $this->mapper->get($row, 'procesador'),
                        'ram'               => $this->mapper->get($row, 'ram'),
                        'disco'             => $this->mapper->get($row, 'disco'),
                        'sistema_operativo' => $this->mapper->get($row, 'sistema_operativo'),
                        'fecha_compra'      => $this->mapper->getDate($row, 'fecha_compra'),
                        'fin_garantia'      => $this->mapper->getDate($row, 'fin_garantia'),
                        'tiempo_uso'        => $this->mapper->get($row, 'tiempo_uso'),
                    ]
                );

                // 4. Crear o actualizar usuario asignado (misma fila = mismo equipo)
                $cedula = $this->mapper->getOrDefault($row, 'cedula');
                $nombre = $this->mapper->getOrDefault($row, 'nombre_usuario');
                
                UsuarioAsignado::updateOrCreate(
                    ['equipo_id' => $equipo->id],
                    [
                        'nombre'              => $nombre,
                        'cedula'              => $cedula,
                        'empresa_propietaria' => $this->mapper->get($row, 'empresa_propietaria'),
                        'dependencia'         => $this->mapper->get($row, 'dependencia'),
                        'fuente_recurso'      => $this->mapper->get($row, 'fuente_recurso'),
                        'empresa_funcionario' => $this->mapper->get($row, 'empresa_funcionario'),
                        'tipo_vinculacion'    => $this->mapper->get($row, 'tipo_vinculacion'),
                        'shortname'           => $this->mapper->get($row, 'shortname'),
                        'departamento'        => $this->mapper->get($row, 'departamento'),
                        'ciudad'              => $this->mapper->get($row, 'ciudad'),
                        'cargo'               => $this->mapper->get($row, 'cargo'),
                        'area'                => $this->mapper->get($row, 'area'),
                        'piso'                => $this->mapper->get($row, 'piso'),
                    ]
                );

                // 5. Sincronizar automáticamente con el módulo de Funcionarios
                if ($cedula && $cedula !== 'Sin Asignar' && $nombre && $nombre !== 'Sin Asignar') {
                    \App\Models\Funcionario::updateOrCreate(
                        ['identificacion' => $cedula],
                        [
                            'nombres' => $nombre,
                            'apellidos' => '',
                            'cargo' => $this->mapper->get($row, 'cargo'),
                            'area' => $this->mapper->get($row, 'area'),
                            'departamento' => $this->mapper->get($row, 'departamento'),
                            'ciudad' => $this->mapper->get($row, 'ciudad'),
                            'empresa_funcionario' => $this->mapper->get($row, 'empresa_funcionario'),
                            'tipo_vinculacion' => $this->mapper->get($row, 'tipo_vinculacion'),
                            'estado' => 'Activo'
                        ]
                    );
                }

                return $equipo;
            });

            $this->insertados++;
            return $equipo;

        } catch (\Exception $e) {
            $this->rowFailures[] = [
                'fila'    => $this->currentRow,
                'errores' => ['Error interno: ' . $e->getMessage()],
            ];
            Log::error("Import excepción fila {$this->currentRow}", ['msg' => $e->getMessage()]);
            return null;
        }
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  GETTERS PARA EL CONTROLADOR
    // ══════════════════════════════════════════════════════════════════════════

    public function getInsertados(): int           { return $this->insertados; }
    public function getOmitidos(): int             { return $this->omitidos; }
    public function getRowFailures(): array        { return $this->rowFailures; }
    public function getMapper(): CMDBMapperService { return $this->mapper; }

    // ══════════════════════════════════════════════════════════════════════════
    //  HELPERS INTERNOS
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Buscar o crear el tipo de recurso por nombre.
     * Si no se proporcionó, usa un default genérico.
     */
    private function resolverTipoRecurso(?string $nombre): int
    {
        if ($nombre) {
            return TipoRecurso::firstOrCreate(['nombre' => $nombre])->id;
        }

        // Buscar o crear un tipo por defecto
        return TipoRecurso::firstOrCreate(['nombre' => 'SIN CLASIFICAR'])->id;
    }

    /**
     * Resuelve el serial: si es vacío, N/A, PENDIENTE, etc. → autogenerar.
     */
    private function resolverSerial(?string $serial): string
    {
        $s = trim((string) $serial);
        $sUpper = strtoupper($s);

        $invalidos = ['', 'NO TIENE', 'PENDIENTE', 'SIN ASIGNAR', 'N/A', 'NA', 'SIN SERIAL', 'SIN REGISTRO'];

        if (in_array($sUpper, $invalidos, true)) {
            return 'SIN_SERIAL_' . strtoupper(uniqid());
        }

        return $s;
    }

    /**
     * Detecta si el tipo corresponde a un periférico que se debe omitir.
     */
    private function esPeriferico(string $tipo): bool
    {
        $n = strtolower($tipo);
        foreach (self::TIPOS_PERIFERICO as $p) {
            if (str_contains($n, $p)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica si una fila está completamente vacía.
     */
    private function filaVacia(array $row): bool
    {
        foreach ($row as $v) {
            if (trim((string) $v) !== '') {
                return false;
            }
        }
        return true;
    }

    /**
     * Mapea los estados operativos del Excel a los valores ENUM de la BD.
     */
    private function mapearEstadoOperativo(?string $estado): string
    {
        if (!$estado) {
            return 'activo';
        }

        $e = strtolower(trim($estado));

        if (str_contains($e, 'operaci') || str_contains($e, 'activo') || str_contains($e, 'asignado')) {
            return 'activo';
        }
        if (str_contains($e, 'baja') || str_contains($e, 'desechado') || str_contains($e, 'obsoleto')) {
            return 'baja';
        }
        if (str_contains($e, 'almacenado') || str_contains($e, 'pendiente') || str_contains($e, 'mantenimiento') || str_contains($e, 'alistamiento')) {
            return 'mantenimiento';
        }

        return 'activo';
    }
}
