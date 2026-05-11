import { clientService } from '../../services/clientService.js';
import { orderService } from '../../services/orderService.js';
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
            await clientService.criar(dados);

            // 🔥 3. depois loga
            auth = await clientService.login({
                email: dados.email,
                password: dados.password
            });

            notify.error(e.error || e.message);

        }

        // 🔐 salva token
        localStorage.setItem('token_client', auth.token);
        localStorage.setItem('refresh_token_client', auth.refresh_token);
        localStorage.setItem('role', auth.role);

        console.log(auth);

        notify.success('Continuando...');

        goToStep(2);

    } catch (e) {
        notify.error(e.message || 'Erro ao continuar');
    }
});

document.getElementById('formShipping').addEventListener('submit', async (e) => {

    e.preventDefault();

    try {

        const btn = e.target.querySelector('.submit-btn');

        btn.disabled = true;
        btn.innerHTML = 'Continuando...';

        // FRETE
        const shippingSelecionado = document.querySelector('input[name="ship"]:checked');

        const shipOption = shippingSelecionado.closest('.ship-option');

        const tipo = shippingSelecionado.value;

        const valorTexto = shipOption
            .querySelector('.ship-price')
            .innerText;

        const valor = valorTexto.includes('Grátis')
            ? 0
            : parseFloat(
                valorTexto
                    .replace('R$', '')
                    .replace('.', '')
                    .replace(',', '.')
                    .trim()
            );

        // LOGIN
        let auth;

        try {

            auth = await clientService.login({
                email: email.value,
                password: password.value
            });

        } catch (e) {

            throw new Error('E-mail ou senha inválidos');
        }

        // TOKEN
        localStorage.setItem('token_client', auth.token);
        localStorage.setItem('refresh_token_client', auth.refresh_token);
        localStorage.setItem('role', auth.role);

        // PEDIDO
        const order = await orderService.criar({
            shipping_tipo: tipo,
            shipping_valor: valor
        });

        console.log(order);

        localStorage.setItem('order_id', order.order_id.order_id);

        notify.success('Pedido criado com sucesso');

        goToStep(3);

    } catch (e) {

        console.error(e);

        notify.error(e.message || 'Erro ao continuar');

    } finally {

        const btn = document.querySelector('.submit-btn');

        btn.disabled = false;

        btn.innerHTML = `
            Continuar para pagamento
            <span class="arrow">→</span>
        `;
    }
});

document.addEventListener('DOMContentLoaded', async () => {

    const token = localStorage.getItem('token_client');

    const orderId = localStorage.getItem('order_id');
    console.log('pedido: ', orderId);

    if (!token) {
        return;
    }

    try {

        // valida cliente
        const client = await clientService.me();

        console.log(client);

        // possui pedido salvo
        if (orderId) {

            try {

                const order = await orderService.get(orderId);

                console.log('order: ', order);

                // pedido válido
                if (order.status === 'pending') {

                    goToStep(3, true);

                    return;
                }

                // pedido já pago/cancelado/etc
                localStorage.removeItem('order_id');

            } catch (e) {

                console.error(e);

                // pedido não existe
                //localStorage.removeItem('order_id');
            }
        }

        // segue checkout normal
        goToStep(2, true);

    } catch (e) {

        console.error(e);

        localStorage.removeItem('token_client');
        localStorage.removeItem('refresh_token_client');
        //localStorage.removeItem('order_id');
    }
});





