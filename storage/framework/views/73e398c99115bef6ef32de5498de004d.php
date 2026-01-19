<?php $__env->startSection('title', 'Brúlée — Registro'); ?>

<?php $__env->startSection('content'); ?>
    <h1 class="Texto-cabecera m-3">Registrarse</h1>
    <main class="container-fluid login-page">
        <div class="row w-100 justify-content-center g-4">

            <div class="col-lg-6 d-flex justify-content-center align-items-center gap-4 decor-col">
                <div class="d-flex flex-column gap-4">
                    <img src="<?php echo e(asset('assets/img/macarron-de-fresa.jpg')); ?>" alt="Macarrón de fresa"
                        class="img-fluid rounded shadow-sm">
                </div>
            </div>

            <div class="col-lg-4 col-md-8">
                <div class="login-card p-4">
                    <div class="text-center mb-4">
                        <h2>Crear cuenta</h2>
                        <div class="circle-icon">★</div>
                        <p class="small mb-0">
                            Crea tu cuenta para reservar tus catering y hacer tus pedidos.
                        </p>
                    </div>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('registro.post')); ?>" method="POST" class="mb-4">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label class="form-label small" for="nombre">Nombre</label>
                            <input id="nombre" name="nombre" type="text" class="form-control" placeholder="Tu nombre"
                                value="<?php echo e(old('nombre')); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small" for="correo">Correo</label>
                            <input id="correo" name="correo" type="email" class="form-control"
                                placeholder="ejemplo@gmail.com" value="<?php echo e(old('correo')); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small" for="clave">Contraseña</label>
                            <input id="clave" name="clave" type="password" class="form-control" placeholder="******"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small" for="clave_confirmation">Confirmar contraseña</label>
                            <input id="clave_confirmation" name="clave_confirmation" type="password" class="form-control"
                                placeholder="Repite la contraseña" required>
                        </div>

                        <div class="text-end row mt-3">
                            <button type="submit" class="btn btn-primary colorB">
                                Registrarme
                            </button>
                        </div>
                    </form>

                    <div class="text-center mb-3 small">
                        ¿Ya tienes cuenta?
                        <a href="<?php echo e(route('login')); ?>" class="fw-semibold colorB">Inicia sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pdani\Downloads\Desarrollo\app2\appWebBrulee\resources\views/auth/registro.blade.php ENDPATH**/ ?>