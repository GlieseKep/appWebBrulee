

<?php $__env->startSection('title', 'Brúlée — Catálogo de Productos'); ?>

<?php $__env->startSection('cabecera-text'); ?>
    <h1 class="Texto-cabecera m-3">Catálogo de Productos</h1>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container my-4">
        <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            
            <h2 class="Pagina mt-5 mb-3 text-center p-2"><?php echo e($categoria->nombre); ?></h2>
            <p class="text-center text-muted small mb-4"><?php echo e($categoria->descripcion); ?></p>

            
            <div class="row g-4 mb-5">
                <?php $__currentLoopData = $categoria->productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="card shadow-sm h-100">
                            <a href="<?php echo e(route('productos.show', $producto->id)); ?>" class="text-decoration-none text-reset">
                                <img src="<?php echo e($producto->imagen_url); ?>" class="card-img-top"
                                    alt="<?php echo e($producto->alt_imagen ?? $producto->nombre); ?>"
                                    style="height: 200px; object-fit: cover;">
                            </a>
                            <div class="card-body d-flex flex-column justify-content-between">
                                <a href="<?php echo e(route('productos.show', $producto->id)); ?>" class="text-decoration-none text-reset">
                                    <h5 class="card-title Titulo-producto"><?php echo e($producto->nombre); ?></h5>
                                    <p class="card-text small text-muted"><?php echo e(Str::limit($producto->descripcion, 60)); ?></p>
                                    <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                                        <p class="fw-bold text-primary mb-0">$<?php echo e(number_format($producto->precio, 2)); ?></p>
                                        <div class="input-group input-group-sm" style="width: 100px;">
                                            <button class="btn btn-outline-secondary btn-minus" type="button">−</button>
                                            <input type="number" class="form-control text-center qty-input" value="1" min="1"
                                                max="<?php echo e($producto->stock ?? 99); ?>">
                                            <button class="btn btn-outline-secondary btn-plus" type="button">+</button>
                                        </div>
                                    </div>
                                </a>
                                <button class="btn btn-sm btn-outline-primary w-100 btn-agregar-carrito mt-auto"
                                    data-id="<?php echo e($producto->id); ?>" data-nombre="<?php echo e($producto->nombre); ?>"
                                    data-precio="<?php echo e($producto->precio); ?>" data-imagen="<?php echo e($producto->imagen_url); ?>">
                                    Agregar al carrito
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function () {
            // Botones +/-
            $('.btn-plus').click(function (e) {
                e.preventDefault();
                const input = $(this).siblings('.qty-input');
                const val = parseInt(input.val());
                const max = parseInt(input.attr('max')) || 99;
                if (val < max) input.val(val + 1);
            });

            $('.btn-minus').click(function (e) {
                e.preventDefault();
                const input = $(this).siblings('.qty-input');
                const val = parseInt(input.val());
                if (val > 1) input.val(val - 1);
            });

            $('.btn-agregar-carrito').click(function (e) {
                e.preventDefault();
                e.stopPropagation();

                const btn = $(this);
                const card = btn.closest('.card');
                const cantidad = parseInt(card.find('.qty-input').val()) || 1;

                const producto = {
                    id: parseInt(btn.data('id')),
                    nombre: btn.data('nombre'),
                    precio: parseFloat(btn.data('precio')),
                    cantidad: cantidad,
                    imagen: btn.data('imagen')
                };

                if (window.Carrito) {
                    window.Carrito.agregar(producto);
                    const originalText = btn.text();
                    btn.removeClass('btn-outline-primary').addClass('btn-success').text('¡Agregado!');
                    setTimeout(() => {
                        btn.removeClass('btn-success').addClass('btn-outline-primary').text(originalText);
                    }, 1500);
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pdani\Downloads\Desarrollo\app2\appWebBrulee\resources\views/productos/index.blade.php ENDPATH**/ ?>