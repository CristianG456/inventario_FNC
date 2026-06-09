(function () {
    // === Capitalización Automática ===
    function esCampoValido(el) {
        if (!el || el.disabled || el.readOnly) return false;
        if (el.dataset.noCapitalizeFirst === 'true') return false;
        if (el.tagName === 'TEXTAREA') return true;
        if (el.tagName !== 'INPUT') return false;
        const tipo = (el.getAttribute('type') || 'text').toLowerCase();
        const excluidos = ['email', 'password', 'number', 'date', 'datetime-local', 'time', 'month', 'week', 'url', 'search', 'tel', 'hidden', 'file', 'checkbox', 'radio'];
        return tipo === 'text' && !excluidos.includes(tipo);
    }

    function capitalizarPrimeraLetra(el) {
        if (!esCampoValido(el) || !el.value) return;
        const indice = el.value.search(/[A-Za-zÁÉÍÓÚÑáéíóúñ]/u);
        if (indice < 0) return;
        const letra = el.value.charAt(indice);
        const mayuscula = letra.toLocaleUpperCase('es-CO');
        if (letra === mayuscula) return;
        el.value = el.value.slice(0, indice) + mayuscula + el.value.slice(indice + 1);
    }

    document.addEventListener('input', e => capitalizarPrimeraLetra(e.target));
    document.addEventListener('change', e => capitalizarPrimeraLetra(e.target));

    // === Alertas Globales (SweetAlert2) ===
    window.initAlerts = function(flashSuccess, flashError, flashWarning, validationErrors) {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        if (flashSuccess) Toast.fire({ icon: 'success', title: flashSuccess });
        if (flashWarning) Toast.fire({ icon: 'warning', title: flashWarning });
        if (flashError) Swal.fire({ icon: 'error', title: 'Error', text: flashError, confirmButtonColor: '#9e052b' });
        
        if (Array.isArray(validationErrors) && validationErrors.length > 0) {
            const htmlErrores = '<ul style="text-align:left; margin:0; padding-left:1rem;">'
                + validationErrors.map(msg => `<li>${msg}</li>`).join('') + '</ul>';
            Swal.fire({
                icon: 'error',
                title: 'Revise los campos',
                html: htmlErrores,
                confirmButtonColor: '#9e052b',
            });
        }
    };

    // === Confirmación de Eliminación Global ===
    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-delete-url]');
        if (!trigger) return;
        event.preventDefault();

        const url = trigger.getAttribute('data-delete-url');
        const nombre = trigger.getAttribute('data-delete-name') || 'este registro';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        Swal.fire({
            icon: 'warning',
            title: '¿Estás seguro?',
            text: 'Se eliminará ' + nombre + '. Esta acción no se puede deshacer.',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
        }).then((result) => {
            if (result.isConfirmed && url) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.innerHTML = `
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
})();
