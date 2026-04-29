import { api } from '../core/api.js';

export const carrinhoService = {

    get() {
        return api.get('/carrinho');
    },

    adicionar(produto_id, quantidade = 1) {
        return api.post('/carrinho', {
            produto_id,
            quantidade
        });
    },

    atualizar(item_id, quantidade) {
        return api.put(`/carrinho/${item_id}`, { quantidade });
    },

    remover(item_id) {
        return api.delete(`/carrinho/${item_id}`);
    }
};
