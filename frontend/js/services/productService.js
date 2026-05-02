import { api } from '../core/api.js';

export const produtoService = {

    listar(filtros = {}) {
        return api.get('/product', filtros);
    },

    buscar(id) {
        return api.get(`/product/${id}`);
    },

    buscarUuid(id) {
        return api.get(`/product/uuid/${id}`);
    },

    criar(dados) {
        return api.post('/product', dados);
    },

    atualizar(id, dados) {
        return api.post(`/product/${id}`, dados);
    },

    deletar(id) {
        return api.delete(`/product/${id}`);
    }
};

