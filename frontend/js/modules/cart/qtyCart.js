import { carrinhoService } from '../../services/cartService.js';

export async function contaItensCarrinho() {
    try {
        const contagem = await carrinhoService.contaItensCarrinho();

        document.querySelectorAll('.cart-count').forEach((el) => {
            el.textContent = contagem.total;
        });
    } catch (e) {
        console.error(e);
    }
}

contaItensCarrinho();
