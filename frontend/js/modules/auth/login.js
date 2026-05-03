import { authService } from '../../services/authService.js?v=6';
import { notify } from '../../utils/notify.js';

function getLoginMode() {
    const params = new URLSearchParams(window.location.search);

    if (window.LOGIN_MODE === 'admin' || params.get('mode') === 'admin') {
        return 'admin';
    }

    return window.location.pathname.endsWith('/admin-login') ? 'admin' : 'client';
}
 
document.getElementById('formLogin').addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const senha = document.getElementById('password').value;
    const mode = getLoginMode();

    try {

        const response = mode === 'admin'
            ? await authService.loginAdmin(email, senha)
            : await authService.login(email, senha);

        localStorage.removeItem('token');
        localStorage.removeItem('role');

        //salva token
        localStorage.setItem('token', response.token);
        localStorage.setItem('role', response.role);

        notify.success('Login realizado com sucesso');

        setTimeout(() => {
            window.location.href = response.role === 'admin'
                ? '/overgrace/dashboard'
                : '/overgrace/';
        }, 500);


    } catch (err) {
        notify.error(err.message);
    }
});
