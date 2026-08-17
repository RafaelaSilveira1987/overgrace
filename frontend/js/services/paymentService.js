import { api } from '../core/api.js';

export const paymentService = {
  criar(payload = {}) { return api.post('/payments', payload); },
  consultar(id) { return api.get(`/payments/${id}`); },
  atualizar(id) { return api.post(`/payments/${id}/refresh`); },
  cancelar(id) { return api.post(`/payments/${id}/cancel`); }
};
