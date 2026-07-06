import { api } from '../core/api.js';

export const paymentService = {

    criarPix(orderId) {
        return api.post('/payments/pix', {
            order_id: orderId
        });
    },

    consultar(id) {
        return api.get(`/payments/${id}`);
    },

    cancelar(id) {
        return api.post(`/payments/${id}/cancel`);
    }

};