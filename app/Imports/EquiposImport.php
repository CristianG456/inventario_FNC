<?php

namespace App\Imports;

use App\Models\Equipo;
use App\Models\TipoRecurso;
use App\Models\UsuarioAsignado;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class EquiposImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsOnError
{
    use SkipsErrors;

    // ── Tipos que se ignoran silenciosamente ──────────────────────────────────
    private const TIPOS_PERIFERICO = ['telefono', 'teclado', 'mouse', 'camara'];

    // ── Mapa de alias: campo_interno => [posibles encabezados en el Excel] ────
    // WithHeadingRow normaliza los encabezados a snake_case minúsculo.
    // Aquí también se incluyen las versiones ya normalizadas.
    private const COLUMN_MAP = [
        // Equipo
        'tipo_recurso'        => ['tipo_recurso', 'tipo_de_recurso', 'tipo', 'device_type', 'tipo_equipo', 'category', 'categoria'],
        'serial'              => ['serial', 'serial_number', 'nro_serial', 'numero_serial', 'nro._serial', 'asset_serial'],
        'placa'               => ['placa', 'asset_tag', 'activo_fijo', 'placa_inventario', 'tag'],
        'marca'               => ['marca', 'brand', 'fabricante', 'manufacturer'],
        'modelo'              => ['modelo', 'model', 'model_number'],
        'nombre_equipo'       => ['nombre_equipo', 'hostname', 'nombre', 'equipo', 'computer_name', 'device_name', 'nombre_host'],
        'estado_operativo'    => ['estado_operativo', 'estado', 'status', 'state', 'operational_status'],
        'razon_estado'        => ['razon_estado', 'razon', 'reason', 'observacion_estado', 'motivo'],
        'procesador'          => ['procesador', 'processor', 'cpu', 'procesador_cpu'],
        'ram'                 => ['ram', 'memoria_ram', 'memory', 'memoria', 'ram_gb'],
        'disco'               => ['disco', 'disk', 'storage', 'almacenamiento', 'hdd', 'ssd', 'disco_duro'],
        'sistema_operativo'   => ['sistema_operativo', 'so', 'os', 'operating_system', 'sistema', 'version_so'],
        'fecha_compra'        => ['fecha_compra', 'purchase_date', 'fecha_adquisicion', 'fecha_de_compra'],
        'fin_garantia'        => ['fin_garantia', 'warranty_end', 'garantia', 'fecha_garantia', 'warranty_expiry'],
        'tiempo_uso'          => ['tiempo_uso', 'age', 'antiguedad', 'anos_uso'],
        // Usuario asignado
        'nombre_usuario'      => ['nombre_usuario', 'usuario', 'empleado', 'funcionario', 'full_name', 'nombre_completo', 'nombre_funcionario'],
        'cedula'              => ['cedula', 'documento', 'identificacion', 'id_number', 'cedula_ciudadania', 'cc'],
        'empresa_propietaria' => ['empresa_propietaria', 'propietario', 'owner_company', 'empresa_duena'],
        'dependencia'         => ['dependencia', 'dependency', 'area_funcional', 'gerencia'],
        'fuente_recurso'      => ['fuente_recurso', 'fuente', 'funding_source', 'fuente_de_recurso'],
        'empresa_funcionario' => ['empresa_funcionario', 'empresa_empleado', 'employer', 'empresa_contratante'],
        'tipo_vinculacion'    => ['tipo_vinculacion', 'vinculacion', 'empleado_o_contratista', 'employment_type', 'tipo_contrato'],
        'shortname'           => ['shortname', 'short_name', 'usuario_red', 'login', 'network_user', 'username'],
        'departamento'        => ['departamento', 'department', 'depto', 'dpto'],
        'ciudad'              => ['ciudad', 'city', 'municipio', 'location'],
        'cargo'               => ['cargo', 'position', 'job_title', 'titulo', 'puesto'],
        'area'                => ['area', 'area_trabajo', 'work_area'],
        'piso'                => ['piso', 'floor', 'ubicacion', 'nivel'],
    ];

    private int   $insertados  = 0;
    private int   $omitidos    = 0;
    private int   $currentRow  = 1;   // fila 1 = encabezados; datos desde fila 2
    private array $rowFailures = [];

    // ── Procesamiento principal ───────────────────────────────────────────────

    public function model(array $row): ?Equipo
    {
        $this->currentRow++;

        // Ignorar filas completamente vacías
        if ($this->filaVacia($row)) {
            return null;
        }

        $tipoNombre = $this->get($row, 'tipo_recurso');
        $serial     = $this->get($row, 'serial');

        // Periféricos → omitir sin error
        if ($tipoNombre !== null && $this->esPeriferico($tipoNombre)) {
            $this->omitidos++;
            return null;
        }

        // Validación manual por fila
        $errores = $this->validarFila($tipoNombre, $serial);
        if (!empty($errores)) {
            $this->rowFailures[] = ['fila' => $this->currentRow, 'errores' => $errores];
            Log::warning("Import fila {$this->currentRow} rechazada", $errores);
            return null;
        }

        try {
            $equipo = DB::transaction(function () use ($row, $tipoNombre, $serial) {

                // 1. Buscar o crear tipo_recurso
                $tipo = TipoRecurso::firstOrCreate(['nombre' => $tipoNombre]);

                // 2. Crear equipo
                $equipo = Equipo::create([
                    'tipo_recurso_id'   => $tipo->id,
                    'serial'            => $serial,
                    'placa'             => $this->get($row, 'placa'),
                    'marca'             => $this->get($row, 'marca'),
                    'modelo'            => $this->get($row, 'modelo'),
                    'nombre_equipo'     => $this->get($row, 'nombre_equipo'),
                    'estado_operativo'  => $this->get($row, 'estado_operativo') ?? 'Activo',
                    'razon_estado'      => $this->get($row, 'razon_estado'),
                    'procesador'        => $this->get($row, 'procesador'),
                    'ram'               => $this->get($row, 'ram'),
                    'disco'             => $this->get($row, 'disco'),
                    'sistema_operativo' => $this->get($row, 'sistema_operativo'),
                    'fecha_compra'      => $this->parseDate($this->get($row, 'fecha_compra')),
                    'fin_garantia'      => $this->parseDate($this->get($row, 'fin_garantia')),
                    'tiempo_uso'        => $this->get($row, 'tiempo_uso'),
                ]);

                // 3. Crear usuario asignado
                UsuarioAsignado::create([
                    'equipo_id'           => $equipo->id,
                    'nombre'              => $this->get($row, 'nombre_usuario'),
                    'cedula'              => $this->get($row, 'cedula'),
                    'empresa_propietaria' => $this->get($row, 'empresa_propietaria'),
                    'dependencia'         => $this->get($row, 'dependencia'),
                    'fuente_recurso'      => $this->get($row, 'fuente_recurso'),
                    'empresa_funcionario' => $this->get($row, 'empresa_funcionario'),
                    'tipo_vinculacion'    => $this->get($row, 'tipo_vinculacion'),
                    'shortname'           => $this->get($row, 'shortname'),
                    'departamento'        => $this->get($row, 'departamento'),
                    'ciudad'              => $this->get($row, 'ciudad'),
                    'cargo'               => $this->get($row, 'cargo'),
                    'area'                => $this->get($row, 'area'),
                    'piso'                => $this->get($row, 'piso'),
                ]);

                return $equipo;
            });

            $this->insertados++;
            return $equipo;

        } catch (\Exception $e) {
            $this->rowFailures[] = [
                'fila'   => $this->currentRow,
                'errores' => ['Error interno: ' . $e->getMessage()],
            ];
            Log::error("Import excepción fila {$this->currentRow}", ['msg' => $e->getMessage()]);
            return null;
        }
    }

    // ── Getters para el controlador ───────────────────────────────────────────

    public function getInsertados(): int    { return $this->insertados; }
    public function getOmitidos(): int      { return $this->omitidos; }
    public function getRowFailures(): array { return $this->rowFailures; }
    public function chunkSize(): int        { return 100; }

    // ── Resolución de columnas con mapeo de alias ─────────────────────────────

    /**
     * Busca un campo interno usando todos sus alias posibles.
     * WithHeadingRow ya convierte los encabezados a minúsculas con guiones bajos;
     * aquí también normalizamos los alias para igualar ese formato.
     */
    private function get(array $row, string $campo): ?string
    {
        $aliases = self::COLUMN_MAP[$campo] ?? [$campo];

        foreach ($aliases as $alias) {
            // Normalizar alias al mismo formato que usa WithHeadingRow
            $key = strtolower(trim(preg_replace('/[\s\-]+/', '_', $alias)));
            if (array_key_exists($key, $row)) {
                return $this->limpiar($row[$key]);
            }
        }

        return null;
    }

    // ── Validación manual ─────────────────────────────────────────────────────

    private function validarFila(?string $tipo, ?string $serial): array
    {
        $errores = [];

        if (empty($tipo)) {
            $errores[] = 'Campo "tipo_recurso" (o equivalente) obligatorio.';
        }

        if (empty($serial)) {
            $errores[] = 'Campo "serial" obligatorio.';
        } else {
            $existe = Equipo::where('serial', $serial)->whereNull('deleted_at')->exists();
            if ($existe) {
                $errores[] = "Serial \"{$serial}\" ya registrado — omitido.";
            }
        }

        return $errores;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Elimina espacios extra y devuelve null si queda vacío. */
    private function limpiar(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $v = trim(preg_replace('/\s+/', ' ', (string) $value));
        return $v === '' ? null : $v;
    }

    /** Convierte fechas numéricas de Excel o strings a Y-m-d. */
    private function parseDate(mixed $value): ?string
    {
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date
                    ::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Exception) {
                return null;
            }
        }

        try {
            return Carbon::parse((string) $value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }

    /** Detecta si el tipo corresponde a un periférico. */
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

    /** Verifica si una fila está completamente vacía. */
    private function filaVacia(array $row): bool
    {
        foreach ($row as $v) {
            if (trim((string) $v) !== '') {
                return false;
            }
        }
        return true;
    }
}
