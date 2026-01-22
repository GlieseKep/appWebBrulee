@extends('layouts.app')

@section('title', 'Brúlée — Catálogo de Productos')

@section('cabecera-text')
    <h1 class="Texto-cabecera m-3">Catálogo de Productos</h1>
@endsection

@section('content')
    <div class="container my-4">
        @foreach($categorias as $categoria)
            {{-- Título de categoría --}}
            <h2 class="Pagina mt-5 mb-3 text-center p-2">{{ $categoria->nombre }}</h2>
            <p class="text-center text-muted small mb-4">{{ $categoria->descripcion }}</p>

            {{-- Grid de productos --}}
            <div class="row g-4 mb-5">
                @foreach($categoria->productos as $producto)
                    <div class="col-12 col-sm-6 col-md-3">
                        <div class="card shadow-sm h-100">
                            <a href="{{ route('productos.show', ['id' => $producto->id]) }}" class="text-decoration-none text-reset">
                                <img src="{{ $producto->imagen_url }}" class="card-img-top"
                                    alt="{{ $producto->alt_imagen ?? $producto->nombre }}"
                                    style="height: 200px; object-fit: cover;">
                            </a>
                            <div class="card-body d-flex flex-column justify-content-between">
                                <a href="{{ route('productos.show', ['id' => $producto->id]) }}" class="text-decoration-none text-reset">
                                    <h5 class="card-title Titulo-producto">{{ $producto->nombre }}</h5>
                                    <p class="card-text small text-muted">{{ Str::limit($producto->descripcion, 60) }}</p>
                                    <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                                        <p class="fw-bold text-primary mb-0">${{ number_format($producto->precio, 2) }}</p>
                                        <div class="input-group input-group-sm" style="width: 100px;">
                                            <button class="btn btn-outline-secondary btn-minus" type="button">−</button>
                                            <input type="number" class="form-control text-center qty-input" value="1" min="1"
                                                max="{{ $producto->stock ?? 99 }}">
                                            <button class="btn btn-outline-secondary btn-plus" type="button">+</button>
                                        </div>
                                    </div>
                                </a>
                                <button class="btn btn-sm btn-outline-primary w-100 btn-agregar-carrito mt-auto"
                                    data-id="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}"
                                    data-precio="{{ $producto->precio }}" data-imagen="{{ $producto->imagen_url }}">
                                    Agregar al carrito
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
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
                    id: String(btn.data('id')),
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
@endpush