import { couponService } from '../../services/couponService.js';
import { notify } from '../../utils/notify.js';
import { alertConfirm } from '../../utils/alerts.js';
import { marcarErro } from '../../utils/validateUI.js';
import { dataUtil, valorUtil } from '../../utils/normalize.js';
//import { carregarCupons } from './lista.js';

let cupomEditandoId = null;

// 🔹 SUBMIT
document.getElementById('formCoupon').addEventListener('submit', async (e) => {
    e.preventDefault();


    try {
        // 🔹 IDs obrigatórios
        const idsObrigatorios = [
            'couponCode',
            'couponType',
            'couponValue',
            'couponDate',
            'couponStatus',
        ];

        let erro = false;
        const campos = {};

        idsObrigatorios.forEach(id => {
            const campo = document.getElementById(id);

            if (!campo) {
                console.error('❌ Campo não encontrado:', id);
                erro = true;
                return;
            }

            if (!campo.value || !campo.value.trim()) {
                marcarErro(campo);
                erro = true;
            }

            campos[id] = campo;
        });


        const dados = {
            cupom: document.getElementById('couponCode').value,
            tipo: document.getElementById('couponType').value,
            valor: document.getElementById('couponValue').value || null,
            minimo: document.getElementById('couponMin').value || null,
            limite: document.getElementById('couponLimit').value || null,
            validade: document.getElementById('couponDate').value || null,
            status: document.getElementById('couponStatus').value
        };

        console.log('dados:', dados);

        if (erro) {
            throw new Error('Preencha todos os campos obrigatórios.');
        }

        if (cupomEditandoId) {
            await couponService.atualizar(cupomEditandoId, dados);
            notify.success('Cupom atualizado!');
        } else {
            await couponService.criar(dados);
            notify.success('Cupom criado!');
        }

        document.getElementById('formCoupon').reset();
        cupomEditandoId = null;

        //carregarCupons();
        closeCoupon();

    } catch (e) {
        console.error(e);
        notify.error(e.message || e.error || 'Erro ao executar ação');
    }
});


// 🔹 EDITAR
export async function editarCupom(id) {
    try {
        const c = await couponService.buscar(id);

        cupomEditandoId = id;

        document.getElementById('f-cupom').value = c.cupom;
        document.getElementById('f-tipo').value = c.tipo;
        document.getElementById('f-percentual').value = c.percentual || '';
        document.getElementById('f-valor').value = c.valor || '';
        document.getElementById('f-minimo').value = c.minimo || '';
        document.getElementById('f-limite').value = c.limite || '';
        document.getElementById('f-validade').value = c.validade || '';
        document.getElementById('f-status').value = c.status;

        openModal();

    } catch (e) {
        notify.error('Erro ao carregar cupom');
    }
}


// 🔹 DELETE
export async function removerCupom(id) {

    const confirm = await alertConfirm(
        "Deseja excluir este cupom?",
        "confirm",
        "Excluir cupom"
    );

    if (!confirm) return;

    try {

        await couponService.deletar(id);

        notify.success('Cupom removido!');

        carregarCupons();

    } catch (e) {
        notify.error('Erro ao remover cupom');
    }
}
