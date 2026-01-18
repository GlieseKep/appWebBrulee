@extends('layouts.app')

@section('title', 'Brúlée — Contáctanos')

@section('cabecera-text')
    <h1 class="Texto-cabecera m-3">Contáctanos</h1>
@endsection

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="login-card p-4">
                    <h2 class="mb-4">Información de Contacto</h2>

                    <div class="mb-3">
                        <h5><i class="bi bi-envelope-fill"></i> Email</h5>
                        <p>brulee-catering@gmail.com</p>
                    </div>

                    <div class="mb-3">
                        <h5><i class="bi bi-telephone-fill"></i> Teléfono</h5>
                        <p>+593 99 999 9999</p>
                    </div>

                    <div class="mb-3">
                        <h5><i class="bi bi-geo-alt-fill"></i> Dirección</h5>
                        <p>Calderón; calle carapungo oe3-183 y Serange, Quito</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection