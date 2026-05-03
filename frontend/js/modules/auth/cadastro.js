import { registerClient } from '../../services/authService.js?v=3';
import { notify } from '../../utils/notify.js';

const form = document.getElementById('formCadastro');
const submitButton = form.querySelector('.submit-btn');

function onlyNumbers(value) {
    return value.replace(/\D/g, '');
}

function setLoading(isLoading) {
    submitButton.disabled = isLoading;
    submitButton.textContent = isLoading ? 'Cadastrando...' : 'Cadastrar';
}

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const data = {
        nome: document.getElementById('nome').value.trim(),
        sobrenome: document.getElementById('sobrenome').value.trim(),
        email: document.getElementById('email').value.trim(),
        password: document.getElementById('senha').value,
        cpf: onlyNumbers(document.getElementById('cpf').value),
        telefone: onlyNumbers(document.getElementById('telefone').value)
    };

    try {
        setLoading(true);
        await registerClient(data);
        notify.success('Cadastro realizado com sucesso');

        setTimeout(() => {
            window.location.href = '/overgrace/login';
        }, 600);
    } catch (err) {
        notify.error(err.message || 'Nao foi possivel realizar o cadastro');
    } finally {
        setLoading(false);
    }
});
