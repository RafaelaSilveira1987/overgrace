/*

uso sem delay:

setLoading(el, true);
setLoading(el, false);

uso com delay:

setLoading(el, false, { delay: 400 });


*/

export function setLoading(target, show = true, options = {}) {
    let el;

    if (typeof target === 'string') {
        el = document.getElementById(target);
    } else {
        el = target;
    }

    if (!el) return;

    let overlay = el.querySelector('.ui-loading-overlay');

    // cria automaticamente se não existir
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'ui-loading-overlay';

        overlay.innerHTML = `<div class="ui-spinner"></div>`;

        el.classList.add('ui-loading-container');
        el.appendChild(overlay);
    }

    // MOSTRAR
    if (show) {
        overlay.classList.remove('hidden');
        overlay.dataset.start = Date.now(); // guarda início
        return;
    }

    // ESCONDER
    const delay = options.delay || 0;

    if (!delay) {
        overlay.classList.add('hidden');
        return;
    }

    const start = parseInt(overlay.dataset.start || 0);
    const elapsed = Date.now() - start;

    const remaining = delay - elapsed;

    if (remaining > 0) {
        setTimeout(() => {
            overlay.classList.add('hidden');
        }, remaining);
    } else {
        overlay.classList.add('hidden');
    }
}

