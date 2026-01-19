<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'Brúlée Catering & Event Design'); ?></title>

    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@400;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?php echo e(asset('css/estilos.css')); ?>">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>

    <div class="contenedor">
        <!-- CABECERA -->
        <div id="Cabecera" class="Pagina">
            <img id="Cabecera-logo" src="<?php echo e(asset('assets/img/Logo.png')); ?>" alt="Logo brûlée">
            <?php echo $__env->yieldContent('cabecera-text'); ?>
        </div>

        <!-- NAVBAR -->
        <?php echo $__env->make('partials.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- CONTENIDO PRINCIPAL -->
        <main>
            <?php echo $__env->yieldContent('content'); ?>
        </main>

        <!-- FOOTER -->
        <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <!-- OFFCANVAS ACCESIBILIDAD -->
    <?php echo $__env->make('partials.offcanvas-accesibilidad', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script de accesibilidad -->
    <script src="<?php echo e(asset('js/accesibilidad.js')); ?>"></script>

    <!-- Script del carrito de compras -->
    <script src="<?php echo e(asset('js/carrito.js')); ?>"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <script>
        // Configurar AJAX con CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Inicializar accesibilidad
        document.addEventListener("DOMContentLoaded", () => {
            new controlarAccesibilidad();
        });
    </script>
</body>

</html><?php /**PATH C:\Users\pdani\Downloads\Desarrollo\app2\appWebBrulee\resources\views/layouts/app.blade.php ENDPATH**/ ?>