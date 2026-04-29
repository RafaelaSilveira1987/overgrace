import { carregarProdutos } from '../../modules/stock/lista.js';
import { stockService } from '../../services/stockService.js';
import { notify } from '../../utils/notify.js';
import { marcarErro } from '../../utils/validateUI.js';
import { alertConfirm } from '../../utils/alerts.js';
import { dataUtil, valorUtil } from '../../utils/normalize.js';


let produtoEditandoId = null;
let imagens = [];


document.getElementById('formEstoque').addEventListener('submit', async (e) => {
    e.preventDefault();

    try {

        // 🔹 IDs obrigatórios
        const idsObrigatorios = [
            'f-date',
        ];

        let erro = false;
        const campos = {};

        // 🔥 valida antes de usar .value
        idsObrigatorios.forEach(id => {
            const campo = document.getElementById(id);

            if (!campo) {
                console.error('❌ Campo não encontrado:', id);
                erro = true;
                return;
            }

            if (!campo.value) {
                marcarErro(campo);
                erro = true;
            }

            campos[id] = campo;
        });

        if (erro) {
            throw new Error('Preencha todos os campos obrigatórios.');
        }
 
        // 🔹 tamanhos
        const tamanhos = [...document.querySelectorAll('#reporSizesContainer .repor-row')]
            .map(row => {
                const tamanho = row.dataset.size;

                const quantidade = row.querySelector(
                    'input[name*="[quantidade]"]'
                ).value;

                return {
                    tamanho,
                    quantidade: Number(quantidade),
                };
            });

        // 🔥 VALIDAÇÃO AQUI
        const temQuantidade = tamanhos.some(t => t.quantidade > 0);

        if (!temQuantidade) {
            throw new Error('Informe a quantidade de pelo menos um tamanho.');
        }



        // 🔹 montar dados com segurança
        const dados = {
            data: campos['f-date'].value,
            nome: document.getElementById('f-name')?.value || null,
            tamanhos: tamanhos,
            fornecedor: document.getElementById('f-fornecedor')?.value || null,
            lote: document.getElementById('f-lote')?.value || null,
            custo: document.getElementById('f-custo')?.value || null,
            obs: document.getElementById('f-obs')?.value || null,
            tipo_movimento: document.getElementById('f-tipe-movement')?.value || null,
            produto_id: document.getElementById('f-product-id').value
        };

        // 🔹 montar FormData
        const formData = new FormData();

        Object.entries(dados).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                formData.append(key, JSON.stringify(value));
            } else {
                formData.append(key, value ?? '');
            }
        });

        // 🔥 CREATE ou UPDATE
        if (produtoEditandoId) {
            await stockService.reposicao(formData);
            notify.success('Produto atualizado com sucesso!');
        } 

        // 🔹 reset
        produtoEditandoId = null;

        await carregarProdutos();
        closeRepor();

    } catch (e) {
        console.error(e);
        notify.error(e.message || e.error || 'Erro ao executar ação');
    }
});

export async function reposicao(id) {
    try {
        const produto = await stockService.buscar(id);

        produtoEditandoId = id;

        // 🔹 preencher campos
        document.getElementById('f-name').value = produto.descricao;
        document.getElementById('f-product-id').value = produtoEditandoId;

        const sizes = produto.tamanhos.map(t => t.tamanho);

        console.log(sizes);

        // 🔥 renderiza os inputs
        renderSizeInputsFromSelector(sizes);

        openRepor();

    } catch (e) {
        notify.error('Erro ao carregar produto');
        console.error(e);
    }
}

document.addEventListener('click', (e) => {
    if (e.target.closest('.btn-repor')) {
        const id = e.target.closest('.btn-repor').dataset.id;
        reposicao(id);
    }
});

