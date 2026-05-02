import { carrinhoService } from '../../services/cartService.js';
import { produtoService } from '../../services/productService.js';
import { contaItensCarrinho } from '../../modules/cart/qtyCart.js';
import { notify } from '../../utils/notify.js';
import { marcarErro } from '../../utils/validateUI.js';
import { alertConfirm } from '../../utils/alerts.js';
import { dataUtil, valorUtil } from '../../utils/normalize.js';


let produtoEditandoId = null;

document.getElementById('formProd').addEventListener('submit', async (e) => {
    e.preventDefault();

    try {
        const selected = document.querySelector('#sizes .size-btn.active');
        const tamanho = selected ? selected.textContent : null;
        if (!tamanho) {
            notify.error('Selecione pelo menos um tamanho.');
            return;
        }

        const produto = document.getElementById('f-id').value;
        const quantidade = parseInt(document.getElementById('f-qtd')?.value, 10) || 1;

        await carrinhoService.adicionar(produto, tamanho, quantidade);

        contaItensCarrinho();
        notify.success('Produto adicionado ao carrinho!');
    } catch (e) {
        console.error(e);
        notify.error(e.message || 'Erro ao adicionar ao carrinho');
    }
});



function formatar(valor) {
    if (!valor) return '';

    const num = Number(valor);
    if (isNaN(num)) return '';

    return num.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}


export async function carregarProduto() {
    try {
        const params = new URLSearchParams(window.location.search);
        const uuid = params.get('id');

        const produto = await produtoService.buscarUuid(uuid);

        // 🔹 preencher campos
        document.getElementById('f-id').value = produto.id;
        document.getElementById('f-name').textContent = produto.descricao;

        document.getElementById('f-price').textContent = formatar(produto.preco_atual);
        const elPrecoAntigo = document.getElementById('f-price-old');

        if (!produto.preco_antigo || Number(produto.preco_antigo) <= 0) {
            elPrecoAntigo.remove();
        } else {
            elPrecoAntigo.textContent = 'R$' + formatar(produto.preco_antigo);
        }


        const sizesHtml = produto.tamanhos.map(t => {
            return `<button type="button" class="size-btn">${t.tamanho}</button>`;
        }).join('');
        document.getElementById('sizes').innerHTML = sizesHtml;

        document.getElementById('f-desc').textContent = produto.descricao_completa;

        const BASE_IMG = '/overgrace/frontend/uploads/products/';
        const imagens = produto.imagens || [];

        // 🔹 pega imagem destaque (ou primeira)
        const destaque = imagens.find(img => img.destaque == 1) || imagens[0];

        // 🔹 define imagem principal
        document.getElementById('mainProductImage').src = BASE_IMG + destaque.nome;

        // 🔹 monta thumbs
        const thumbsHtml = imagens.map(img => {
            const src = BASE_IMG + img.nome;

            return `
        <img src="${src}" onclick="trocarImagem(this)" />
    `;
        }).join('');

        document.querySelector('.thumb-list').innerHTML = thumbsHtml;



    } catch (e) {
        notify.error('Erro ao carregar produto');
        console.error(e);
    }
}

carregarProduto();

function trocarImagem(el) {
    document.getElementById('mainProductImage').src = el.src;
}





