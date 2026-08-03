<?php
$content = file_get_contents('resources/views/equipos/show.blade.php');

$perifericosBlock = <<<HTML
        {{-- Periféricos --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-warning bg-opacity-10 fw-semibold border-0 py-3">
                <i class="bi bi-usb-plug me-2 text-warning"></i>Periféricos
            </div>
            <div class="card-body">
                @if(\$equipo->periferico)
                    <dl class="row mb-0">
                        <dt class="col-sm-4 text-muted">Teléfono Fijo</dt>
                        <dd class="col-sm-8">{{ \$equipo->periferico->telefono ?? '—' }}</dd>
                        <dt class="col-sm-4 text-muted">Teclado</dt>
                        <dd class="col-sm-8">{{ \$equipo->periferico->teclado ?? '—' }}</dd>
                        <dt class="col-sm-4 text-muted">Mouse</dt>
                        <dd class="col-sm-8">{{ \$equipo->periferico->mouse ?? '—' }}</dd>
                        <dt class="col-sm-4 text-muted">Cámara</dt>
                        <dd class="col-sm-8">{{ \$equipo->periferico->camara ?? '—' }}</dd>
                    </dl>
                @else
                    <p class="text-muted mb-0">Sin periféricos registrados.</p>
                @endif
            </div>
        </div>
HTML;

// Find the start and end of Periféricos
$pStart = strpos($content, "{{-- Periféricos --}}");
$pEndStr = "</div>\n        </div>\n    </div>\n</div>";
$pEnd = strpos($content, "    </div>\n</div>", $pStart);

// Delete the Periféricos block from where it was
$content = str_replace($perifericosBlock . "\n", "", $content);

// Insert it at the bottom of the Left Column
$insertPoint = <<<HTML
        {{-- Complementos del Activo --}}
        @if(\$equipo->complementos->isNotEmpty() || auth()->user()->can('equipos.editar'))
            @include('equipos._complementos_show')
        @endif
HTML;

$replacement = $insertPoint . "\n\n" . $perifericosBlock;
$content = str_replace($insertPoint, $replacement, $content);

file_put_contents('resources/views/equipos/show.blade.php', $content);
echo "Done";
?>
