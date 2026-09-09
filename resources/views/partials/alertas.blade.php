{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Contenedor Global con Z-Index Supremo */
    div:where(.swal2-container) {
        z-index: 999999 !important;
    }

    /* Fondo Oscuro Elegante con Desenfoque */
    div:where(.swal2-container).swal2-backdrop-show:not(.swal2-top-end):not(.swal2-top-start):not(.swal2-bottom-end):not(.swal2-bottom-start) {
        background: rgba(15, 23, 42, 0.5) !important;
        backdrop-filter: blur(6px) !important;
        -webkit-backdrop-filter: blur(6px) !important;
    }

    /* PARA TOASTS: Totalmente transparente, CERO fondo negro/oscuro */
    div:where(.swal2-container).swal2-top-end,
    div:where(.swal2-container).swal2-top-start,
    div:where(.swal2-container).swal2-bottom-end,
    div:where(.swal2-container).swal2-bottom-start,
    div:where(.swal2-container):has(.powernet-swal-toast) {
        background: transparent !important;
        background-color: transparent !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        pointer-events: none !important;
    }

    div:where(.swal2-container) .powernet-swal-toast {
        pointer-events: auto !important;
        margin: 1rem 1rem 0 0 !important;
    }

    /* Modal emergente principal (Diseño Moderno & Elegante) */
    div:where(.swal2-container) div:where(.swal2-popup).powernet-swal-popup {
        border-radius: 2rem !important;
        padding: 2.25rem 2rem 2rem 2rem !important;
        font-family: 'Figtree', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        background: #ffffff !important;
        box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.3), 0 0 0 1px rgba(226, 232, 240, 0.8) !important;
        border: none !important;
        max-width: 27rem !important;
        position: relative !important;
        overflow: hidden !important;
        pointer-events: auto !important;
    }

    /* Animaciones controladas para entrada y salida limpia */
    div:where(.swal2-container) div:where(.swal2-popup).powernet-swal-popup.swal2-show {
        animation: powernetPopIn 0.22s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    div:where(.swal2-container) div:where(.swal2-popup).powernet-swal-popup.swal2-hide {
        animation: powernetPopOut 0.15s cubic-bezier(0.16, 1, 0.3, 1) forwards !important;
    }

    @keyframes powernetPopIn {
        from {
            opacity: 0;
            transform: scale(0.92) translateY(8px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    @keyframes powernetPopOut {
        from {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
        to {
            opacity: 0;
            transform: scale(0.92) translateY(8px);
        }
    }

    /* Barra de acento superior sutil */
    div:where(.swal2-container) div:where(.swal2-popup).powernet-swal-popup::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 5px !important;
        background: linear-gradient(90deg, #f59e0b 0%, #e11d48 50%, #7c3aed 100%) !important;
    }

    /* Título del Modal */
    div:where(.swal2-container) .powernet-swal-title {
        font-size: 1.3rem !important;
        font-weight: 900 !important;
        color: #0f172a !important;
        letter-spacing: -0.03em !important;
        margin-top: 0.65rem !important;
        margin-bottom: 0.5rem !important;
        padding: 0 !important;
        line-height: 1.3 !important;
    }

    /* Contenedor del Mensaje */
    div:where(.swal2-container) .powernet-swal-html {
        font-size: 0.875rem !important;
        font-weight: 500 !important;
        color: #475569 !important;
        line-height: 1.6 !important;
        margin: 0.4rem 0 1.5rem 0 !important;
        padding: 0 !important;
    }

    /* Íconos Modernizados con Halo Suave */
    div:where(.swal2-container) .powernet-swal-icon {
        margin: 0.25rem auto 0.75rem auto !important;
        width: 4.25rem !important;
        height: 4.25rem !important;
        border-width: 3px !important;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    /* Icono Error */
    div:where(.swal2-container) .powernet-swal-icon.swal2-error {
        border-color: #fecaca !important;
        background-color: #fff1f2 !important;
        box-shadow: 0 0 0 8px rgba(254, 226, 226, 0.6) !important;
    }

    div:where(.swal2-container) .powernet-swal-icon.swal2-error [class^='swal2-x-mark-line'] {
        background-color: #e11d48 !important;
        height: 3px !important;
    }

    /* Icono Éxito */
    div:where(.swal2-container) .powernet-swal-icon.swal2-success {
        border-color: #bbf7d0 !important;
        background-color: #f0fdf4 !important;
        box-shadow: 0 0 0 8px rgba(220, 252, 231, 0.6) !important;
    }

    div:where(.swal2-container) .powernet-swal-icon.swal2-success [class^='swal2-success-line'] {
        background-color: #10b981 !important;
    }

    /* Icono Advertencia */
    div:where(.swal2-container) .powernet-swal-icon.swal2-warning {
        border-color: #fde68a !important;
        background-color: #fffbeb !important;
        box-shadow: 0 0 0 8px rgba(254, 243, 199, 0.6) !important;
        color: #d97706 !important;
    }

    /* Icono Info */
    div:where(.swal2-container) .powernet-swal-icon.swal2-info {
        border-color: #ddd6fe !important;
        background-color: #f5f3ff !important;
        box-shadow: 0 0 0 8px rgba(237, 233, 254, 0.6) !important;
        color: #7c3aed !important;
    }

    /* Contenedor de Botones de Acción */
    div:where(.swal2-container) .powernet-swal-actions {
        gap: 0.75rem !important;
        width: 100% !important;
        margin-top: 0.75rem !important;
        display: flex !important;
        justify-content: center !important;
        z-index: 10 !important;
    }

    /* Botón Confirmar Principal (Sleek Dark Moderno) */
    div:where(.swal2-container) .powernet-btn-confirm,
    div:where(.swal2-container) .swal2-confirm {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
        color: #ffffff !important;
        font-size: 0.875rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.01em !important;
        padding: 0.85rem 2.6rem !important;
        border-radius: 1.15rem !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.35), 0 4px 6px -2px rgba(15, 23, 42, 0.15) !important;
        transition: all 0.18s ease-in-out !important;
        cursor: pointer !important;
        outline: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 0.5rem !important;
        pointer-events: auto !important;
        position: relative !important;
        z-index: 50 !important;
        user-select: none !important;
    }

    div:where(.swal2-container) .powernet-btn-confirm:hover,
    div:where(.swal2-container) .swal2-confirm:hover {
        background: linear-gradient(135deg, #1e293b 0%, #020617 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 14px 28px -6px rgba(15, 23, 42, 0.45) !important;
    }

    div:where(.swal2-container) .powernet-btn-confirm:active,
    div:where(.swal2-container) .swal2-confirm:active {
        transform: scale(0.97) !important;
    }

    /* Botón Peligro (Rojo Gradiente) */
    div:where(.swal2-container) .powernet-btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 10px 22px -5px rgba(239, 68, 68, 0.4) !important;
    }

    div:where(.swal2-container) .powernet-btn-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 14px 28px -6px rgba(239, 68, 68, 0.5) !important;
    }

    /* Botón Cancelar */
    div:where(.swal2-container) .powernet-btn-cancel,
    div:where(.swal2-container) .swal2-cancel {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        font-size: 0.875rem !important;
        font-weight: 700 !important;
        padding: 0.85rem 1.75rem !important;
        border-radius: 1.15rem !important;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.18s ease-in-out !important;
        cursor: pointer !important;
        outline: none !important;
        pointer-events: auto !important;
    }

    div:where(.swal2-container) .powernet-btn-cancel:hover,
    div:where(.swal2-container) .swal2-cancel:hover {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
        transform: translateY(-1px) !important;
    }

    /* Notificaciones Toast Modernas */
    div:where(.swal2-container) div:where(.swal2-popup).powernet-swal-toast {
        border-radius: 1.25rem !important;
        background: #ffffff !important;
        color: #0f172a !important;
        padding: 0.85rem 1.15rem !important;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06) !important;
        border: 1px solid #e2e8f0 !important;
    }

    div:where(.swal2-container) .powernet-swal-toast .swal2-title {
        color: #0f172a !important;
        font-size: 0.85rem !important;
        font-weight: 800 !important;
        margin: 0 0 0 0.5rem !important;
        font-family: 'Figtree', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
    }

    div:where(.swal2-container) .powernet-swal-toast .swal2-icon {
        transform: scale(0.8) !important;
        margin: 0 !important;
    }

    div:where(.swal2-container) .powernet-swal-toast .swal2-timer-progress-bar {
        background: #10b981 !important;
        height: 3px !important;
        border-radius: 0 0 1.25rem 1.25rem !important;
    }

    /* Tarjeta de Lista de Errores Moderna */
    .powernet-error-card {
        background: #fff1f2;
        border: 1px solid #ffe4e6;
        border-radius: 1.25rem;
        padding: 1rem 1.25rem;
        color: #be123c;
        font-weight: 600;
        font-size: 0.825rem;
        text-align: left;
        box-shadow: 0 2px 8px rgba(225, 29, 72, 0.04);
        margin-top: 0.4rem;
    }

    .powernet-error-item {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        margin-bottom: 0.4rem;
        line-height: 1.45;
    }

    .powernet-error-item:last-child {
        margin-bottom: 0;
    }
</style>

<script>
    (function() {
        // Base SweetAlert Mixin para Modales
        const PowerSwal = Swal.mixin({
            customClass: {
                popup: 'powernet-swal-popup',
                title: 'powernet-swal-title',
                htmlContainer: 'powernet-swal-html',
                confirmButton: 'powernet-btn-confirm',
                cancelButton: 'powernet-btn-cancel',
                icon: 'powernet-swal-icon',
                actions: 'powernet-swal-actions'
            },
            buttonsStyling: false,
            allowOutsideClick: true,
            allowEscapeKey: true
        });

        // Cierre infalible: ante cualquier click en el botón de confirmación, se cierra inmediatamente
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.powernet-btn-confirm, .swal2-confirm, .powernet-btn-cancel, .swal2-cancel');
            if (btn) {
                // Cierre nativo SweetAlert
                Swal.close();

                // Respaldo de seguridad inmediato si SweetAlert estuviera bloqueado por alguna animación
                setTimeout(() => {
                    const containers = document.querySelectorAll('.swal2-container');
                    containers.forEach(c => c.remove());
                    document.body.classList.remove('swal2-shown', 'swal2-height-auto');
                }, 120);
            }
        });

        // Toast Mixin
        const PowerToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            backdrop: false,
            customClass: {
                popup: 'powernet-swal-toast',
                title: 'swal2-title'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // Global Alert Utilities
        window.alertaToast = function(mensaje, icono = 'success') {
            return PowerToast.fire({
                icon: icono,
                title: mensaje
            });
        };

        window.alertaExito = function(mensaje, titulo = '¡Operación Exitosa!') {
            return PowerSwal.fire({
                icon: 'success',
                title: titulo,
                html: mensaje,
                confirmButtonText: 'Aceptar'
            });
        };

        window.alertaError = function(mensaje, titulo = '¡Ups! Revisa estos datos') {
            return PowerSwal.fire({
                icon: 'error',
                title: titulo,
                html: mensaje,
                confirmButtonText: 'Entendido'
            });
        };

        window.alertaAdvertencia = function(mensaje, titulo = 'Atención') {
            return PowerSwal.fire({
                icon: 'warning',
                title: titulo,
                html: mensaje,
                confirmButtonText: 'Entendido'
            });
        };

        window.alertaInfo = function(mensaje, titulo = 'Información') {
            return PowerSwal.fire({
                icon: 'info',
                title: titulo,
                html: mensaje,
                confirmButtonText: 'Entendido'
            });
        };

        window.alertaConfirmar = async function({
            titulo = '¿Estás seguro?',
            texto = 'Esta acción no se puede deshacer.',
            icono = 'warning',
            textoConfirmar = 'Sí, continuar',
            textoCancelar = 'Cancelar',
            esPeligroso = true
        } = {}) {
            const result = await PowerSwal.fire({
                title: titulo,
                html: texto,
                icon: icono,
                showCancelButton: true,
                confirmButtonText: textoConfirmar,
                cancelButtonText: textoCancelar,
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    popup: 'powernet-swal-popup',
                    title: 'powernet-swal-title',
                    htmlContainer: 'powernet-swal-html',
                    confirmButton: esPeligroso ? 'powernet-btn-confirm powernet-btn-danger' : 'powernet-btn-confirm',
                    cancelButton: 'powernet-btn-cancel',
                    icon: 'powernet-swal-icon',
                    actions: 'powernet-swal-actions'
                }
            });
            return result.isConfirmed;
        };

        // Redirigir alert() nativo a Toast
        window.alert = function(mensaje) {
            window.alertaToast(mensaje, 'info');
        };

        // Interceptar formularios confirm()
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            const onsubmitAttr = form.getAttribute('onsubmit');
            if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const match = onsubmitAttr.match(/confirm\(['"]([\s\S]*?)['"]\)/);
                const mensaje = match ? match[1] : '¿Estás seguro de realizar esta acción?';

                window.alertaConfirmar({
                    titulo: '¿Confirmar Acción?',
                    texto: mensaje,
                    icono: 'warning',
                    textoConfirmar: 'Sí, continuar',
                    textoCancelar: 'Cancelar',
                    esPeligroso: true
                }).then(confirmado => {
                    if (confirmado) {
                        form.removeAttribute('onsubmit');
                        form.submit();
                    }
                });
                return false;
            }
        }, true);

    })();
</script>

{{-- Manejador de mensajes Flash de Laravel --}}
@if(session('success') || session('Mensaje'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.alertaToast("{{ addslashes(session('success') ?? session('Mensaje')) }}", 'success');
        });
    </script>
@endif

@if(session('error') || session('Error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.alertaError("{{ addslashes(session('error') ?? session('Error')) }}", 'Error');
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.alertaAdvertencia("{{ addslashes(session('warning')) }}", 'Atención');
        });
    </script>
@endif

@if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.alertaInfo("{{ addslashes(session('info')) }}", 'Información');
        });
    </script>
