import { authService } from '../../services/authService.js';
import { notify } from '../../utils/notify.js';
 
document.getElementById('formLogin').addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const senha = document.getElementById('password').value;

    try {

        const response = await authService.login(email, senha);

        //salva token
        localStorage.setItem('token', response.token);

        notify.success('Login realizado com sucesso');

        setTimeout(() => {
            window.location.href = '/overgrace/dashboard';
        }, 500);


    } catch (err) {
        notify.error(err.message);
    }
});
