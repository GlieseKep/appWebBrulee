@extends('layouts.app')

@section('title', 'Brúlée — Inicio de Sesión')

@section('content')
    <h1 class="Texto-cabecera m-3">Iniciar Sesión</h1>
    <main class="container-fluid login-page" role="main">
        <div class="row w-100 justify-content-center g-4">

            {{-- Sección visual --}}
            <section class="col-lg-6 d-flex justify-content-center align-items-center gap-4 decor-col">
                <div class="d-flex flex-column gap-4">
                    <img src="{{ asset('assets/img/gatoGordo.jpg') }}" alt="Postre Gato Gordo Roll"
                        class="img-fluid rounded shadow-sm">
                    <img src="{{ asset('assets/img/julieta.jpg') }}" alt="Arreglo floral junto a postres"
                        class="img-fluid rounded shadow-sm">
                </div>
                <img src="{{ asset('assets/img/macarron-de-fresa.jpg') }}" alt="Macarrón de fresa"
                    class="img-fluid rounded shadow-sm">
            </section>

            {{-- Formulario de Login --}}
            <section class="col-lg-4 col-md-8">
                <div class="login-card p-4">
                    <div class="text-center mb-4">
                        <h2>Login</h2>
                        <div class="circle-icon">D:</div>
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

                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" class="mb-4">
                        @csrf

                        <div class="mb-3">
                            <label for="nombre" class="form-label small">Nombre</label>
                            <input id="nombre" name="nombre" type="text" class="form-control" placeholder="Nombre"
                                value="{{ old('nombre') }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="correo" class="form-label small">Email</label>
                            <input id="correo" name="correo" type="email" class="form-control"
                                placeholder="correo@gmail.com" value="{{ old('correo') }}" required>
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
                        <a href="{{ route('registro') }}" class="small colorB">Regístrate</a>
                    </div>

                    <div class="text-center">
                        <div class="circle-icon">:D</div>
                    </div>
                </div>
            </section>
        </div>
    </main>
@endsection