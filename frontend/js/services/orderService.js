import { api } from '../core/api.js';

export const pedidoService = {

    criar() {
        return api.post('/pedidos', {});
    },

    listar() {
        return api.get('/pedidos');
    }
};
