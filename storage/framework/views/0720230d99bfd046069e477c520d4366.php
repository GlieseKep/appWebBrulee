<nav class="navbar sticky-top navbar-expand-lg p-1" style="background: var(--brand-2);"
    aria-label="Navegación principal">
    <div class="container-fluid d-flex justify-content-between">
        <!-- Lado izquierdo -->
        <div class="navbar-brand m-0">
            <a href="<?php echo e(route('home')); ?>" class="btn btn-outline-dark" aria-label="Regresar">
                <i class="bi bi-arrow-left"></i>
            </a>

            <button class="btn btn-primary" type="button" id="btn-accesibilidad" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling" aria-label="Accesibilidad">
                <i class="bi bi-universal-access"></i>
            </button>
        </div>

        <!-- Lado derecho -->
        <div class="d-flex align-items-center gap-2">
            <a href="<?php echo e(route('productos.index')); ?>" class="btn btn-outline-dark fw-semibold">
                Iniciar a comprar
            </a>

            <!-- Botón del carrito -->
            <button class="btn btn-primary position-relative" type="button" data-bs-toggle="modal"
                data-bs-target="#modalCarrito" aria-label="Ver carrito">
                <i class="bi bi-cart"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                    id="carrito-badge" style="display: none;">0</span>
            </button>

            <?php if(session()->has('usuario_id')): ?>
                
                <button class="btn btn-primary" type="button" id="btn-perfil" aria-label="Perfil">
                    Hola, <?php echo e(session('usuario_nombre')); ?> <i class="bi bi-person-circle"></i>
                </button>

                <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Cerrar sesión">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            <?php else: ?>
                
                <a href="<?php echo e(route('login')); ?>" class="btn btn-primary" aria-label="Iniciar sesión">
                    Invitado <i class="bi bi-person-circle"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>


<?php if(session()->has('usuario_id')): ?>
    <div class="modal fade" id="modalPerfil" tabindex="-1" aria-labelledby="modalPerfilLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPerfilLabel">Mi Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small">Correo electrónico</label>
                        <input type="email" class="form-control" id="perfil-correo" readonly>
                        <small class="text-muted">El correo no se puede modificar</small>
                    </div>

                    <div class="mb-3">
                        <label for="perfil-nombre" class="form-label small">Nombre de usuario</label>
                        <input type="text" class="form-control" id="perfil-nombre" placeholder="Tu nombre">
                        <div id="perfil-error" class="text-danger small mt-1" style="display:none;"></div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-danger btn-sm" id="btn-eliminar-cuenta">
                        <i class="bi bi-trash"></i> Eliminar cuenta
                    </button>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btn-guardar-perfil">Guardar cambios</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script>
            $(document).ready(function () {
                const modalPerfil = new bootstrap.Modal('#modalPerfil');

                // Abrir modal de perfil
                $('#btn-perfil').on('click', function (e) {
                    e.preventDefault();

                    // Cargar datos del perfil
                    $.get('<?php echo e(route("perfil.datos")); ?>', function (data) {
                        $('#perfil-correo').val(data.correo);
                        $('#perfil-nombre').val(data.nombre);
                        modalPerfil.show();
                    }).fail(function () {
                        alert('Error al cargar el perfil');
                    });
                });

                // Guardar cambios
                $('#btn-guardar-perfil').on('click', function () {
                    const nuevoNombre = $('#perfil-nombre').val().trim();

                    if (nuevoNombre.length < 3) {
                        $('#perfil-error').text('El nombre debe tener al menos 3 caracteres').show();
                        return;
                    }

                    $.post('<?php echo e(route("perfil.actualizar.nombre")); ?>', {
                        nombre: nuevoNombre
                    }, function (response) {
                        if (response.success) {
                            alert('Nombre actualizado correctamente');
                            location.reload(); // Recargar para actualizar el navbar
                        }
                    }).fail(function (xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            $('#perfil-error').text(xhr.responseJSON.errors.nombre[0]).show();
                        } else {
                            $('#perfil-error').text('Error al actualizar el nombre').show();
                        }
                    });
                });

                // Eliminar cuenta
                $('#btn-eliminar-cuenta').on('click', function () {
                    if (!confirm('⚠️ ¿Estás seguro de que deseas eliminar tu cuenta?\n\nEsta acción no se puede deshacer.')) {
                        return;
                    }

                    // Verificar si tiene pedidos
                    $.get('<?php echo e(route("perfil.verificar.pedidos")); ?>', function (response) {
                        if (response.tienePedidos) {
                            alert('😔 No puedes eliminar tu cuenta porque ya has realizado pedidos.\n\nTus datos están asociados a transacciones.');
                            return;
                        }

                        // Proceder con eliminación
                        $.ajax({
                            url: '<?php echo e(route("perfil.eliminar.cuenta")); ?>',
                            type: 'DELETE',
                            success: function (response) {
                                if (response.success) {
                                    alert(response.mensaje);
                                    window.location.href = '<?php echo e(route("home")); ?>';
                                } else {
                                    alert(response.mensaje);
                                }
                            },
                            error: function () {
                                alert('Error al eliminar la cuenta. Por favor, inténtalo de nuevo.');
                            }
                        });
                    }).fail(function () {
                        alert('Error al verificar pedidos');
                    });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>


<div class="modal fade" id="modalCarrito" tabindex="-1" aria-labelledby="modalCarritoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCarritoLabel">
                    <i class="bi bi-cart"></i> Mi Carrito
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="carrito-items">
                    <!-- Los items se renderizan dinámicamente con JavaScript -->
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div>
                    <h5 class="mb-0">Total: <span class="text-primary">$<span id="carrito-total">0.00</span></span></h5>
                </div>
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Seguir comprando</button>
                    <a href="<?php echo e(route('checkout.index')); ?>" class="btn btn-success" id="btn-finalizar-compra">
                        <i class="bi bi-check-circle"></i> Finalizar compra
                    </a>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH C:\Users\pdani\Downloads\Desarrollo\app2\appWebBrulee\resources\views/partials/navbar.blade.php ENDPATH**/ ?>