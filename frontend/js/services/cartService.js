import { api } from '../core/api.js';

export const carrinhoService = {

    get() {
        return api.get('/cart/item');
    },

    contaItensCarrinho() {
        return api.get('/cart/count');
    },

    adicionar(produto_id, tamanho, quantidade = 1) {
        return api.post('/cart', {
            produto_id,
            tamanho,
            quantidade
        });
    },

    atualizar(id, quantidade) {
        return api.post(`/cart/alter/${id}`, {quantidade: quantidade});
    },

    remover(item_id) {
        return api.delete(`/cart/${item_id}`);
    }
};
