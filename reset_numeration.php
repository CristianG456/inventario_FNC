<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Equipo;

try {
    DB::beginTransaction();

    // Fetch all items (including trashed) sorted by ID
    $equipos = Equipo::withTrashed()->orderBy('id')->get();
    $totalCount = $equipos->count();
    
    echo "Total Equipos encontrados: {$totalCount}\n";
    
    $consecutivo = 1;
    $updatedCount = 0;
    
    foreach ($equipos as $equipo) {
        $equipo->consecutivo = $consecutivo;
        $equipo->timestamps = false; // Do not update 'updated_at' for this administrative change
        $equipo->save();
        
        $consecutivo++;
        $updatedCount++;
    }
    
    DB::commit();
    echo "Transacción completada exitosamente. {$updatedCount} registros actualizados.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error durante la actualización. ROLLBACK ejecutado.\n";
    echo "Detalle: " . $e->getMessage() . "\n";
}
