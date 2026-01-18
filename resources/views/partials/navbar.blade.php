<nav class="navbar sticky-top navbar-expand-lg p-1" style="background: var(--brand-2);"
    aria-label="Navegación principal">
    <div class="container-fluid d-flex justify-content-between">
        <!-- Lado izquierdo -->
        <div class="navbar-brand m-0">
            <a href="{{ route('home') }}" class="btn btn-outline-dark" aria-label="Regresar">
                <i class="bi bi-arrow-left"></i>
            </a>

            <button class="btn btn-primary" type="button" id="btn-accesibilidad" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling" aria-label="Accesibilidad">
                <i class="bi bi-universal-access"></i>
            </button>
        </div>

        <!-- Lado derecho -->
        <div class="d-flex align-items-center gap-2">
            <a href="#" class="btn btn-outline-dark fw-semibold">
                Iniciar a comprar
            </a>

            @if(session()->has('usuario_id'))
                {{-- Usuario logueado --}}
                <button class="btn btn-primary" type="button" id="btn-perfil" aria-label="Perfil">
                    Hola, {{ session('usuario_nombre') }} <i class="bi bi-person-circle"></i>
                </button>

                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Cerrar sesión">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            @else
                {{-- Usuario invitado --}}
                <a href="{{ route('login') }}" class="btn btn-primary" aria-label="Iniciar sesión">
                    Invitado <i class="bi bi-person-circle"></i>
                </a>
            @endif
        </div>
    </div>
</nav>

{{-- Modal de perfil --}}
@if(session()->has('usuario_id'))
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-guardar-perfil">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                const modalPerfil = new bootstrap.Modal('#modalPerfil');

                // Abrir modal de perfil
                $('#btn-perfil').on('click', function (e) {
                    e.preventDefault();

                    // Cargar datos del perfil
                    $.get('{{ route("perfil.datos") }}', function (data) {
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

                    $.post('{{ route("perfil.actualizar.nombre") }}', {
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
            });
        </script>
    @endpush
@endif