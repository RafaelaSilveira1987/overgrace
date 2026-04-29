import { api } from '../core/api.js';
import { reposicao } from '../modules/stock/form.js';

export const stockService = {

    listar(filtros = {}) {
        return api.get('/stock', filtros);
    },

    buscar(id) {
        return api.get(`/stock/${id}`);
    },

    reposicao(dados) {
        return api.post('/stock', dados);
    },

    atualizar(id, dados) {
        return api.post(`/stock/${id}`, dados);
    },
    
    deletar(id) {
        return api.delete(`/stock/${id}`);
    }
};

