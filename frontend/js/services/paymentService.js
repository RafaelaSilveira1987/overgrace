import { api } from '../core/api.js';

export const paymentService = {

    criar({ order_id, client_id, method }) {
        return api.post('/payments', {
            order_id,
            client_id,
            method 
        });
    }, 

    consultar(id) {
        return api.get(`/payments/${id}`);
    },

    cancelar(id) {
        return api.post(`/payments/${id}/cancel`);
    }

};