@endif

@if(session('status') && !in_array(session('status'), ['verification-link-sent']))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let msg = "{{ session('status') }}";
            if (msg === 'profile-updated') msg = 'Perfil actualizado correctamente.';
            if (msg === 'password-updated') msg = 'Contraseña actualizada correctamente.';
            window.alertaToast(msg, 'success');
        });
    </script>
@endif

{{-- Manejador de Errores de Validación con Títulos Inteligentes y Cierre Instantáneo --}}
@if($errors->any())
    @php
        $tituloError = '¡Ups! Revisa estos datos';
        $erroresTraducidos = [];

        foreach ($errors->all() as $err) {
            if (str_contains($err, 'These credentials do not match our records.')) {
                $tituloError = 'No pudimos iniciar sesión';
                $erroresTraducidos[] = 'El correo electrónico o la contraseña ingresados no son correctos.';
            } elseif (str_contains($err, 'The email has already been taken.')) {
                $tituloError = 'Correo ya registrado';
                $erroresTraducidos[] = 'Este correo electrónico ya se encuentra registrado. Inicia sesión o usa otro.';
            } elseif (str_contains($err, 'confirmation does not match')) {
                $tituloError = 'Las contraseñas no coinciden';
                $erroresTraducidos[] = 'La confirmación de la contraseña no coincide con la ingresada.';
            } elseif (str_contains($err, 'must be at least 8 characters')) {
                $tituloError = 'Contraseña muy corta';
                $erroresTraducidos[] = 'La contraseña debe tener un mínimo de 8 caracteres.';
            } elseif (str_contains($err, 'The password field is required.')) {
                $erroresTraducidos[] = 'El campo de contraseña es obligatorio.';
            } elseif (str_contains($err, 'The email field is required.')) {
                $erroresTraducidos[] = 'El campo de correo electrónico es obligatorio.';
            } else {
                $erroresTraducidos[] = $err;
            }
        }
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const errores = {!! json_encode($erroresTraducidos) !!};
            const titulo = "{{ $tituloError }}";

            let htmlContent = '<div class="powernet-error-card">';
            errores.forEach(err => {
                htmlContent += `<div class="powernet-error-item">
                    <i class="fa-solid fa-circle-exclamation" style="color: #e11d48; margin-top: 3px; font-size: 13px; flex-shrink: 0;"></i>
                    <span>${err}</span>
                </div>`;
            });
            htmlContent += '</div>';

            window.alertaError(htmlContent, titulo);
        });
    </script>
@endif
