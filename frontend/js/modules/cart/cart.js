import { carrinhoService } from '../../services/cartService.js';
import { notify } from '../../utils/notify.js';
import { marcarErro } from '../../utils/validateUI.js';
import { alertConfirm } from '../../utils/alerts.js';
import { dataUtil, valorUtil } from '../../utils/normalize.js';


let produtoEditandoId = null;

function formatar(valor) {
    if (!valor) return '';

    const num = Number(valor);
    if (isNaN(num)) return '';

    return num.toLocaleString('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

document.getElementById('formCart').addEventListener('submit', async (e) => {
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

export async function carregarCarrinho() {
    try {
        const cart = await carrinhoService.get();

        // 🔹 preencher campos
        document.getElementById('sub-total-items').textContent = 'R$ ' + cart.total;
        document.getElementById('total-items').textContent = 'R$ ' + cart.total;
        const BASE_IMG = '/overgrace/frontend/uploads/products/';

        if (cart.items.length > 0) {
            const items = cart.items.map(t => {
                return `
                            <div class="cart-item" id="item-${t.id}">
                            <div class="item-product">
                                <img
                                class="item-thumb"
                                src="${BASE_IMG + t.imagem}"
                                alt="${t.descricao}" />
                                <div class="item-details">
                                <p class="item-name">${t.descricao}</p>
                                <p class="item-meta">
                                    <span>Tamanho: ${t.size}</span>
                                </p>
                                <button type="button" class="item-remove btn-deletar" data-id="${t.id}">
                                    Remover
                                </button>
                                </div>
                            </div>
                            <div>
                                <div class="qty-control">
                                <button type="button" class="qty-btn btn-atualizar" data-id="${t.id}" data-action="minus">
                                    −
                                </button>
                                <span class="qty-value" id="qty-${t.id}">${t.quantity ?? 1}</span>
                                <button type="button" class="qty-btn btn-atualizar" data-id="${t.id}" data-action="plus">
                                    +
                                </button>
                                </div>
                            </div>
                            <div class="item-subtotal" id="sub-${t.id}">R$ ${formatar(t.subtotal)}</div>
                            <button
                                type="button"
                                class="item-delete btn-deletar"
                                data-id="${t.id}"
                                title="Remover">
                                ×
                            </button>
                            </div>
                        
                        `;

            }).join('');
            document.getElementById('list-items').innerHTML = items;
        }

        document.getElementById('itemCount').textContent = cart.items.length + ' itens selecionados';

    } catch (e) {
        notify.error('Erro ao carregar produto');
        console.error(e);
    }
}

export async function removerItemCarrinho(id) {
    try {
        await carrinhoService.remover(id);

        notify.success('Produto removido do carrinho!');

        // atualizar grid
        carregarCarrinho();

    } catch (e) {
        notify.error('Algo deu errado na exclusão do item no carrinho!');
    }
}

export async function atualizaItemCarrinho(id, quantity) {
    try {

        await carrinhoService.atualizar(id, quantity);

        notify.success('Produto atualizado com sucesso!');

        // atualizar grid
        carregarCarrinho();

    } catch (e) {
        notify.error('Algo deu errado!');
    }
}

document.addEventListener('click', function (e) {
    if (e.target.matches('.btn-deletar') || e.target.closest('.btn-deletar')) {

        const btn = e.target.closest('.btn-deletar');

        const id = btn.getAttribute('data-id');

        console.log('ID FINAL:', id);

        removerItemCarrinho(id);
    }
});

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-atualizar');
    if (!btn) return;

    const id = btn.dataset.id;
    const action = btn.dataset.action;

    const qtyElement = document.getElementById(`qty-${id}`);
    let currentQty = parseInt(qtyElement.textContent);

    if (action === 'plus') {
        currentQty++;
    }

    if (action === 'minus') {
        currentQty--;
    }

    // evita quantidade inválida
    if (currentQty < 1) return;

    qtyElement.textContent = currentQty;


    console.log('ID:', id);
    console.log('Nova quantidade:', currentQty);

    atualizaItemCarrinho(id, currentQty);
});


carregarCarrinho();






