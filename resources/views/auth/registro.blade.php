@extends('layouts.app')

@section('title', 'Brúlée — Registro')

@section('content')
    <h1 class="Texto-cabecera m-3">Registrarse</h1>
    <main class="container-fluid login-page">
        <div class="row w-100 justify-content-center g-4">

            <div class="col-lg-6 d-flex justify-content-center align-items-center gap-4 decor-col">
                <div class="d-flex flex-column gap-4">
                    <img src="{{ asset('assets/img/macarron-de-fresa.jpg') }}" alt="Macarrón de fresa"
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

                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('registro.post') }}" method="POST" class="mb-4">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small" for="nombre">Nombre</label>
                            <input id="nombre" name="nombre" type="text" class="form-control" placeholder="Tu nombre"
                                value="{{ old('nombre') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small" for="correo">Correo</label>
                            <input id="correo" name="correo" type="email" class="form-control"
                                placeholder="ejemplo@gmail.com" value="{{ old('correo') }}" required>
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
                        <a href="{{ route('login') }}" class="fw-semibold colorB">Inicia sesión</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection