<?php $__env->startSection('title', 'Brúlée — Inicio de Sesión'); ?>

<?php $__env->startSection('content'); ?>
    <h1 class="Texto-cabecera m-3">Iniciar Sesión</h1>
    <main class="container-fluid login-page" role="main">
        <div class="row w-100 justify-content-center g-4">

            
            <section class="col-lg-6 d-flex justify-content-center align-items-center gap-4 decor-col">
                <div class="d-flex flex-column gap-4">
                    <img src="<?php echo e(asset('assets/img/gatoGordo.jpg')); ?>" alt="Postre Gato Gordo Roll"
                        class="img-fluid rounded shadow-sm">
                    <img src="<?php echo e(asset('assets/img/julieta.jpg')); ?>" alt="Arreglo floral junto a postres"
                        class="img-fluid rounded shadow-sm">
                </div>
                <img src="<?php echo e(asset('assets/img/macarron-de-fresa.jpg')); ?>" alt="Macarrón de fresa"
                    class="img-fluid rounded shadow-sm">
            </section>

            
            <section class="col-lg-4 col-md-8">
                <div class="login-card p-4">
                    <div class="text-center mb-4">
                        <h2>Login</h2>
                        <div class="circle-icon">D:</div>
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

                    <?php if(session('success')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('success')); ?>

                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('login.post')); ?>" method="POST" class="mb-4">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label for="nombre" class="form-label small">Nombre</label>
                            <input id="nombre" name="nombre" type="text" class="form-control" placeholder="Nombre"
                                value="<?php echo e(old('nombre')); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="correo" class="form-label small">Email</label>
                            <input id="correo" name="correo" type="email" class="form-control"
                                placeholder="correo@gmail.com" value="<?php echo e(old('correo')); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="clave" class="form-label small">Contraseña</label>
                            <input id="clave" name="clave" type="password" class="form-control" placeholder="**************"
                                required>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary colorB" style="width: 100%;">
                                Iniciar
                            </button>
                        </div>
                    </form>

                    <div class="text-center mb-3">
                        <span class="small">¿No tienes cuenta?</span>
                        <a href="<?php echo e(route('registro')); ?>" class="small colorB">Regístrate</a>
                    </div>

                    <div class="text-center">
                        <div class="circle-icon">:D</div>
                    </div>
                </div>
            </section>
        </div>
    </main>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\pdani\Downloads\Desarrollo\app2\appWebBrulee\resources\views/auth/login.blade.php ENDPATH**/ ?>