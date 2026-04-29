import { carrinhoService } from '../../services/carrinhoService.js';

export async function adicionarAoCarrinho(produtoId) {
    try {
        await carrinhoService.adicionar(produtoId, 1);
        alert('Adicionado ao carrinho');
    } catch (e) {
        console.error(e);
    }
}

async function carregarCarrinho() {
    try {
        const res = await carrinhoService.get();

        const container = document.getElementById('carrinho');
        container.innerHTML = '';

        res.data.forEach(item => {
            container.innerHTML += `
                <div>
                    ${item.nome} - ${item.quantidade}
                </div>
            `;
        });

    } catch (e) {
        console.error(e);
    }
}

carregarCarrinho();
