import { clientService } from '../../services/clientService.js';
import { notify } from '../../utils/notify.js';
import { marcarErro } from '../../utils/validateUI.js';

document.getElementById('formRegister').addEventListener('submit', async (e) => {
    e.preventDefault();

    try {

        const camposObrigatorios = [
            'nome',
            'sobrenome',
            'email',
            'password',
            'cpf',
            'tel'
        ];

        let erro = false;

        camposObrigatorios.forEach(id => {
            const el = document.getElementById(id);

            if (!el.value.trim()) {
                marcarErro(el);
                erro = true;
            }
        });

        const senha = document.getElementById('password').value;

        // 🔐 validação básica frontend
        if (senha.length < 8) {
            marcarErro(document.getElementById('password'));
            throw new Error('Senha deve ter no mínimo 8 caracteres');
        }

        const dados = {
            nome: document.getElementById('nome').value,
            sobrenome: document.getElementById('sobrenome').value,
            email: document.getElementById('email').value,
            senha: senha,
            cpf: document.getElementById('cpf').value || null,
            telefone: document.getElementById('tel').value || null
        };

        if (erro) {
            throw new Error('Preencha os campos obrigatórios');
        }

        const submit = await clientService.criar(dados);

        if (submit.token) {

            localStorage.setItem('token_client', submit.token);

            console.log(localStorage.getItem('token_client'));

            notify.success('Cadastro realizado com sucesso!');
            goToStep(2);

        } else {
            notify.error('Erro ao cadastrar');
        }


        document.getElementById('formRegister').reset();

    } catch (e) {
        console.error(e);
        notify.error(e.message || e.error || 'Erro ao cadastrar');
    }
});


document.getElementById('formAddress').addEventListener('submit', async (e) => {
    e.preventDefault();

    try {

        const camposObrigatorios = [
            'cep',
            'endereco',
            'numero',
            'comp',
            'bairro',
            'cidade',
            'estado',
        ];

        let erro = false;

        camposObrigatorios.forEach(id => {
            const el = document.getElementById(id);

            if (!el.value.trim()) {
                marcarErro(el);
                erro = true;
            }
        });

        const senha = document.getElementById('password').value;

        // 🔐 validação básica frontend
        if (senha.length < 8) {
            marcarErro(document.getElementById('password'));
            throw new Error('Senha deve ter no mínimo 8 caracteres');
        }

        const dados = {
            nome: document.getElementById('nome').value,
            sobrenome: document.getElementById('sobrenome').value,
            email: document.getElementById('email').value,
            senha: senha,
            cpf: document.getElementById('cpf').value || null,
            telefone: document.getElementById('tel').value || null
        };

        if (erro) {
            throw new Error('Preencha os campos obrigatórios');
        }

        const submit = await clientService.criar(dados);
        if (submit.success) {
            notify.success('Cadastro realizado com sucesso!');
            goToStep(2);
        } else {
            notify.error(e.message || e.error || e.mensagem || 'Erro ao cadastrar');
        }

        document.getElementById('formRegister').reset();

    } catch (e) {
        console.error(e);
        notify.error(e.message || e.error || 'Erro ao cadastrar');
    }
});
