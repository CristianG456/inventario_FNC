<?php
$aliases = ['nombres_y_apellidos', 'nombre_usuario'];
$row = [
    'nombres_y_apellidos' => 'JUAN PEREZ',
    'cedula_del_funcionariocontratista' => '123456',
    'shortname' => '123456',
    'serial' => '123',
    'tipo_de_recurso' => 'PC'
];

function get(array $row, array $aliases): ?string {
    foreach ($aliases as $alias) {
        $aliasFlat = strtolower(preg_replace('/[^a-z0-9]/i', '', $alias));
        foreach ($row as $key => $value) {
            $keyFlat = strtolower(preg_replace('/[^a-z0-9]/i', '', $key));
            if ($keyFlat === $aliasFlat) {
                return $value;
            }
        }
    }
    return null;
}

echo "Nombre: " . get($row, $aliases) . "\n";
echo "Cedula: " . get($row, ['cedula_del_funcionariocontratista']) . "\n";
