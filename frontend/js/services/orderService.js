import { api } from '../core/api.js';

export const orderService = {
  criar(dados = {}) {
    return api.post('/orders', dados);
  },

  listar(filtros = {}) {
    return api.get('/orders-list', filtros);
  },

  listarDash(filtros = {}) {
    return api.get('/orders-dash', filtros);
  },

  get(id) {
    return api.get(`/orders/${id}`);
  },
};
