import { api } from '../core/api.js';

export const orderService = {
  criar(dados = {}) {
    return api.post('/orders', dados);
  },

  listar() {
    return api.get('/pedidos');
  },

  get(id) {
    return api.get(`/orders/${id}`);
  },
};
