import { authService } from '../../services/authService.js?v=11';
import { notify } from '../../utils/notify.js';

function getLoginMode() {
    const params = new URLSearchParams(window.location.search);

    if (
        window.LOGIN_MODE === 'admin' ||
        params.get('mode') === 'admin'
    ) {
        return 'admin';
    }

    return window.location.pathname.endsWith('/admin-login')
        ? 'admin'
        : 'client';
}

function appUrl(path = '/') {
    const base = window.APP_BASE_PATH || '';
    return `${base}${path}` || '/';
}

function redirectAfterLogin(role) {
    if (role === 'admin') {
        window.location.href = appUrl('/dashboard');
        return;
    }

    // Cliente volta para a loja
    window.location.href = appUrl('/');
}

const form = document.getElementById('formLogin');
const submitButton = form?.querySelector('.login-btn');

form?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const email = document
        .getElementById('email')
        ?.value
        .trim();

    const password = document
        .getElementById('password')
        ?.value;

    const mode = getLoginMode();

    if (!email || !password) {
        notify.error('Informe e-mail e senha');
        return;
    }

    try {
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Entrando...';
        }

        let response;

        if (mode === 'admin') {
            response = await authService.loginAdmin(
                email,
                password
            );
        } else {
            response = await authService.login(
                email,
                password
            ); 
        }

        // Usa a role retornada pela API.
        // Se não vier, usa o modo da página como fallback.
        const role = response?.role || mode;

        notify.success('Login realizado com sucesso');

        setTimeout(() => {
            redirectAfterLogin(role);
        }, 350);
    } catch (error) {
        console.error('[LOGIN ERROR]', error);

        notify.error(
            error?.message ||
            'Não foi possível entrar'
        );
    } finally {
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = 'Entrar';
        }
    }
});
