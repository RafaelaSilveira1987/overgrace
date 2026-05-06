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

export async function carregarCarrinho() {
    try {
        // chama endpoint da api para carregar os dados com base nos cookies
        const cart = await carrinhoService.get();

        // base imagem
        const BASE_IMG = '/overgrace/frontend/uploads/products/';

        // preencher campos
        document.getElementById('total-items').textContent = 'R$ ' + formatar(cart.total);
        document.getElementById('total-descontos').textContent = 'R$ ' + formatar(cart.coupon);
        document.getElementById('total-items-final').textContent = 'R$ ' + formatar(cart.sub_total);

        //preenche itens do carrinho
        if (cart.items.length > 0) {
            const items = cart.items.map(t => {
                return `
                        <div class="order-item">
                            <div class="order-thumb-wrap">
                                <img
                                class="order-thumb"
                                src="${BASE_IMG + t.imagem}"
                                alt="${t.descricao}" />
                                <span class="order-qty-badge">${t.quantity ?? 1}</span>
                            </div>
                            <div style="flex: 1">
                                <p class="order-item-name">${t.descricao}</p>
                                <p class="order-item-variant">Tam · ${t.size}</p>
                            </div>
                            <p class="order-item-price">R$ ${formatar(t.subtotal)}</p>
                        </div>
                         
                         `;

            }).join('');
            document.getElementById('order-items').innerHTML = items;
        }

        // contagem de itens topo tela
        //document.getElementById('itemCount').textContent = cart.items.length + ' itens selecionados';

    } catch (e) {
        notify.error('Erro ao carregar produto');
        console.error(e);
    }
}


carregarCarrinho();






