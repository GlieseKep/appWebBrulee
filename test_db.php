<?php
try {
    echo "Iniciando prueba de conexión...\n";

    // Check extension
    if (!extension_loaded('pdo_oci') && !extension_loaded('oci8')) {
        throw new Exception("La extensión OCI8/PDO_OCI no está cargada en PHP. Revisa php.ini y el PATH.");
    }
    echo "Extensiones OCI detectadas.\n";

    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    echo "Intentando conectar a DB...\n";
    $pdo = \DB::connection()->getPdo();

    if ($pdo) {
        echo "¡ÉXITO! Conexión a Oracle establecida correctamente.\n";
    } else {
        echo "FALLO: No se pudo obtener el objeto PDO.\n";
    }

} catch (\Exception $e) {
    echo "ERROR GRAVE: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
