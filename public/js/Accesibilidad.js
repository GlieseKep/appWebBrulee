class controlarAccesibilidad {
    constructor() {
        this.root = document.documentElement;
        // Recuperar estados de localStorage para persistencia entre páginas
        this.fontSize = parseInt(localStorage.getItem('fontSize')) || 100;
        this.altoContraste = localStorage.getItem('altoContraste') === 'true';
        this.sinImagenes = localStorage.getItem('sinImagenes') === 'true';

        this.init();
    }

    init() {
        this.aplicarCambios();
        this.configurarAccesibilidad();
    }

    aplicarCambios() {
        // Aplicar tamaño de letra
        this.root.style.setProperty('font-size', `${this.fontSize}%`, 'important');

        // Aplicar clase de Alto Contraste al body
        document.body.classList.toggle('alto-contraste', this.altoContraste);

        // Aplicar lógica de imágenes
        if (this.sinImagenes) {
            this.reemplazarImagenesPorAlt();
        } else {
            this.restaurarImagenes();
        }
    }

    configurarAccesibilidad() {
        const btnContraste = document.getElementById('btn-contraste');
        const btnAumentar = document.getElementById('btn-aumentar');
        const btnDisminuir = document.getElementById('btn-disminuir');
        const btnRestablecer = document.getElementById('btn-restablecer');
        const btnImagenes = document.getElementById('btn-quitar-imagenes');

        btnContraste?.addEventListener('click', () => {
            this.altoContraste = !this.altoContraste;
            localStorage.setItem('altoContraste', this.altoContraste);
            this.aplicarCambios();
        });

        btnAumentar?.addEventListener('click', () => {
            if (this.fontSize < 180) {
                this.fontSize += 10;
                localStorage.setItem('fontSize', this.fontSize);
                this.aplicarCambios();
            }
        });

        btnDisminuir?.addEventListener('click', () => {
            if (this.fontSize > 50) {
                this.fontSize -= 10;
                localStorage.setItem('fontSize', this.fontSize);
                this.aplicarCambios();
            }
        });

        btnImagenes?.addEventListener('click', () => {
            this.sinImagenes = !this.sinImagenes;
            localStorage.setItem('sinImagenes', this.sinImagenes);
            this.aplicarCambios();
        });

        btnRestablecer?.addEventListener('click', () => {
            this.fontSize = 100;
            this.altoContraste = false;
            this.sinImagenes = false;
            localStorage.clear();
            this.aplicarCambios();
        });
    }

    reemplazarImagenesPorAlt() {
        document.querySelectorAll('img').forEach(img => {
            if (!img.dataset.altReemplazado && img.alt) {
                const span = document.createElement('span');
                span.textContent = img.alt;
                span.className = 'alt-texto';

                img.style.display = 'none';
                img.after(span);

                img.dataset.altReemplazado = 'true';
            }
        });
    }

    restaurarImagenes() {
        document.querySelectorAll('img').forEach(img => {
            if (img.dataset.altReemplazado) {
                img.style.display = '';
                img.removeAttribute('data-alt-reemplazado');

                const next = img.nextElementSibling;
                if (next && next.classList.contains('alt-texto')) {
                    next.remove();
                }
            }
        });
    }
}