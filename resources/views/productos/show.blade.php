@extends('layouts.app')

@section('title', 'Brúlée — ' . $producto->nombre)

@section('cabecera-text')
    <h1 class="Texto-cabecera m-3">Detalle producto</h1>
@endsection

@section('content')
    <div class="contenedor my-4">
        <div class="Productos">
            <img id="Imagen-detalle" src="{{ $producto->imagen_url }}"
                alt="{{ $producto->alt_imagen ?? $producto->nombre }}">
            <div>
                <h2> Descripción </h2>
                <p id="Texto-detalle-producto">{{ $producto->descripcion }}</p>
                <p><strong>Categoría:</strong> {{ $producto->categoria->nombre }}</p>
                @if($producto->stock > 0)
                    <p><strong>Stock disponible:</strong> {{ $producto->stock }} unidades</p>
                @else
                    <div class="alert alert-warning">Producto agotado</div>
                @endif
            </div>
        </div>

        <h2 class="Titulo-producto" id="titulo-producto">{{ $producto->nombre }}</h2>
        <h2 class="Titulo-producto" id="Precio-producto">${{ number_format($producto->precio, 2) }}</h2>

        <div class="text-end m-3">
            @if($producto->stock > 0)
                <button class="btn btn-primary btn-lg" id="btn-comprar" data-bs-toggle="modal" data-bs-target="#modalCompra">
                    Comprar
                </button>
            @endif
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary btn-lg ms-2">
                Volver
            </a>
        </div>

        <!-- Modal de Compra (Carrito) -->
        <div class="modal fade" id="modalCompra" tabindex="-1" aria-labelledby="modalCompraLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCompraLabel">Eliga su producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="formComprar">
                            <div class="mb-3">
                                <label for="selectProducto" class="form-label">Producto</label>
                                <select id="selectProducto" class="form-select" required>
                                    <option value="{{ $producto->id }}" selected>{{ $producto->nombre }}</option>
                                    @foreach($productosSimilares as $rel)
                                        <option value="{{ $rel->id }}">{{ $rel->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 d-flex align-items-center justify-content-center gap-3">
                                <label for="cantidad" class="form-label">Cantidad</label>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <button type="button" class="btn btn-outline-secondary" id="btn-restar">−</button>
                                    <input type="number" id="cantidad" class="form-control text-center" style="width: 80px;"
                                        value="1" min="1" max="{{ $producto->stock }}">
                                    <button type="button" class="btn btn-outline-secondary" id="btn-sumar">+</button>
                                </div>
                            </div>
                        </form>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" id="btn-confirmar">Agregar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Productos similares visualización extra si se desea --}}
        @if($productosSimilares->count() > 0)
            <div class="mt-5 container">
                <h3 class="mb-4 text-center">También te podría gustar</h3>
                <div class="row g-4 justify-content-center">
                    @foreach($productosSimilares as $similar)
                        <div class="col-12 col-sm-6 col-md-3">
                            <a href="{{ route('productos.show', $similar->id) }}" class="text-decoration-none text-reset">
                                <div class="card shadow-sm h-100">
                                    <img src="{{ $similar->imagen_url }}" class="card-img-top"
                                        alt="{{ $similar->alt_imagen ?? $similar->nombre }}"
                                        style="height: 200px; object-fit: cover;">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">{{ $similar->nombre }}</h5>
                                        <p class="fw-bold text-primary">${{ number_format($similar->precio, 2) }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Sumar/Restar cantidad
            $('#btn-sumar').click(function () {
                let val = parseInt($('#cantidad').val()) || 1;
                let max = parseInt($('#cantidad').attr('max')) || 100; // Obtener max del atributo
                if (val < max) $('#cantidad').val(val + 1);
            });
            $('#btn-restar').click(function () {
                let val = parseInt($('#cantidad').val()) || 1;
                if (val > 1) $('#cantidad').val(val - 1);
            });

            // Agregar al carrito
            $('#btn-confirmar').click(function () {
                const prdId = $('#selectProducto').val();
                const cantidad = parseInt($('#cantidad').val());

                // Obtener datos del producto seleccionado
                const $option = $("#selectProducto option:selected");
                const nombre = $option.text().trim();

                // En un escenario real, deberíamos obtener el precio e imagen del producto seleccionado si es diferente al principal.
                // Aquí asumimos que si es el principal, usamos sus datos. Si es otro, usamos datos genéricos o recargamos.
                // Para mantenerlo simple y funcional con la UI actual:

                const producto = {
                    id: parseInt(prdId),
                    nombre: nombre,
                    // Nota: Usamos el precio del producto principal por simplicidad. 
                    // Idealmente el select debería tener data-precio.
                    precio: {{ $producto->precio }},
                    cantidad: cantidad,
                    imagen: "{{ $producto->imagen_url }}"
                };

                // Usar módulo global
                if (window.Carrito) {
                    window.Carrito.agregar(producto);

                    alert(cantidad + ' unidad(es) de "' + nombre + '" agregada(s) al carrito.');

                    // Cerrar modal
                    const modalEl = document.getElementById('modalCompra');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                } else {
                    console.error('El módulo Carrito no está cargado');
                    alert('Error al agregar al carrito');
                }
            });

            // Al cambiar el select, redirigir a la página del producto seleccionado para ver su precio/info correcta
            $('#selectProducto').change(function () {
                const newId = $(this).val();
                if (newId != {{ $producto->id }}) {
                    window.location.href = '/productos/' + newId;
                }
            });
        });
    </script>
@endpush