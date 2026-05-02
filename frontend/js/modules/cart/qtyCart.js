import { carrinhoService } from '../../services/cartService.js';

export async function contaItensCarrinho() {
    try {
        const contagem = await carrinhoService.contaItensCarrinho();
        // 🔹 preencher campos
        document.getElementById('cartCount').textContent = contagem.total;
    } catch (e) {
        console.error(e);
    }
}

contaItensCarrinho();







