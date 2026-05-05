import { clientService } from '../../services/clientService.js';
import { notify } from '../../utils/notify.js';
import { marcarErro } from '../../utils/validateUI.js';

document.getElementById('formRegister').addEventListener('submit', async (e) => {
    e.preventDefault();

    try {

        const campos = ['nome', 'sobrenome', 'email', 'password', 'cpf', 'tel', 'cep', 'endereco', 'numero', 'comp', 'bairro', 'cidade', 'estado'];

        let erro = false;

        campos.forEach(id => {
            const el = document.getElementById(id);
            if (!el.value.trim()) {
                marcarErro(el);
                erro = true;
            }
        });

        if (erro) throw new Error('Preencha os campos obrigatórios');

        const dados = {
            //identificação
            nome: nome.value,
            sobrenome: sobrenome.value,
            email: email.value,
            password: password.value,
            cpf: cpf.value,
            telefone: tel.value,
            //endereço de entrega
            cep: cep.value,
            endereco: endereco.value,
            numero: numero.value,
            complemento: comp.value || null,
            bairro: bairro.value,
            cidade: cidade.value,
            estado: estado.value
        };

        let auth;

        // 🔥 1. tenta login primeiro
        try { 
            auth = await clientService.login({
                email: dados.email,
                password: dados.password
            });
        } catch (e) {

            // 🔥 2. se não logou → tenta cadastrar
            await await clientService.criar(dados);

            // 🔥 3. depois loga
            auth = await clientService.login({
                email: dados.email,
                password: dados.password
            });
        }

        // 🔐 salva token
        localStorage.setItem('token_client', auth.token);

        notify.success('Continuando...');

        goToStep(2);

    } catch (e) {
        notify.error(e.message || 'Erro ao continuar');
    }
});

document.addEventListener('DOMContentLoaded', async () => {
    const token = localStorage.getItem('token_client');

    console.log(token);


    if (!token) return;

    try {

        const client = await api.get('/auth/me');

        // já logado → pula cadastro
        goToStep(2);

    } catch (e) {
        localStorage.removeItem('token_client');
    }
});



