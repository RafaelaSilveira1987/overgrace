import { api } from '../core/api.js';

export const orderService = {

    criar() {
        return api.post('/orders', {});
    },

    listar() {
        return api.get('/pedidos');
    },

    get(id) {
        return api.get(`/orders/${id}`);
    },
};
