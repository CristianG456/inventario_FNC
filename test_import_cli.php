<?php
require '/var/www/vendor/autoload.php';
\ = require '/var/www/bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Console\Kernel::class);
\->bootstrap();

try {
    \ = new App\Imports\EquiposImport('/var/www/storage/app/public/dummy.xlsx', 'Admin');
    echo 'Instantiated.\n';
} catch (\Throwable \) {
    echo 'Error: ' . \->getMessage() . '\n';
}
