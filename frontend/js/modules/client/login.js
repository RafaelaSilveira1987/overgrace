import { customerService } from '../../services/customerService.js';
import { notify } from '../../utils/notify.js';
import { marcarErro } from '../../utils/validateUI.js';

document.getElementById('formLogin').addEventListener('submit', async (e) => {
    e.preventDefault();

    try {

        const email = document.getElementById('loginEmail');
        const senha = document.getElementById('loginSenha');

        let erro = false;

        if (!email.value.trim()) {
            marcarErro(email);
            erro = true;
        }

        if (!senha.value.trim()) {
            marcarErro(senha);
            erro = true;
        }

        if (erro) {
            throw new Error('Preencha email e senha');
        }

        const res = await customerService.login(email.value, senha.value);

        // 👉 depois você pode salvar token aqui
        localStorage.setItem('customer', JSON.stringify(res.user));

        notify.success('Login realizado!');

        // redirect
        window.location.href = '/';

    } catch (e) {
        console.error(e);
        notify.error(e.message || 'Erro ao logar');
    }
});
