import { api } from '../core/api.js';

export const couponService = {

    listar(filtros = {}) {
        return api.get('/coupon', filtros);
    },

    buscar(id) {
        return api.get(`/coupon/${id}`);
    },

    criar(dados) {
        return api.post('/coupon', dados);
    },

    atualizar(id, dados) {
        return api.post(`/coupon/${id}`, dados);
    },

    deletar(id) {
        return api.delete(`/coupon/${id}`);
    }
};
