import { api } from '../core/api.js';

export const paymentService = {
 
    criar(orderId, method) {
        return api.post('/payments', {
            order_id: orderId,
            method: method
        });
    },

    consultar(id) {
        return api.get(`/payments/${id}`);
    },

    cancelar(id) {
        return api.post(`/payments/${id}/cancel`);
    }

};