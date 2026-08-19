{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Fondo para Modales Grandes (Confirmaciones / Errores) */
    div:where(.swal2-container).swal2-backdrop-show:not(.swal2-top-end):not(.swal2-top-start):not(.swal2-bottom-end):not(.swal2-bottom-start) {
        background: rgba(15, 23, 42, 0.25) !important;
        backdrop-filter: blur(2px) !important;
        -webkit-backdrop-filter: blur(2px) !important;
    }

    /* PARA TOASTS: Totalmente transparente, CERO fondo negro/oscuro, la pantalla queda 100% visible */
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

    /* Modal emergente principal (Confirmaciones / Errores grandes) */
    div:where(.swal2-container) div:where(.swal2-popup).powernet-swal-popup {
        border-radius: 1.75rem !important;
        padding: 2rem 1.75rem 1.75rem 1.75rem !important;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        background: #ffffff !important;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.2), 0 0 0 1px rgba(226, 232, 240, 0.9) !important;
        border: none !important;
        max-width: 26rem !important;
    }

    div:where(.swal2-container) .powernet-swal-title {
        font-size: 1.2rem !important;
        font-weight: 900 !important;
        color: #0f172a !important;
        letter-spacing: -0.025em !important;
        margin-top: 0.75rem !important;
        margin-bottom: 0.35rem !important;
        padding: 0 !important;
    }

    div:where(.swal2-container) .powernet-swal-html {
        font-size: 0.85rem !important;
        font-weight: 500 !important;
        color: #64748b !important;
        line-height: 1.5 !important;
        margin: 0.25rem 0 1.25rem 0 !important;
        padding: 0 !important;
    }

    div:where(.swal2-container) .powernet-swal-icon {
        margin: 0 auto 0.5rem auto !important;
        border-width: 3px !important;
        width: 3.75rem !important;
        height: 3.75rem !important;
        transform: scale(0.95) !important;
    }

    div:where(.swal2-container) .powernet-swal-actions {
        gap: 0.65rem !important;
        width: 100% !important;
        margin-top: 1rem !important;
        display: flex !important;
        justify-content: center !important;
    }

    div:where(.swal2-container) button:where(.swal2-styled).powernet-btn-confirm {
        background-color: #0f172a !important;
        color: #ffffff !important;
        font-size: 0.8125rem !important;
        font-weight: 800 !important;
        padding: 0.75rem 1.4rem !important;
        border-radius: 1rem !important;
        border: none !important;
        box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.15) !important;
        transition: all 0.18s ease-in-out !important;
        cursor: pointer !important;
        outline: none !important;
    }

    div:where(.swal2-container) button:where(.swal2-styled).powernet-btn-confirm:hover {
        background-color: #000000 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2) !important;
    }

    div:where(.swal2-container) button:where(.swal2-styled).powernet-btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3) !important;
    }

    div:where(.swal2-container) button:where(.swal2-styled).powernet-btn-danger:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 8px 16px rgba(239, 68, 68, 0.4) !important;
    }

    div:where(.swal2-container) button:where(.swal2-styled).powernet-btn-cancel {
        background-color: #f8fafc !important;
        color: #475569 !important;
        font-size: 0.8125rem !important;
        font-weight: 700 !important;
        padding: 0.75rem 1.25rem !important;
        border-radius: 1rem !important;
        border: 1px solid #e2e8f0 !important;
        transition: all 0.18s ease-in-out !important;
        cursor: pointer !important;
        outline: none !important;
    }

    div:where(.swal2-container) button:where(.swal2-styled).powernet-btn-cancel:hover {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
    }

    /* Notificaciones Toast Modernas y Claras (Fondo blanco, sombra suave y bordes redondeados) */
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
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
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
            allowOutsideClick: true
        });

        // Toast Mixin: backdrop: false (CERO fondo negro, la página queda completamente normal)
        const PowerToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
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

        window.alertaError = function(mensaje, titulo = 'Ha ocurrido un error') {
            return PowerSwal.fire({
                icon: 'error',
                title: titulo,
                html: mensaje,
                confirmButtonText: 'Entendido'
            });
        };

        window.alertaAdvertencia = function(mensaje, titulo = 'Advertencia') {
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

        // Redirigir el window.alert nativo a Toast
        window.alert = function(mensaje) {
            window.alertaToast(mensaje, 'info');
        };

        // Interceptar automáticamente todos los formularios con onsubmit="return confirm(...)"
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
            window.alertaAdvertencia("{{ addslashes(session('warning')) }}", 'Advertencia');
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

@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.alertaError("{!! addslashes(implode('<br>• ', $errors->all())) !!}", 'Por favor corrige los errores');
        });
    </script>
@endif
